<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle45\Admin;

use Moosh\MooshCommand;

class CleanupOrphanedCourses extends MooshCommand {

    public function __construct() {
        parent::__construct('cleanup-orphaned-courses', 'admin');
        $this->addOption('e|execute', 'Actually delete the records');
        $this->addOption('y|yes', 'Skip confirmation prompt');
    }

    public function execute() {
        global $DB;

        $execute = !empty($this->expandedOptions['execute']);
        $skipconfirm = !empty($this->expandedOptions['yes']);

        $sql = "SELECT DISTINCT courseid FROM (
                    SELECT courseid FROM {grade_items}
                    UNION ALL 
                    SELECT course AS courseid FROM {course_modules}
                    UNION ALL 
                    SELECT course AS courseid FROM {course_completions}
                    UNION ALL 
                    SELECT courseid FROM {enrol}
                ) t
                WHERE courseid NOT IN (SELECT id FROM {course})
                ORDER BY courseid";

        $records = $DB->get_records_sql($sql);

        if (empty($records)) {
            cli_writeln("No orphaned courses found");
            return;
        }

        $courseids = array_keys($records);
        list($insql, $params) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);

        cli_writeln("Found orphaned course IDs: " . implode(', ', $courseids));
        cli_writeln("");

        $deletions = [
            'grade_grades_history' => "itemid IN (SELECT id FROM {grade_items} WHERE courseid $insql)",
            'grade_grades' => "itemid IN (SELECT id FROM {grade_items} WHERE courseid $insql)",
            'grade_items' => "courseid $insql",
            'course_modules_completion' => "coursemoduleid IN (SELECT id FROM {course_modules} WHERE course $insql)",
            'course_modules' => "course $insql",
            'course_completions' => "course $insql",
            'course_completion_criteria' => "course $insql",
            'user_enrolments' => "enrolid IN (SELECT id FROM {enrol} WHERE courseid $insql)",
            'enrol' => "courseid $insql",
            'course_sections' => "course $insql",
            'course_format_options' => "courseid $insql",
            'user_lastaccess' => "courseid $insql",
            'event' => "courseid $insql",
            'context' => "contextlevel = 50 AND instanceid $insql"
        ];

        $total = 0;
        foreach ($deletions as $table => $where) {
            $count = $DB->count_records_sql("SELECT COUNT(*) FROM {{$table}} WHERE {$where}", $params);
            if ($count > 0) {
                cli_writeln(sprintf("%-35s %d", $table, $count));
                $total += $count;
            }
        }

        cli_writeln("");
        cli_writeln("Total records to delete: {$total}");

        if (!$execute) {
            cli_writeln("");
            cli_writeln("Dry run - nothing deleted. Use --execute to perform cleanup.");
            return;
        }

        if (!$skipconfirm) {
            cli_writeln("");
            cli_writeln("WARNING: This will permanently delete {$total} records!");
            $input = cli_input("Type 'yes' to continue: ");
            if (strtolower(trim($input)) !== 'yes') {
                cli_writeln("Aborted");
                return;
            }
        }

        cli_writeln("");
        cli_writeln("Executing cleanup...");

        $transaction = $DB->start_delegated_transaction();

        try {
            foreach ($deletions as $table => $where) {
                $DB->execute("DELETE FROM {{$table}} WHERE {$where}", $params);
            }

            $transaction->allow_commit();
            cli_writeln("Cleanup completed successfully");

        } catch (\Exception $e) {
            $transaction->rollback($e);
            cli_error("Cleanup failed: " . $e->getMessage());
        }
    }
}

