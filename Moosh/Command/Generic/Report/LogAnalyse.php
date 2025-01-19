<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2023 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Generic\Report;

use Moosh\MooshCommand;


/**
 * Class LogAnalyse
 *
 * The days where there was no activity at all are excluded from the statistics by default.
 *
 * @package Moosh\Command\Generic\Report
 */
class LogAnalyse extends MooshCommand
{

    const DATE_FORMAT = "Y-m-d H:i:s";

    public function __construct()
    {
        parent::__construct('analyse', 'log');

        $this->addOption('f|from:',
            'from date in YYYYMMDDHHmm or YYYY-MM-DD HH:MM format'
        );
        $this->addOption('t|to:',
            'to date in YYYYMMDDHHmm or YYYY-MM-DD HH:MM format');
        $this->addOption('z|time-zone:',
            'timezone used to display the dates. Possible values on https://secure.php.net/manual/en/timezones.php.', "UTC");

    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_FULL_NO_ADMIN_CHECK;
    }

    /**
     * @param $datetime YYYYMMDDHHmm or YYYY-MM-DD HH:MM
     * @return \DateTime
     */
    private function parseDateTime(string $datetime, \DateTimeZone $timezone, $second = 0)
    {
        $datetime = str_replace('-', '', $datetime);
        $datetime = str_replace(' ', '', $datetime);
        $datetime = str_replace(':', '', $datetime);
        if (strlen($datetime) != 12) {
            cli_error("Wrong format for --from. Use YYYYMMDDHHmm or YYYY-MM-DD HH:MM");
        }
        // YYYYMMDD
        $date = substr($datetime, 0, 8);

        // HHmm
        $hour = substr($datetime, 8, 2);
        $minute = substr($datetime, 10, 2);

        $return = new \DateTime($date, $timezone);
        $return->setTime($hour, $minute, $second);

        return $return;
    }

    private function getRecordStats($from, $to)
    {
        global $DB;
        $return = array();
        // Number of entries in the log for given from-to range.
        $sql = "SELECT COUNT(*) AS 'count', MIN(id) AS 'min', MAX(id) AS 'max'
                   FROM {logstore_standard_log}
				  WHERE timecreated >= ? AND timecreated < ?";
        $record = $DB->get_record_sql($sql, array($from, $to));
        $return['min'] = $record->min;
        $return['max'] = $record->max;
        $return['count'] = $record->count;

        return $return;
    }

    private function getTargetStats($from, $to)
    {
        global $DB;

        $return = array();
        // Get number of actions per user.
        $sql = "SELECT target, COUNT(*) AS 'count' 
                   FROM {logstore_standard_log}
				  WHERE timecreated >= ? AND timecreated < ?
				  GROUP BY target";
        $records = $DB->get_records_sql($sql, array($from, $to));
        foreach ($records as $record) {
            $return[$record->target] = [];
            $return[$record->target]['count'] = $record->count;
            $return[$record->target]['target'] = $record->target;
        }

        // sort array by count descending
        usort($return, function ($a, $b) {
            return $b['count'] - $a['count'];
        });


        return $return;
    }

    private function getUserStats($from, $to)
    {
        global $DB;

        $return = array();
        // Get number of actions per user.
        $sql = "SELECT userid, COUNT(*) AS 'count' 
                   FROM {logstore_standard_log}
				  WHERE timecreated >= ? AND timecreated < ?
				  GROUP BY userid";
        $records = $DB->get_records_sql($sql, array($from, $to));
        foreach ($records as $record) {
            $return[$record->userid] = [];
            $return[$record->userid]['count'] = $record->count;
            $return[$record->userid]['userid'] = $record->userid;
        }

        // sort array by count descending
        usort($return, function ($a, $b) {
            return $b['count'] - $a['count'];
        });


        return $return;
    }

    private function getCourseStats($from, $to)
    {
        global $DB;

        $return = array();
        // Get number of actions per course.
        $sql = "SELECT CONCAT(courseid,'-', crud) AS 'id',courseid, crud, COUNT(*) AS 'count' 
                   FROM {logstore_standard_log}
				  WHERE timecreated >= ? AND timecreated < ?
				  GROUP BY courseid,crud";
        $records = $DB->get_records_sql($sql, array($from, $to));
        foreach ($records as $record) {
            if (!isset($return[$record->courseid])) {
                $return[$record->courseid] = [];
                $return[$record->courseid]['courseid'] = $record->courseid;
            }
            $return[$record->courseid]['count-' . $record->crud] = $record->count;
        }
        foreach ($return as $k => $record) {
            $return[$k]['count'] = 0;
            if (isset($record['count-c'])) {
                $return[$k]['count'] += $record['count-c'];
            }
            if (isset($record['count-r'])) {
                $return[$k]['count'] += $record['count-r'];
            }
            if (isset($record['count-u'])) {
                $return[$k]['count'] += $record['count-u'];
            }
            if (isset($record['count-d'])) {
                $return[$k]['count'] += $record['count-d'];
            }
        }
        // sort array by count descending
        usort($return, function ($a, $b) {
            return $b['count'] - $a['count'];
        });
        return $return;
    }

    // SELECT * FROM `mdl_logstore_standard_log` WHERE timecreated >= 1693478040 AND timecreated < 1693478100 ORDER BY id ASC;

    /**
     * Timestamps with out of order ID in Moodle log table.
     * @param $from
     * @param $to
     * @return array
     * @throws \dml_exception
     */
    private function analyseIdOrdering($from, $to)
    {
        global $DB;
        // logstore_standard_log
        $sql = "SELECT id, timecreated 
                   FROM log
				  WHERE timecreated >= ? AND timecreated < ?
				  ORDER BY id ASC";
        $records = $DB->get_recordset_sql($sql, array($from, $to), 0, 9000000);
        $return = array();
        $previousId = 0;
        $previousTime = 0;
        foreach ($records as $record) {
            if ($previousTime < $record->timecreated) {
                $previousTime = $record->timecreated;
            }
            if ($previousTime > $record->timecreated + 200) {
                echo "Time decreasing: {$previousId} -> {$record->id}: $previousTime -> {$record->timecreated} (diff ". ($previousTime - $record->timecreated) .")\n";
                $previousTime = $record->timecreated;
                if (!isset($return[$record->timecreated])) {
//                    $return[$record->timecreated] = 0;
                }
//                $return[$record->timecreated]++;
            }
            $previousId = $record->id;
        }
        $records->close();
        ksort($return);
        return $return;
    }

    public function execute()
    {
        global $DB, $CFG;
        define('CLI_SCRIPT', true);
        define('PHPUNIT_TEST', false);

        $options = $this->expandedOptions;

        $timezone = new \DateTimeZone($options['time-zone']);

        // If from not given then auto-detect the oldest entry in the DB
        if (!$options['from']) {
            $fromdb = $DB->get_record_sql('SELECT timecreated FROM {logstore_standard_log} ORDER BY timecreated ASC LIMIT 1');
            $from = new \DateTime('@' . $fromdb->timecreated, $timezone);
            if ($this->verbose) {
                echo "Option --from not provided, the earliest timestamp found in mdl_logstore_standard_log is " . $fromdb->timecreated . "\n";
            }
        } else {
            $from = $this->parseDateTime($options['from'], $timezone);
        }

        if (!$options['from']) {
            $todb = $DB->get_record_sql('SELECT timecreated FROM {logstore_standard_log} ORDER BY timecreated DESC LIMIT 1');
            $to = new \DateTime('@' . $todb->timecreated, $timezone);
            if ($this->verbose) {
                echo "Option --to not provided, the latest timestamp found in mdl_logstore_standard_log is " . $todb->timecreated . "\n";
            }
        } else {
            $to = $this->parseDateTime($options['to'], $timezone, 59);
        }

        if ($this->verbose) {
            echo "From " . $from->format(self::DATE_FORMAT) . ' [' . $from->getTimestamp() . "]\n";
            echo "To " . $to->format(self::DATE_FORMAT) . ' [' . $to->getTimestamp() . "]\n";
        }
        if ($to < $from) {
            cli_error('"to date" must be later than "from date".');
        }
        $timestamps = $this->analyseIdOrdering($from->getTimestamp(), $to->getTimestamp());
	
	// Split timestamps into 60-second intervals.
        $range = array();
        $current = $from->getTimestamp();
        $end = $to->getTimestamp();
        while ($current <= $end) {
            $range[$current] = [];
            $current += 60;
        }
        $timezone = new \DateTimeZone('Europe/Dublin');
        $targetStats = [];
        foreach ($range as $start => $value) {
            $end = $start + 60;

            $startdt = new \DateTime();
            $startdt->setTimestamp($start);
            $startdt->setTimezone($timezone);
            $enddt = new \DateTime();
            $enddt->setTimestamp($end);
            $enddt->setTimezone($timezone);

            echo "From " . $startdt->format(self::DATE_FORMAT) . ' [' . $start . "]\n";
            echo "To " . $enddt->format(self::DATE_FORMAT) . ' [' . $end . "]\n";

            $stats = $this->getRecordStats($start, $end);
            echo "\tRecords: " . $stats['count'] . " (id " . $stats['min'] . ' - ' . $stats['max'] . ")\n";

            echo "\tPer target:\n";
            $stats = $this->getTargetStats($start, $end);
            foreach ($stats as $stat) {
                echo "\t\t" . $stat['target'] . ': ' . $stat['count'] . "\n";
            }

            // Export as CSV only starting minute and number of webservice_login calls.
            $targetStats[$startdt->format('H:i')] = 0;
            foreach ($stats as $stat) {
                if ($stat['target'] == 'webservice_login') {
                    $targetStats[$startdt->format('H:i')] = $stat['count'];
                }
            }

            echo "\tPer user:\n";
            $stats = $this->getUserStats($start, $end);
            foreach ($stats as $stat) {
                echo "\t\t" . $stat['userid'] . ': ' . $stat['count'] . "\n";
            }

            echo "\tPer course:\n";
            $stats = $this->getCourseStats($start, $end);
            foreach ($stats as $stat) {
                echo "\t\t" . $stat['courseid'] . ': ' . $stat['count'] . " ";
                if (isset($stat['count-c'])) {
                    echo "C:" . $stat['count-c'] . " ";
                }
                if (isset($stat['count-r'])) {
                    echo "R:" . $stat['count-r'] . " ";
                }
                if (isset($stat['count-u'])) {
                    echo "U:" . $stat['count-u'] . " ";
                }
                if (isset($stat['count-d'])) {
                    echo "D:" . $stat['count-d'] . " ";
                }
                echo "\n";
            }

            // Show number of webservice_login calls
        }
//        foreach ($targetStats as $hour=>$targetStat) {
//            file_put_contents('targetStats.csv', "$hour,$targetStat\n", FILE_APPEND);
//        }

    }
}
