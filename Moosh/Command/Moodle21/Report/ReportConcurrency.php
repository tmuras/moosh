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
    	global $DB;

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

        $sql = "SELECT (FROM_UNIXTIME(period * ( $period*60 ))) AS time, 
				online_users FROM (SELECT ROUND( time / ( $period*60 ) ) AS period,
				COUNT( DISTINCT userid ) AS online_users
				FROM {log}
				WHERE action <> 'error' AND module <> 'library'
				AND time >= $from_date AND time <= $to_date
				GROUP BY period
				) AS concurrent_users_report";

        $query = $DB->get_records_sql($sql);
        foreach ($query as $k => $v) {
        	echo $k . " users online: " . $v->online_users . "\n";
        }
    }
}
