<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2025 Aleksander Rubacha
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle45\Course;

use Moosh\MooshCommand;

class TopCourses extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('top', 'course');

        // Define option -n for limit
        $this->addOption('n|number:', 'Number of top courses to identify (default: 3)');
        
        // Define option -t for displaying hits count
        $this->addOption('t|total', 'Show total hits count next to course ID');
    }

    public function execute()
    {
        global $DB;

        $this->expandOptions();

        // 1. Handle arguments
        $limit = $this->expandedOptions['number'] ?? 3;
        $showHits = isset($this->expandedOptions['total']);
        
        $limit = intval($limit);
        if ($limit < 1) {
            $limit = 3;
        }

        // 2. Calculate timestamp
        $oneMonthAgo = time() - (30 * 24 * 60 * 60);

        // 3. SQL Query
        $sql = "SELECT courseid, COUNT(*) AS hits
                FROM {logstore_standard_log}
                WHERE timecreated > :timecreated
                AND courseid <> 1
                AND courseid <> 0
                AND eventname = :eventname
                GROUP BY courseid
                ORDER BY hits DESC";

        $params = [
            'timecreated' => $oneMonthAgo,
            'eventname'   => '\core\event\course_viewed'
        ];

        // 4. Fetch records
        $records = $DB->get_records_sql($sql, $params, 0, $limit);

        // 5. Output
        if ($records) {
            foreach ($records as $record) {
                if ($showHits) {
                    echo $record->courseid . " " . $record->hits . "\n";
                } else {
                    echo $record->courseid . "\n";
                }
            }
        }
    }
}