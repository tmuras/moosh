<?php
/**
 * moosh - Moodle Shell
 * TBA
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Generic\Report;

use Moosh\Analysis\ScriptCalculator;
use Moosh\MooshCommand;

/**
 * Class ReportConcurrency
 *
 * The days where there was no activity at all are excluded from the statistics by default.
 *
 * @package Moosh\Command\Moodle31\Report
 */
class PerfAnalyse extends MooshCommand
{
    const DATE_FORMAT = "Y-m-d H:i:s";

    /**
     * @var \DateTime
     */
    private $from;

    /**
     * @var \DateTime
     */
    private $to;

    private $fromto;

    public function __construct()
    {
        parent::__construct('analyse', 'perf');

        $this->addOption('f|from:',
            'from date in YYYYMMDD or YYYY-MM-DD format (default is 1 month ago)'
        );
        $this->addOption('t|to:',
            'to date in YYYYMMDD or YYYY-MM-DD format (default is today)');
        $this->addOption('z|time-zone:',
            'timezone used to display the dates. Possible values on https://secure.php.net/manual/en/timezones.php.', "UTC");
        $this->addOption('r|req-sec:',
            'dump requests/sec but only those bigger than the value provided.', 0);
        $this->addOption('c|csv', 'create CSV files.', false);

    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_DB_ONLY;
    }

    public function execute()
    {
        global $DB, $CFG;

        $options = $this->expandedOptions;

        $timezone = new \DateTimeZone($options['time-zone']);
        $fromdate = str_replace('-', '', $options['from']);
        if (strlen($fromdate) != 8) {
            cli_error("Wrong format for --from. Use YYYYMMDD or YYYY-MM-DD");
        }
        if (!$fromdate) {
            // Set to 1 month ago.
            $fromdate = new \DateTime('now', $timezone);
            $fromdate->setTime(0, 0, 0);
            $fromdate->sub(new \DateInterval('P1M'));
        } else {
            $fromdate = new \DateTime($fromdate, $timezone);
        }

        $todate = str_replace('-', '', $options['to']);
        if (strlen($todate) != 8) {
            cli_error("Wrong format for --to. Use YYYYMMDD or YYYY-MM-DD");
        }

        if (!$todate) {
            // Set to 1 month ago.
            $todate = new \DateTime('now', $timezone);
        } else {
            $todate = new \DateTime($todate, $timezone);
        }
        $todate->setTime(23, 59, 59);

        if ($todate < $fromdate) {
            cli_error('"to date" must be later than "from date".');
        }

        // Get the entries from $fromdate to $todate
        // Grouped by script name, ordered by count.
        $fromto = [$fromdate->format('Y-m-d H:i:s'), $todate->format('Y-m-d H:i:s')];

        $this->from = $fromdate;
        $this->to = $todate;
        $this->fromto = $fromto;

        // timestamp: 2020-01-30 04:44:09
        $sql = "SELECT COUNT( * ) AS total,
                MIN(timestamp) as first,
                MAX(timestamp) as last
				FROM perflog
				WHERE timestamp > ? and timestamp < ?";
        $result = $DB->get_record_sql($sql, $fromto);
        echo "Total number of rows between the requested dates: " . $result->total . "\n";
        echo "First entry: " . $result->first . "\n";
        echo "Last entry: " . $result->last . "\n";

        $top5 = $this->get_by_metric("COUNT(*)", 5);
        $top5calc = [];
        echo "Top 5 scripts by number of executions:\n";
        foreach ($top5 as $topscript) {
            echo "\t" . $topscript->script . "\t" . $topscript->total . "\n";

            // Analyze single script in given $fromdate - $todate time frame.
            $top5calc[] = $this->script_analyse($topscript->script, "COUNT(*)");
        }

        // Number of requests per hour in one graph - 5 top scripts (@TODO + the rest).
        $hoursindex = $top5calc[0]->get_hours();
        $top5hours = [];
        foreach ($top5calc as $topcalc) {
            $top5hours[] = $topcalc->get_hours();
        }
        $top5hourscombined = self::combine_arrays($top5hours, 'sum');
        $header = [];
        foreach ($top5 as $topscript) {
            $header[] = $topscript->script;
        }
        $header = array_merge(['date'], $header);
        $this->save_csv('top5-requests-per-hour.csv', [[$header], $top5hourscombined]);

        // Scripts by the database usage - which cause the most reads / writes.
        $top5dbqueries = $this->get_by_metric("SUM(db_queries_time)", 5);
        $top5dbqueriescalc = [];
        echo "Top 5 scripts by the sum of DB queries time:\n";
        foreach ($top5dbqueries as $topscript) {
            echo "\t" . $topscript->script . "\t" . $topscript->total . "\n";

            // Analyze single script in given $fromdate - $todate time frame.
            $top5dbqueriescalc[] = $this->script_analyse($topscript->script, "SUM(db_queries_time)");
        }

        // Number of requests per hour in one graph - 5 top scripts + the rest.
        $hoursindex = $top5dbqueriescalc[0]->get_hours();
        $top5hours = [];
        foreach ($top5dbqueriescalc as $topcalc) {
            $top5hours[] = $topcalc->get_hours();
        }
        $top5hourscombined = self::combine_arrays($top5hours, 'sum');
        $header = [];
        foreach ($top5dbqueries as $topscript) {
            $header[] = $topscript->script;
        }
        $header = array_merge(['date'], $header);
        $this->save_csv('top5-db-query-time-per-hour.csv', [[$header], $top5hourscombined]);

        // Save each of the top 5 scripts with no of requests and DB usage columns.
        // If they happen to be in both top 5 categories.
        // Combine $top5calc and $top5dbqueriescalc.
        $topall = [];
        foreach ($top5 as $topcount) {
            if (!isset($topall[$topcount->script])) {
                $topall[$topcount->script] = ['header' => []];
            }
            $topall[$topcount->script];
        }
        foreach ($top5dbqueries as $topdb) {
            $topall[$topdb->script] = true;
        }

        var_dump($topall);
        $header = [];

        // Scripts by the CPU usage.

        // Entries that take the most time.

        // If there are different hosts, then create per-host statistics.


        if ($options['csv']) {
            $filepath = $this->cwd . '/' . 'requests-per-second.csv';
            $csvfile = fopen($filepath, 'w');
            if (!$csvfile) {
                cli_error("Can't open '$filepath' for writing");
            }

            // Requests per second.
            $sql = "SELECT timestamp, COUNT(*) AS requests FROM perflog
                WHERE timestamp > ? AND timestamp < ?
                GROUP BY timestamp
                HAVING requests > ?
                ORDER BY timestamp ASC";
//            echo $sql;
//            var_dump(array_merge($fromto, [$options['req-sec']]));
            // SELECT COUNT(*) AS requests, SUBSTRING(timestamp, 0, 8) as date FROM perflog WHERE script="/mod/chat/chat_ajax.php" GROUP BY date ORDER BY date ASC

            $results = $DB->get_records_sql($sql, array_merge($fromto, [$options['req-sec']]));
            foreach ($results as $result) {
                fputcsv($csvfile, [$result->timestamp, $result->requests]);
            }
            fclose($csvfile);
        }


    }

    /**
     * @param $metric
     */
    private function get_by_metric($metric, $top = 0)
    {
        global $DB;

        // Scripts by the database usage - which cause the most reads / writes.
        $sql = "SELECT script, $metric AS total  FROM perflog
                WHERE timestamp > ? and timestamp < ?
                GROUP BY script ORDER BY total DESC";
        if ($top) {
            $sql .= " LIMIT $top";
        }
        return $DB->get_records_sql($sql, $this->fromto);
    }

    /**
     * Combine 2 or more arrays into multi-dimensional with
     * the key put in the first column.
     * We assume the keys are exactly the same in all arrays.
     * @param array $arrays
     */
    static function combine_arrays($arrays, $attribute)
    {
        $merged = [];
        $index = array_keys($arrays[0]);
        foreach ($index as $key) {
            $row = [$key];
            foreach ($arrays as $array) {
                array_push($row, $array[$key][$attribute]);
            }
            $merged[$key] = $row;
        }
        return $merged;
    }

    private function save_csv($name, $arrays)
    {
        $filename = ltrim($name, '/');
        $filename = str_replace('/', '_', $filename);
        $filepath = $this->cwd . '/' . $filename;

        $csvfile = fopen($filepath, 'w');
        if (!$csvfile) {
            cli_error("Can't open '$filepath' for writing");
        }
        foreach ($arrays as $array) {
            foreach ($array as $row) {
                fputcsv($csvfile, $row);
            }
        }
        fclose($csvfile);
    }

    /**
     * Analysis of a single script.
     *
     * @param $script
     * @param $fromdate
     * @param $todate
     * @throws \Exception
     */
    protected function script_analyse($script, $metric)
    {
        // min, max, avg times.
        // Special case - /lib/ajax/service.php
        // user + sys + DB queries = ticks

        global $DB;
        $options = $this->expandedOptions;

//        if ($script == '/lib/ajax/service.php') {
//            $ajaxcalculator = new ajax_service_calculator();
//            $sql = "SELECT * FROM perflog
//                WHERE timestamp > ? and timestamp < ? AND script LIKE ?";
//            $results = $DB->get_records_sql($sql, array_merge($this->fromto, [$script]));
//            foreach ($results as $result) {
//                $ajaxcalculator->add($result);
//            }
//            $ajaxcalculator->get_stats();
//        }

        $calc = new ScriptCalculator($script, $this->from, $this->to);
        $sql = "SELECT timestamp, $metric AS requests FROM perflog
                WHERE timestamp > ? and timestamp < ? AND script LIKE ?
                GROUP BY timestamp ";

        $results = $DB->get_records_sql($sql, array_merge($this->fromto, [$script]));
        foreach ($results as $result) {
            $tempdate = new \DateTime($result->timestamp);
            $calc->add($tempdate, $result->requests);
        }

        $calc->get_stats();
        return $calc;
    }

    /**
     * Week day number 1-7 to name (Monday-Sunday).
     * @param $number
     */
    private function week_day_name($number)
    {
        switch ($number) {
            case 1:
                return 'Monday';
            case 2:
                return 'Tuesday';
            case 3:
                return 'Wednesday';
            case 4:
                return 'Thursday';
            case 5:
                return 'Friday';
            case 6:
                return 'Saturday';
            case 7:
                return 'Sunday';
        }
        throw new \Exception("Invalid day number: '$number'");
    }
}

class stats2
{
    public $weekdays;
    public $globalavg;
    public $avg;
}


class ajax_service_calculator
{
    private $globalcount = 0;
    private $byname = [];

    public function __construct()
    {

    }

    /**
     * Add the value of $users
     *
     * @param $date
     * @param $users
     */
    public function add($row)
    {
        // Global average.
        $this->globalcount++;

        // Group per name of the webservice
        // /lib/ajax/service.php?sesskey=t1Ua4HpoH6&info=core_calendar_get_calendar_monthly_view

        $matches = null;
        $query = preg_match('/info=(\w+)/', $row->query, $matches);
        if (!$matches[1]) {
            return;
        }
        $name = $matches[1];
        if (!isset($this->byname[$name])) {
            $this->byname[$name] = 0;
        }
        $this->byname[$name]++;
    }

    public function get_stats()
    {
        arsort($this->byname);
        $sum = array_sum($this->byname);
        foreach ($this->byname as $name => $total) {
            //echo "$name\t$total\t" . (int)($total / $sum * 100) . "\n";
        }
    }
}
