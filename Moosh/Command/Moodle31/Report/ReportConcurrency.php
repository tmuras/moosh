<?php
/**
 * moosh - Moodle Shell
 * TBA
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle31\Report;

use Moosh\MooshCommand;

/**
 * Class ReportConcurrency
 *
 * The days where there was no activity at all are excluded from the statistics by default.
 *
 * @package Moosh\Command\Moodle31\Report
 */
class ReportConcurrency extends MooshCommand {

    const DATE_FORMAT = "Y-m-d H:i:s";

    public function __construct() {
        parent::__construct('concurrency', 'report');

        $this->addOption('f|from:',
                'from date in YYYYMMDD or YYYY-MM-DD format (default is 1 month ago)'
        );
        $this->addOption('t|to:',
                'to date in YYYYMMDD or YYYY-MM-DD format (default is today)');
        $this->addOption('p|period:', 'period of time (in minutes) during which unique users will be counted as concurrent.', 5);
        $this->addOption('z|time-zone:',
                'timezone used to display the dates. Possible values on https://secure.php.net/manual/en/timezones.php.', "UTC");
        $this->addOption('c|csv:', 'save the concurrency values to CSV file.');
        $this->addOption('Z|zero-days-include:',
                'By default the days with no activity at all are ignored in some statistics. This option reverts that.');
        $this->addOption('work-hours-from:',
                '(not implemented yet) The start time for generating the statistics. Values from 0 to 23.');
        $this->addOption('work-hours-to:',
                '(not implemented yet) The start time for generating the statistics. Values from 0 to 23.');
        $this->addOption('work-days:', '(not implemented yet) Define working days.', '12345');

    }

    public function execute() {
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

        if ($this->verbose) {
            echo 'Records from ' .
                    $fromdate->format(self::DATE_FORMAT) . ' [' . $fromdate->getTimestamp() . ']' .
                  ' to ' .
                    $todate->format(self::DATE_FORMAT) . ' [' . $todate->getTimestamp() . ']' . "\n";
        }

        $tsutcfrom = $fromdate->getTimestamp();
        $tsutcto = $todate->getTimestamp();
        
        if ($options['csv']) {
            $filepath = $this->cwd . '/' . $options['csv'];
            $csvfile = fopen($filepath, 'w');
            if (!$csvfile) {
                cli_error("Can't open '$filepath' for writing");
            }
        }

        $period = $options['period'] * 60;

        // Number of entries in the log for given from-to range.
        $sql = "SELECT COUNT(*) AS 'count', MIN(id) AS 'min', MAX(id) AS 'max'
                   FROM {logstore_standard_log}
				  WHERE timecreated BETWEEN $tsutcfrom AND $tsutcto";
        $record = $DB->get_record_sql($sql);
        $recordsfrom = $record->min;
        $recordsto = $record->max;
        $recordscount = $record->count;
        if ($this->verbose) {
            echo "To retrieve $recordscount records from id $recordsfrom to $recordsto\n";
        }

        // Get the number of concurrent users for each period.
        $sql = "SELECT 
                  period * ( $period ) AS unixtime,
				  online_users FROM 
				
				(SELECT ROUND( timecreated / ( $period ) ) AS period,
				COUNT( DISTINCT userid ) AS online_users
				FROM {logstore_standard_log}
				WHERE timecreated BETWEEN $tsutcfrom AND $tsutcto
				AND origin = 'web'
				GROUP BY period
				) AS concurrent_users_report";
        $query = $DB->get_records_sql($sql);

        $fulldata = [];
        $previoustime = null;
        foreach ($query as $k => $v) {
            $date = date_create('@' . $v->unixtime, $timezone);
            if (!$date) {
                die("Invalid date for " . $v->unixtime);
            }
            $fulldata[$v->unixtime] = ['date' => $date, 'users' => $v->online_users];

            if ($previoustime && $v->unixtime - $previoustime != $period) {
                // Insert missing records - per period.
                $missing = ($v->unixtime - $previoustime) / ($period) - 1;
                $missing = (int) $missing;
                for ($i = 1; $i <= $missing; $i++) {
                    $tempdate = date_create('@' . ($v->unixtime + $i * $period), $timezone);
                    $fulldata[$v->unixtime + $i * $period] =
                            ['date' => $tempdate, 'users' => 0];
                }
            }
            $previoustime = $v->unixtime;
        }

        $weekstats = new weekday_stats($options['zero-days-include']);

        $maxconcurrent = ['date' => null, 'users' => 0];
        foreach ($fulldata as $k => $row) {
            $weekstats->add($row['date'], $row['users']);
            echo $row['date']->format(self::DATE_FORMAT), " - ", $row['users'], "\n";

            if ($options['csv']) {
                fputcsv($csvfile, [$row['date']->format(self::DATE_FORMAT), $row['users']]);
            }
            if ($row['users'] > $maxconcurrent['users']) {
                $maxconcurrent['users'] = $row['users'];
                $maxconcurrent['date'] = $row['date'];
            }
        }

        if ($options['csv']) {
            fclose($csvfile);
        }

        echo "Name: " . $CFG->wwwroot . "\n";

        // Database size
        $sql = "SELECT table_name AS 'table',
                  ROUND(((data_length + index_length))) AS 'Size(Bytes)'
                  FROM information_schema.TABLES
                  WHERE table_schema = '" . $CFG->dbname . "'
                  ORDER BY (data_length + index_length) DESC";
        $results = $DB->get_records_sql($sql);

        $databasesize = 0;
        $topsizes = [];
        $topnames = [];
        foreach ($results as $result) {
            if (!isset($topsizes[0])) {
                $topsizes[0] = $result->{'size(bytes)'};
                $topnames[0] = $result->table;
            } else if (!isset($topsizes[1])) {
                $topsizes[1] = $result->{'size(bytes)'};
                $topnames[1] = $result->table;
            } else if (!isset($topsizes[2])) {
                $topsizes[2] = $result->{'size(bytes)'};
                $topnames[2] = $result->table;
            }
            $databasesize += $result->{'size(bytes)'};
        }
        echo "Database Size: $databasesize\n";
        echo "Top 3 tables:\n";
        for ($i = 0; $i < 3; $i++) {
            echo "\t" . $topnames[$i] . " : " . $topsizes[$i] . "\n";
        }

        // Display active users during specified period.
        $sql = "SELECT COUNT( DISTINCT userid ) AS NumberOfActiveUsers
				FROM {logstore_standard_log}
				WHERE timecreated BETWEEN $tsutcfrom AND $tsutcto";
        $result = $DB->get_record_sql($sql);

        echo "Active Users: " . $result->{'numberofactiveusers'} . "\n";
        echo "Max Concurrent Users: " . $maxconcurrent['users'] . "\n";
        echo "\ton " . $maxconcurrent['date']->format(self::DATE_FORMAT) . "\n";
        $stats = $weekstats->get_stats();
        print_r($stats);

        //echo "Average concurrent users: " . round($totalusersinpastyear / $periodsoveryear, 2) . "\n";
    }
}

class weekday_stats {
    private $daily = [];
    private $weekdays = [];
    private $zerodaysinclude = false;

    public function __construct($zerodaysinclude) {
        $this->zerodaysinclude = $zerodaysinclude;
    }

    /**
     * Add the value of $users
     *
     * @param $date
     * @param $users
     */
    public function add(\DateTime $date, $users) {
        // Let's store each day separately.
        // z - The day of the year 0 - 365.
        $dayyear = $date->format('z');
        if (!isset($this->daily[$dayyear])) {
            $this->daily[$dayyear] = ['count' => 0, 'sum' => 0, 'max' => 0, 'date' => $date];
        }
        $this->daily[$dayyear]['count']++;
        $this->daily[$dayyear]['sum'] += $users;
        if ($users > $this->daily[$dayyear]['max']) {
            $this->daily[$dayyear]['max'] = $users;
        }
    }

    public function get_stats() {
        // Now that we have all data, calculate averages.
        foreach ($this->daily as $day => $data) {
            $this->daily[$day]['avg'] = round($data['sum'] / $data['count'], 2);
        }

        // Generate statistics per week day.
        // N - Week day.
        foreach ($this->daily as $day => $data) {
            if (!$this->zerodaysinclude && $data['sum'] == 0) {
                continue;
            }
            $currentweekday = $data['date']->format("N");
            if (!isset($this->weekdays[$currentweekday])) {
                $this->weekdays[$currentweekday] = ['count' => 0, 'sum' => 0];
            }
            $this->weekdays[$currentweekday]['count']++;
            $this->weekdays[$currentweekday]['sum'] += $data['avg'];
        }

        foreach ($this->weekdays as $day => $data) {
            $this->weekdays[$day]['avg'] = round($data['sum'] / $data['count'], 2);
        }

        return $this->weekdays;
    }
}