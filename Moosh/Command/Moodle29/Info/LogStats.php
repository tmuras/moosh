<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle29\Info;

use Moosh\MooshCommand;

class LogStats extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('stats', 'log');

        $this->addOption('f|from:',
            'from date in YYYYMMDD or YYYY-MM-DD format (default is 30 days backwards)',
            '-30 days'
        );
        $this->addOption('t|to:',
            'to date in YYYYMMDD or YYYY-MM-DD format (default is today)');
        $this->addOption('p|period:', 'period of time in minutes', 5);

    }

    public function execute()
    {
        // Some variables you may want to use
        //  $this->cwd - the directory where moosh command was executed
        //  $this->mooshDir - moosh installation directory
        //  $this->expandedOptions - commandline provided options, merged with defaults
        //  $this->topDir - top Moodle directory
        //  $this->arguments[0] - first argument passed
        //  $this->pluginInfo - array with information about the current plugin (based on cwd), keys:'type','name','dir'
        //  $this->verbose - if set to true, then "moosh -v" was run - add more verbose / debug information


        /* if verbose mode was requested, show some more information/debug messages
        if($this->verbose) {
            echo "Say what you're doing now";
        }
        */

        global $DB;

        $options = $this->expandedOptions;


        $fromdate = strtotime($options['from']);
        if ($options['to']) {
            $todate = strtotime($options['to']);
        } else {
            $todate = time();
        }

        if ($fromdate === false) {
            cli_error('invalid from date');
        }

        if ($todate === false) {
            cli_error('invalid to date');
        }

        if ($todate < $fromdate) {
            cli_error('to date must be higher than from date');
        }

        $period = $period = $options['period'];
        $fromdate = new \DateTime("@$fromdate");
        $todate = new \DateTime("@$todate");
        $sql = "SELECT * FROM {logstore_standard_log} WHERE timecreated > ? AND timecreated < ? AND origin = 'web'";
        $all = $DB->get_records_sql($sql, array($fromdate->getTimestamp(), $todate->getTimestamp()));
        if ($this->verbose) {
            echo count($all) . " records from " . $fromdate->format(\DATE_RFC1036) . " to " . $todate->format(\DATE_RFC1036) . "\n";
        }

        $count = array();
        foreach ($all as $log) {
            $date = new \DateTime("@" . $log->timecreated);
            $doy = $date->format('z');
            $doy = $date->format('Y-n-j');
            $dow = $date->format('w');
            $h = $date->format('G');

            if (!isset($count[$doy][$h])) {
                $count[$doy][$h] = 1;
            } else {
                $count[$doy][$h]++;
            }
            /*
            if($doy == 162) {
                echo $date->format(\DateTime::RFC1036);
                echo "\n";
                echo $date->getTimestamp();
                die();

            }
            */
        }
        $perdaymax = 0;
        $perhourmax = 0;
        foreach ($count as $day => $dataday) {
            $sum = array_sum($dataday);
            if ($sum > $perdaymax) {
                $perdaymax = $sum;
                $perdaymaxday = $day;
            }
        }
        //print_r($count);

        foreach ($count as $day => $dataday) {
            foreach ($dataday as $hour => $hourcount) {
                if ($hourcount > $perhourmax) {
                    $perhourmax = $sum;
                    $perhourmaxday = $day;
                    $perhourmaxhour = $hour;
                }
            }
        }

        echo "Maximum # of logs per day: $perdaymax on day $perdaymaxday\n";
        echo "Maximum # of logs per hour: $perhourmax on day $perhourmaxday, hour $perhourmaxhour\n";

        // Dump CSV
        foreach ($count as $day => $dataday) {
            for($hour = 0; $hour < 24; $hour++) {
                if(isset($dataday[$hour])) {
                    $hourcount = $dataday[$hour];
                } else {
                    $hourcount = 0;
                }
                echo "$day,$hour,$hourcount\n";
            }
        }
    }
}