<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Generic\Apache;

use Moosh\MooshCommand;
use Moosh\ApacheLogParser\Parser;

/**
 *
 * @package Moosh\Command\Generic\Apache
 */
class NginxParseExtendedLog extends MooshCommand {
    public static $MAX_LINE_LENGTH = 8192;

    public function __construct() {
        parent::__construct('parse-extendedlog', 'nginx');

        $this->addArgument('logfile');
        $this->addOption('s|sql', 'output data as SQL');
        $this->addOption('t|table:', 'table name', 'apachelog');
        $this->addOption('c|csv', 'output data as CSV');

    }

    public function bootstrapLevel() {
        return self::$BOOTSTRAP_NONE;
    }

    public function execute() {
        $logfile = $this->arguments[0];

        if (!is_file($logfile) || !is_readable($logfile)) {
            cli_error("File '$logfile' does not exist or not readable.");
        }

        if ($this->options['sql'] && $this->options['csv']) {
            cli_error("Choose only one export at the time - SQL or CSV");
        }
        require_once($this->mooshDir . '/includes/ApacheLogParser/Parser.class.php');

        /*
         91.166.19.215 - - [18/Nov/2020:06:25:16 +0000] "GET /favicon.ico HTTP/1.1" 404 47
        "https://example.com/pluginfile.php/1441543/mod_resource/content/1/abc.pdf"
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.1 Safari/605.1.15"
        request_time=0.000 upstream_response_time=0.000 upstream_connect_time=0.000 upstream_header_time=0.000 moodle_user=- 

         */
        $elements = array(
                'remote_ip',
                'time',
                'request_first_line',
                'status',
                'bytes_sent',
                array('element' => 'request_header_line', 'name' => 'referer'),
                array('element' => 'request_header_line', 'name' => 'user_agent'),
                array('element' => 'note', 'name' => 'request_time'),
                array('element' => 'note', 'name' => 'upstream_response_time'),
                array('element' => 'note', 'name' => 'upstream_connect_time'),
                array('element' => 'note', 'name' => 'upstream_header_time'),
                array('element' => 'note', 'name' => 'moodle_user'),
        );
        $header = [];
        foreach ($elements as $element) {
            if (is_string($element)) {
                $header[] = $element;
            }
            if (is_array($element)) {
                $header[] = $element['name'];
            }
        }

        $re =
                '%s - - %s "%s" %s %s "%s" "%s" request_time=%s upstream_response_time=%s upstream_connect_time=%s upstream_header_time=%s moodle_user=%s';
        // $parser = Parser::createFormat(Parser::$FORMAT_COMBINED);
        $parser = new Parser($re, $elements);
        $parser->setFile($logfile);

        if ($this->expandedOptions['csv']) {
            $csv = fopen("php://output", 'w');
            fputcsv($csv, $header);
            while (($line = $parser->next()) != -1) {
                fputcsv($csv, $line);
                if (!count($line)) {
                    echo "nothing on the line";
                    die();
                }
            }
            fclose($csv);
        }

        if ($this->expandedOptions['sql']) {
            $sqlline = "CREATE TABLE IF NOT EXISTS " . $this->expandedOptions['table'] . " ( ";
            foreach ($header as $h) {
                $sqlline .= " $h VARCHAR(256) NULL,";
            }
            $sqlline = trim($sqlline, ',');
            $sqlline .= ');';
            echo $sqlline;
            echo "\n";

            while (($line = $parser->next()) != -1) {
                $sqlline = "INSERT IGNORE INTO " . $this->expandedOptions['table'] . " SET ";
                foreach ($line as $k => $v) {
                    $sqlline .= "$k = '$v',";
                }
                $sqlline = trim($sqlline, ',');
                echo $sqlline . ";\n";
            }

        }
    }
}
