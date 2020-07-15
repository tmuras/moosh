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
                'The start hour for generating the statistics. Values from 0 to 23. Default: 0.', 0);
        $this->addOption('work-hours-to:',
                'The finish hour for generating the statistics. Values from 0 to 23. Default: 0.', 0);
        $this->addOption('work-days:', 'Define working days. 1 - Monday, ..., 7 - Sunday. Defaults to whole week: 1234567.', '1234567');

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

        if ($options['work-hours-from'] < 0 || $options['work-hours-from'] > 23) {
            cli_error("'work-hours-from' must be between 0 and 23");
        }

        if ($options['work-hours-to'] < 0 || $options['work-hours-from'] > 23) {
            cli_error("'work-hours-from' must be between 0 and 23");
        }

        if (preg_match('/^\d+$/', $options['work-days']) != 1) {
            cli_error("'work-days' should be a string constructed from digits 1-7");
        }

        if ($this->verbose) {
            $dowfrom = $fromdate->format('N');
            $dowfrom = $this->week_day_name($dowfrom);
            $dowto = $todate->format('N');
            $dowto = $this->week_day_name($dowto);

            echo 'Records from ' .
                $fromdate->format(self::DATE_FORMAT) . ' [' . $fromdate->getTimestamp() . "] $dowfrom" .
                ' to ' .
                $todate->format(self::DATE_FORMAT) . ' [' . $todate->getTimestamp() . "] $dowto\n";
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
				  WHERE timecreated >= $tsutcfrom AND timecreated < $tsutcto";
        $record = $DB->get_record_sql($sql);
        $recordsfrom = $record->min;
        $recordsto = $record->max;
        $recordscount = $record->count;
        if ($this->verbose) {
            echo "About to retrieve $recordscount records from id $recordsfrom to $recordsto\n";
        }

        // Get the number of concurrent users for each period.
        $sql = "SELECT
                  period * ( $period ) AS unixtime,
				  online_users,
				  number_actions FROM
				
				(SELECT ROUND( timecreated / ( $period ) ) AS period,
				COUNT( DISTINCT userid ) AS online_users,
				COUNT( id ) AS number_actions
				FROM {logstore_standard_log}
				WHERE timecreated >= $tsutcfrom AND timecreated < $tsutcto
				AND origin = 'web'
				GROUP BY period
				) AS concurrent_users_report";
        $results = $DB->get_records_sql($sql);

        $fulldata = [];
        $previoustime = null;
        foreach ($results as $k => $v) {
            $date = date_create('@' . $v->unixtime, new \DateTimeZone('UTC'));
            $date->setTimezone($timezone);
            if ($date < $fromdate || $date > $todate) {
                continue;
            }
            if (!$date) {
                die("Invalid date for " . $v->unixtime);
            }
            $fulldata[$v->unixtime] = ['date' => $date, 'users' => $v->online_users, 'actions' => $v->number_actions];

            if ($previoustime && $v->unixtime - $previoustime != $period) {
                // Insert missing records - per period.
                $missing = ($v->unixtime - $previoustime) / ($period) - 1;
                $missing = (int)$missing;
                for ($i = 1; $i <= $missing; $i++) {
                    $tempdate = date_create('@' . ($v->unixtime + $i * $period), $timezone);
                    $fulldata[$v->unixtime + $i * $period] =
                        ['date' => $tempdate, 'users' => 0, 'actions' => 0];
                }
            }
            $previoustime = $v->unixtime;
        }
        unset($results);

        // Get number of messages created.
        $sql = "SELECT
                  period * ( $period ) AS unixtime,
                  users_from, number_messages FROM
				(SELECT ROUND( timecreated / ( $period ) ) AS period,
				COUNT( DISTINCT useridfrom ) AS users_from,
				COUNT( id ) AS number_messages
				FROM {messages}
				WHERE timecreated >= $tsutcfrom AND timecreated < $tsutcto
				GROUP BY period
				) AS messages_report";
        $results = $DB->get_records_sql($sql);
        foreach ($results as $k => $v) {
            $date = date_create('@' . $v->unixtime, new \DateTimeZone('UTC'));
            $date->setTimezone($timezone);
            if ($date < $fromdate || $date > $todate) {
                continue;
            }
            if (!$date) {
                die("Invalid date for " . $v->unixtime);
            }
            if(isset($v->users_from)) {
                $fulldata[$v->unixtime]['message_users_from'] = $v->users_from;
            } else {
                $fulldata[$v->unixtime]['message_users_from'] = 0;
            }
            if(isset($v->number_messages)) {
                $fulldata[$v->unixtime]['number_messages'] = $v->number_messages;
            } else {
                $fulldata[$v->unixtime]['number_messages'] = 0;
            }
        }
        unset($results);

        // Get number of notifications created.
        $sql = "SELECT
                  period * ( $period ) AS unixtime,
                  users_to, number_notifications FROM
				(SELECT ROUND( timecreated / ( $period ) ) AS period,
				COUNT( DISTINCT useridto ) AS users_to,
				COUNT( id ) AS number_notifications
				FROM {messages}
				WHERE timecreated >= $tsutcfrom AND timecreated < $tsutcto
				GROUP BY period
				) AS notifications_report";
        $results = $DB->get_records_sql($sql);
        foreach ($results as $k => $v) {
            $date = date_create('@' . $v->unixtime, new \DateTimeZone('UTC'));
            $date->setTimezone($timezone);
            if ($date < $fromdate || $date > $todate) {
                continue;
            }
            if (!$date) {
                die("Invalid date for " . $v->unixtime);
            }
            if(isset($v->users_from)) {
                $fulldata[$v->unixtime]['notification_users_to'] = $v->users_to;
            } else {
                $fulldata[$v->unixtime]['notification_users_to'] = 0;
            }
            if(isset($v->number_messages)) {
                $fulldata[$v->unixtime]['number_notifications'] = $v->number_notifications;
            } else {
                $fulldata[$v->unixtime]['number_notifications'] = 0;
            }
        }
        unset($results);

        $weekstats =
            new weekday_stats_calculator($options['zero-days-include'], $options['work-hours-from'], $options['work-hours-to'],
                $options['work-days']);

        $maxconcurrent = ['date' => null, 'users' => 0];
        foreach ($fulldata as $k => $row) {
            $weekstats->add($row['date'], $row['users']);
            //echo $row['date']->format(self::DATE_FORMAT), " - ", $row['users'], "\n";

            if ($options['csv']) {
                fputcsv($csvfile, [$row['date']->format(self::DATE_FORMAT), $row['users'], $row['actions'],
                        $row['message_users_from'], $row['number_messages'],
                        $row['notification_users_to'], $row['number_notifications']]
                );
            }
            if ($row['users'] > $maxconcurrent['users']) {
                $maxconcurrent['users'] = $row['users'];
                $maxconcurrent['date'] = $row['date'];
            }
        }

        if ($options['csv']) {
            fclose($csvfile);
        }

        if(isset($CFG->wwwroot)) {
            echo "Name: " . $CFG->wwwroot . "\n";
        }

        // Display active users during specified period.
        $sql = "SELECT COUNT( DISTINCT userid ) AS NumberOfActiveUsers
				FROM {logstore_standard_log}
				WHERE timecreated BETWEEN $tsutcfrom AND $tsutcto";
        $result = $DB->get_record_sql($sql);

        echo "Active Users: " . $result->{'numberofactiveusers'} . "\n";

        if ($maxconcurrent['date']) {
            $weekday = $maxconcurrent['date']->format("N");
            echo "Max Concurrent Users: " . $maxconcurrent['users'] . "\n";
            echo "\ton " . $this->week_day_name($weekday) . ', ' . $maxconcurrent['date']->format(self::DATE_FORMAT) . "\n";
        }

        $stats = $weekstats->get_stats();
        echo "Average concurrent users per day of the week\n";
        foreach($stats->weekdays as $d=>$weekday) {
            echo "\t" . $this->week_day_name($d) . ' : ' . $weekday['avg'] . "\n";
        }

        echo "Global average concurrent users: " . $stats->globalavg ."\n";
        echo "Average concurrent users considering working days & hours: " . $stats->avg ."\n";

    }

    /**
     * Week day number 1-7 to name (Monday-Sunday).
     * @param $number
     */
    private function week_day_name($number) {
        switch($number) {
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

class stats {
    public $weekdays;
    public $globalavg;
    public $avg;
}

class weekday_stats_calculator {
    private $daily = [];
    private $weekdays = [];
    private $zerodaysinclude = false;
    private $workhoursfrom = null;
    private $workhoursto = null;
    private $workdays = null;
    private $globalsum = 0;
    private $globalcount = 0;
    private $globalavg = 0;

    public function __construct($zerodaysinclude, $workhoursfrom, $workhoursto, $workdays) {
        $this->zerodaysinclude = $zerodaysinclude;
        $this->workhoursfrom = $workhoursfrom;
        $this->workhoursto = $workhoursto;
        $this->workdays = $workdays;
    }

    /**
     * Add the value of $users
     *
     * @param $date
     * @param $users
     */
    public function add(\DateTime $date, $users) {
        // Global average.
        $this->globalcount++;
        $this->globalsum += $users;

        // Let's store each day separately.
        // z - The day of the year 0 - 365.
        $dayyear = $date->format('z');

        // N - Week day.
        $dayweek = $date->format('N');
        if (strstr($this->workdays, $dayweek) === false) {
            // Ignore the whole day.
            return false;
        }
        // G 24-hour format of an hour without leading zeros 	0 through 23
        $hour = (int) $date->format('G');
        if ($this->workhoursfrom && $hour < $this->workhoursfrom) {
            return false;
        }
        if ($this->workhoursto && $hour >= $this->workhoursto) {
            return false;
        }

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
        $stats = new stats();

        if($this->globalcount) {
            $this->globalavg = round($this->globalsum / $this->globalcount, 2);
        } else {
            $this->globalavg = 0;
        }
        $stats->globalavg = $this->globalavg;

        // Now that we have all data, calculate averages.
        foreach ($this->daily as $day => $data) {
            $this->daily[$day]['avg'] = round($data['sum'] / $data['count'], 2);
        }

        // Calculate global avg - but this one is after exclusions.
        $sum = 0;
        $count = 0;
        foreach ($this->daily as $day => $data) {
            $count++;
            $sum += $this->daily[$day]['avg'];
        }
        if($count) {
            $stats->avg = round($sum / $count, 2);
        } else {
            $stats->avg = 0;
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
        ksort($this->weekdays);
        $stats->weekdays = $this->weekdays;

        return $stats;
    }
}
