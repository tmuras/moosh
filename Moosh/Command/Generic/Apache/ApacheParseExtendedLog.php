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
class ApacheParseExtendedLog extends MooshCommand
{
    public static $MAX_LINE_LENGTH = 8192;

    public function __construct()
    {
        parent::__construct('parse-extendedlog', 'apache');

        $this->addArgument('logfile');
        $this->addOption('t|table', 'table name', 'apachelog');

    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_NONE;
    }

    public function execute()
    {
        $logfile = $this->arguments[0];

        if (!is_file($logfile) || !is_readable($logfile)) {
            cli_error("File '$logfile' does not exist or not readable.");
        }

        require_once($this->mooshDir . '/includes/ApacheLogParser/Parser.class.php');

        /*
         LogFormat "H: %v U: %{MOODLEUSER}n T: %Ts / %Dus | %{X-Forwarded-For}i %l %u %t \"%r\" %>s %O \"%{Referer}i\" \"%{User-Agent}i\"" moodle_log
           H: www,example.com U: 10164391 T: 0s / 20500µs | 192.198.151.44 - - [22/Dec/2013:08:20:35 +0000] "GET /login/index.php?testsession=1164 HTTP/1.1"
           303 904 "http://www,example.com/login/index.php" "Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko"
         */
        $elements = array(
            'server_name',
            array('element' => 'note', 'name' => 'moodle_user'),
            'serving_time',
            'serving_time_microseconds',
            array('element' => 'request_header_line', 'name' => 'forwarded_for'),
            'remote_logname',
            'remote_user',
            'time',
            'request_first_line',
            'status',
            'bytes_sent',
            array('element' => 'request_header_line', 'name' => 'referer'),
            array('element' => 'request_header_line', 'name' => 'user-agent'),
        );
        $re = 'H: %s U: %s T: %ss \\/ %sµs \\| %s %s %s %s "%s" %s %s "%s" "%s"';
        // $parser = Parser::createFormat(Parser::$FORMAT_COMBINED);
        $parser = new Parser($re, $elements);
        $parser->setFile($logfile);
        while (($line = $parser->next()) != -1) {
            var_dump($line);
            if (!count($line)) {
                die();
            }

        }
    }

}
