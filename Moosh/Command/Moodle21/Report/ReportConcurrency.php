<?php
/**
 * moosh - Moodle Shell
 * TBA
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle21\Report;
use Moosh\MooshCommand;

class ReportConcurrency extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('concurrency', 'report');

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
    	global $DB, $CFG;

        $options = $this->expandedOptions;


        $from_date = strtotime($options['from']);
        if ($options['to']) {
            $to_date = strtotime($options['to']);
        }
        else {
            $to_date = time();
        }
        
        if ($from_date === false) {
            cli_error('invalid from date');
        }

        if ($to_date === false) {
            cli_error('invalid to date');
        }

        if ($to_date < $from_date) {
            cli_error('to date must be higher than from date');
        }

        $period = $period = $options['period'];

        /*
         * check which log is being used
         */
        // Get list of readers.
        $logmanager = get_log_manager();
        $readers = $logmanager->get_readers();
        $uselegacyreader = false;

        // Get preferred reader.
        if (!empty($readers)) {
            foreach ($readers as $readerpluginname => $reader) {
                // If legacy reader is preferred reader.
                if ($readerpluginname == 'logstore_legacy') {
                    $uselegacyreader = true;
                }
            }
        }

        /*
         * if using legacy log
         * else if not using legacy log
         */
        if($uselegacyreader)
        {
            //$sql = "SELECT ( FROM_UNIXTIME( period * ( 15 *60 ) ) ) AS Date,
            //    DAYNAME( FROM_UNIXTIME( period * ( 15 *60 ) ) ) AS  DAY ,
            //    DATE_FORMAT( FROM_UNIXTIME( period * ( 15 *60 ) ) , '%M %d, %Y' ) AS DayDate,
            //    TIME( FROM_UNIXTIME( period * ( 15 *60 ) ) ) AS Timecreated,
            //    online_users FROM
            //
            //    (SELECT ROUND( timecreated / ( 15 *60 ) ) AS period,
            //    COUNT( DISTINCT userid ) AS online_users
            //    FROM {log}
            //    GROUP BY period ) AS concurrent_users_report";
            //
            //$query = $DB->get_records_sql($sql);
            //foreach ($query as $k => $v) {
            //    echo $k . " users online: " . $v->online_users . "\n";
            //}

        } else {

            /*
             * get the number of concurrent users for each period (hidden)
             */
            $sql = "SELECT (FROM_UNIXTIME(period * ( $period*60 ))) AS Date,
                DAYNAME( FROM_UNIXTIME( period * ( 15 *60 ) ) ) AS DAY,
                DATE_FORMAT( FROM_UNIXTIME( period * ( 15 *60 ) ) , '%M %d, %Y' ) AS DayDate,
                TIME( FROM_UNIXTIME( period * ( 15 *60 ) ) ) AS Timecreated,
				online_users FROM 
				
				(SELECT ROUND( timecreated / ( $period*60 ) ) AS period,
				COUNT( DISTINCT userid ) AS online_users
				FROM {logstore_standard_log}
				GROUP BY period) AS concurrent_users_report";

            $query = $DB->get_records_sql($sql);
            foreach ($query as $k => $v) {
                //echo $k . " users online: " . $v->online_users . "\n";
            }
            /*
             * get the size of the database
             */
            $sql = "SELECT table_name AS 'Table',
                    ROUND(((data_length + index_length))) AS 'Size(B)'
                    FROM information_schema.TABLES
                    WHERE table_schema = '".$CFG->dbname."'
                ORDER BY (data_length + index_length) DESC";
            $results = $DB->get_records_sql($sql);

            $databasesize = 0;
            foreach ($results as $result) {
                $databasesize += $result->{'size(b)'};
            }

            echo "Database Size: ".round($databasesize / 1024 / 1024, 2) ." (MB)\n";
            /*
             * get active users
             */
            echo "Active Users: Not Defined\n";
            /*
             * get the max concurrent users during any period
             */
            $sql = "SELECT MAX( concurrent_users_report.online_users )
                FROM (SELECT ROUND( timecreated / ( $period*60 ) ) AS period,
				COUNT( DISTINCT userid ) AS online_users
				FROM {logstore_standard_log}
				GROUP BY period) AS concurrent_users_report";

            $results = $DB->get_records_sql($sql);
            $manconcurrentusers = 0;
            foreach ($results as $result) {
                $manconcurrentusers += $result->{'max( concurrent_users_report.online_users )'};
            }

            echo "Max Concurrent Users: ".$manconcurrentusers."\n";
            /*
             * Get the average concurrent users last 12 months
             */
            $todayminustwelvemonths =  strtotime('-1 years');
            $totalusersinpastyear = 0;
            $periodsoveryear = 0;

            $sql = "SELECT (FROM_UNIXTIME(period * ( $period*60 ))) AS Date,
                DAYNAME( FROM_UNIXTIME( period * ( 15 *60 ) ) ) AS DAY,
                DATE_FORMAT( FROM_UNIXTIME( period * ( 15 *60 ) ) , '%M %d, %Y' ) AS DayDate,
                TIME( FROM_UNIXTIME( period * ( 15 *60 ) ) ) AS Timecreated,
				online_users FROM 
				
				(SELECT ROUND( timecreated / ( $period*60 ) ) AS period,
				COUNT( DISTINCT userid ) AS online_users
				FROM {logstore_standard_log}
				GROUP BY period) AS concurrent_users_report";
            $results = $DB->get_records_sql($sql);
            foreach ($results as $result) {
                if(strtotime($result->date) > $todayminustwelvemonths) {
                    $totalusersinpastyear += $result->online_users;
                    $periodsoveryear++;
                }
            }
            echo "Average concurrent users past 12 months: ".($totalusersinpastyear/$periodsoveryear)."\n";
        }
    }
}
