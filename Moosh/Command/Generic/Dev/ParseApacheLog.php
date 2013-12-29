<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Generic\Dev;

use Moosh\MooshCommand;
use Moosh\ApacheLogParser\Parser;

/**
 * The DB table:
 *
 * time,timestamp,url,memory_peak,includecount,langcountgetstring,db_reads,ticks,user,sys,serverload
 *
 * Class ParsePerformanceLog
 * @package Moosh\Command\Generic\Dev
 */
class ParseApacheLog extends MooshCommand
{
    public static $MAX_LINE_LENGTH = 8192;

    public function __construct()
    {
        parent::__construct('parse', 'apachelog');

        $this->addArgument('logfile');
        $this->addOption('t|table', 'table name', 'perflog');

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

        //H: aua3.learnonline.ie U: 10164391 T: 0s / 20500µs | 192.198.151.44 - - [22/Dec/2013:08:20:35 +0000] "GET /login/index.php?testsession=1164 HTTP/1.1"
        //303 904 "http://aua3.learnonline.ie/login/index.php" "Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko"
        /*
               // LogFormat "H: %v U: %{MOODLEUSER}n T: %Ts / %Dµs | %{X-Forwarded-For}i %l %u %t \"%r\" %>s %O \"%{Referer}i\" \"%{User-Agent}i\"" time_taken
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
            var_dump($line['moodle_user']);
            if (!count($line)) {
                die();
            }

        }
        die('ok');

        $handle = fopen($logfile, "r");

        while (($line = fgets($handle, self::$MAX_LINE_LENGTH)) !== false) {
            if (strpos($line, 'PERF:') === false) {
                continue;
            }
            $row = array();
            //echo $line;
            //time: 2.774779s
            //$row['raw'] = $line;
            $row['time'] = (int)($this->parse($line, 'time: (\d+.\d+)s') * 1000000);

            //[Sun Dec 22 06:29:01 2013]
            $row['timestamp'] = $this->parse($line, ' (.*?)\]');
            $tmp = date_parse($row['timestamp']);

            if ($tmp['year'] < 10) {
                $tmp['year'] = '0' . $tmp['year'];
            }
            if ($tmp['month'] < 10) {
                $tmp['month'] = '0' . $tmp['month'];
            }
            if ($tmp['day'] < 10) {
                $tmp['day'] = '0' . $tmp['day'];
            }
            if ($tmp['hour'] < 10) {
                $tmp['hour'] = '0' . $tmp['hour'];
            }
            if ($tmp['minute'] < 10) {
                $tmp['minute'] = '0' . $tmp['minute'];
            }
            if ($tmp['second'] < 10) {
                $tmp['second'] = '0' . $tmp['second'];
            }

            $row['timestamp'] = $tmp['year'] . '-' . $tmp['month'] . '-' . $tmp['day'] . ' ' . $tmp['hour'] . ':' . $tmp['minute'] . ':' . $tmp['second'];

            //PERF: /login/index.php
            $row['url'] = $this->parse($line, 'PERF: (.*?) ');
            //if no URL, we assume it was cron
            if (!$row['url']) {
                $row['url'] = '<cron>';
            }

            //memory_peak: 67556680B (
            $row['memory_peak'] = $this->parse($line, 'memory_peak: (\d+)B');

            //includecount: 751
            $row['includecount'] = $this->parse($line, 'includecount: (\d+)');

            //contextswithfilters
            $row['contextswithfilters'] = $this->parse($line, 'contextswithfilters: (\d+)');

            //filterscreated
            $row['filterscreated'] = $this->parse($line, 'filterscreated: (\d+)');
            $row['textsfiltered'] = $this->parse($line, 'textsfiltered: (\d+)');
            $row['stringsfiltered'] = $this->parse($line, 'stringsfiltered: (\d+)');
            $row['langcountgetstring'] = $this->parse($line, 'langcountgetstring: (\d+)');


            //db reads/writes: 62/30
            $row['db_reads'] = $this->parse($line, 'db reads\\/writes: (\d+)');
            $row['db_writes'] = $this->parse($line, 'db reads\\/writes: \d+\\/(\d+)');

            //ticks: 278 user: 60 sys: 4 cuser: 0 csys: 0
            $row['ticks'] = $this->parse($line, 'ticks: (\d+)');
            $row['user'] = $this->parse($line, 'user: (\d+)');
            $row['sys'] = $this->parse($line, 'sys: (\d+)');
            $row['cuser'] = $this->parse($line, 'cuser: (\d+)');
            $row['csys'] = $this->parse($line, 'csys: (\d+)');

            //serverload: 1.58
            $row['serverload'] = (int)($this->parse($line, 'serverload: (\d+.\d+)') * 100);

            //var_dump($row);
            //we assume that the row is unique if timestamp, url and time are unique. Therefore they are required values
            if (!$row['url'] || !$row['timestamp'] || !$row['time']) {
                cli_problem('Invalid row: ' . $row['url']);
                continue;
            }

            //construct SQL statement
            $columns = array();
            $values = array();
            foreach ($row as $k => $v) {
                if (isset($v)) {
                    $columns[] = $k;
                    $values[] = "'" . mysql_real_escape_string($v) . "'";
                }
            }

            $sql = "INSERT IGNORE INTO " . $this->options['table'] . " (" . implode(',', $columns) . ') VALUES (' . implode(',', $values) . ');';
            echo "$sql\n";
        }

    }

    private function parse($line, $regexp)
    {
        $matches = NULL;
        if (!preg_match("/$regexp/", $line, $matches)) {
            return NULL;
        }
        return $matches[1];
    }
}
