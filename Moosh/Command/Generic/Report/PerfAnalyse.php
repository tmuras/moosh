<?php
/**
 * moosh - Moodle Shell
 * TBA
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Generic\Report;

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
        $this->addOption('c|csv', 'create CSV files.',false);

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
        // timestamp: 2020-01-30 04:44:09
        $sql = "SELECT COUNT( * ) AS total,
                MIN(timestamp) as first,
                MAX(timestamp) as last                 
				FROM perflog
				WHERE timestamp > ? and timestamp < ?";
        $result = $DB->get_record_sql($sql, $fromto);
        echo "Total number of rows: " . $result->total . "\n";
        echo "First entry: " . $result->first . "\n";
        echo "Last entry: " . $result->last . "\n";
        
        $this->script_analyse("/mod/chat/chat_ajax.php", $fromdate, $todate);
        die();
        $sql = "SELECT script, COUNT(*) AS total  FROM perflog
                WHERE timestamp > ? and timestamp < ?
                GROUP BY script ORDER BY total DESC";
        $results = $DB->get_records_sql($sql, $fromto);

        foreach ($results as $result) {
            echo $result->script . "\t" . $result->total . "\n";
        }

        $top5 = array_slice($results, 0, 5);
        foreach ($top5 as $topscript) {
            $this->script_analyse($topscript->script, $fromdate, $todate);
        }

        // Analyze single script in given $fromdate - $todate time frame.
        // min, max, avg times.
        // Special case - /lib/ajax/service.php
        // user + sys + DB queries = ticks
        // 
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
            echo $sql; 
            var_dump(array_merge($fromto, [$options['req-sec']])); 
            // SELECT COUNT(*) AS requests, SUBSTRING(timestamp, 0, 8) as date FROM perflog WHERE script="/mod/chat/chat_ajax.php" GROUP BY date ORDER BY date ASC
         
            $results = $DB->get_records_sql($sql, array_merge($fromto, [$options['req-sec']]));
            foreach ($results as $result) {
                fputcsv($csvfile, [$result->timestamp, $result->requests]);
            }
            fclose($csvfile);
        }



    }

    protected function script_analyse($script, $fromdate, $todate)
    {
        global $DB;
        $options = $this->expandedOptions;
        
        if ($script == '/lib/ajax/service.php') {
            $ajaxcalculator = new ajax_service_calculator();
            $sql = "SELECT * FROM perflog
                WHERE timestamp > ? and timestamp < ? AND script LIKE ?";
            $results = $DB->get_records_sql($sql, [$fromdate->format('Y-m-d H:i:s'), $todate->format('Y-m-d H:i:s'), $script]);
            foreach ($results as $result) {
                $ajaxcalculator->add($result);
            } 
            $ajaxcalculator->get_stats();
        }
        
        $calc = new script_calculator($script);
        $sql = "SELECT timestamp, COUNT(*) AS requests FROM perflog
                WHERE timestamp > ? and timestamp < ? AND script LIKE ?
                GROUP BY timestamp ";
        
        $results = $DB->get_records_sql($sql, [$fromdate->format('Y-m-d H:i:s'), $todate->format('Y-m-d H:i:s'), $script]);
        foreach ($results as $result) {
            $tempdate = new \DateTime($result->timestamp);
            $calc->add($tempdate, $result->requests);
        }
        
        $calc->get_stats();
        if ($options['csv']) {
            // dump 
        }
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


class script_calculator
{
    private $globalcount = 0;
    private $name;
    private $daily = [];
    private $hourly = [];
    
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Add the value of $users
     *
     * @param $date
     * @param $users
     */
    public function add(\DateTime $datetime, $requests)
    {
        // Global average.
        $this->globalcount += $requests;

        // Let's store each day separately.
        // z - The day of the year 0 - 365.
        $dayyear = $datetime->format('z');
        
        if(!isset($this->daily[$dayyear])) {
            $this->daily[$dayyear] = ['count' => 0, 'sum' => 0, 'max' => 0, 'date' => $datetime];;
        }
        $this->daily[$dayyear]['count']++;
        $this->daily[$dayyear]['sum'] += $requests;
        
        // Each hour separately
        $hour = $datetime->format('Y-m-d:H');
        if(!isset($this->hourly[$hour])) {
            $this->hourly[$hour] = ['count' => 0, 'sum' => 0, 'max' => 0, 'date' => $datetime];;
        }
        $this->hourly[$hour]['count']++;
        $this->hourly[$hour]['sum'] += $requests;

    }

    public function get_stats()
    {
        ksort($this->daily);
        ksort($this->hourly);
        
        echo "Total number of requests: " . $this->globalcount . "\n";
//        var_dump($this->daily);
        var_dump($this->hourly);
    }
    
    public function dump_csv_files()
    {
        $filepath = $this->cwd . '/' . $this->name . '-requests-per-day.csv';
        $csvfile = fopen($filepath, 'w');
        if (!$csvfile) {
            cli_error("Can't open '$filepath' for writing");
        }
        
        foreach ($this->daily as $day) {
            fputcsv($csvfile, [$day['date']]);
        }
        fclose($csvfile);
    }
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
        if(!$matches[1]) {
            return;
        }
        $name = $matches[1];
        if(!isset($this->byname[$name])) {
            $this->byname[$name] = 0;
        }
        $this->byname[$name]++;
    }

    public function get_stats()
    {
        arsort($this->byname);
        $sum = array_sum($this->byname);
        foreach ($this->byname as $name => $total) {
            echo "$name\t$total\t". (int)($total/$sum*100) ."\n";
        }
    }
}
