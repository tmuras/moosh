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
 * Class ApacheParseMissingFiles
 * @package Moosh\Command\Generic\Apache
 */
class ApacheParseMissingFiles extends MooshCommand
{
    public static $MAX_LINE_LENGTH = 8192;

    public function __construct()
    {
        parent::__construct('parse-missing-files', 'apache');

        $this->addArgument('logfile');
        $this->addOption('a|after:','only entries after this date');
    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_NONE;
    }

    public function execute()
    {
        $logfile = $this->arguments[0];
        $options = $this->expandedOptions;

        $this->checkFileArg($logfile);

        require_once($this->mooshDir . '/includes/ApacheLogParser/Parser.class.php');

        $after = false;
        if($options['after']) {
            $after = strtotime($options['after']);
        }

        $parser = Parser::createFormat(Parser::$FORMAT_COMBINED);

        $parser->setFile($logfile);
        $i=0;
        $list = array();
        while (($line = $parser->next()) != -1) {
            $i++;
            if($line['status'] != 404) {
                continue;
            }

            // Possibly check a date
            if($after && $line['time'] <= $after) {
                continue;
            }

            // Check for 'GET /file.php/1234/filename HTTP/'
            $matches = NULL;
            if(!preg_match('|GET /file.php/(.*) HTTP/|',$line['request_first_line'],$matches)) {
                continue;
            }

            //echo $matches[1];
            if(!isset($list[$matches[1]])) {
                $list[$matches[1]] = 0;
            }
            $list[$matches[1]]++;

            if (!count($line)) {
                echo "Line $i could not be parsed!\n";
                var_dump($line);
                die();
            }

        }

        foreach($list as $k=>$n) {
            echo "$n,$k\n";
        }

    }
}
