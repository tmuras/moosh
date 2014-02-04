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

        $this->addOption('f|from:', 'from date');
        $this->addOption('t|to:', 'to date');
        $this->addOption('p|period:', 'period of time');
    }

    public function execute()
    {
    	global $DB;

        $options = $this->expandedOptions;

        if($options['from']) {
        	echo $options['from'];
        }
        
        if ($options['to']) {
        	echo $options['to'];
        }

        $period = 5;
        if ($options['period']) {
        	$period = $options['period'];
        }

        $sql = "SELECT (FROM_UNIXTIME(period * ( $period*60 ))) AS time, 
				online_users FROM (SELECT ROUND( time / ( $period*60 ) ) AS period,
				COUNT( DISTINCT userid ) AS online_users
				FROM mdl_log
				GROUP BY period
				) AS concurrent_users_report";

        $query = $DB->get_records_sql($sql);
        foreach ($query as $k => $v) {
        	echo $k . " users online: " . $v->online_users . "\n";
        }
    }
}
