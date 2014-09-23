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
 * The DB table:
 *
 *
 *
 * Class ParseApacheLog
 * @package Moosh\Command\Generic\Dev
 */
class ApacheParsePerfLog extends MooshCommand
{
    public static $MAX_LINE_LENGTH = 8192;

    public function __construct()
    {
        parent::__construct('parse-perflog', 'apache');

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
