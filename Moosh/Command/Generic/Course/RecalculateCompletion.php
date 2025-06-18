<?php
/**
 * Mark course completion record to be recalculated on the next cron job.
 * moosh recalculate-completion <courseid> <userids>
 *
 * Mark one course completion to be recalculated for user with id 8 in course with id 120
 * @example moosh recalculate-completion 120 8
 *
 * Mark more course completions to be recalculated for users with id: 2, 3, 4 in course with id 50
 * @example moosh recalculate-completion 50 2,3,4
 *
 * Mark all course completions to be recalculated in course with id 50
 * @example moosh recalculate-completion 50 all
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @introduced 2025-06-11
 * @author     Bartosz Hornik
 */

namespace Moosh\Command\Generic\Course;
use Moosh\MooshCommand;

class RecalculateCompletion extends MooshCommand {

    public function __construct() {
        parent::__construct('recalculate-completion', 'course');

        $this->addArgument('courseid');
        $this->addArgument('userids');
    }

    public function execute() {
        global $DB;

        $courseid = $this->arguments[0];
        $userids = $this->arguments[1];

        if ($DB->record_exists('course', ['id' => $courseid])) {
            if ($DB->record_exists('course_completion_criteria', ['course' => $courseid])) {
                if ($userids == 'all') {
                    $sql = "UPDATE {course_completions} SET reaggregate = 1 WHERE course = ?";
                    $DB->execute($sql, [$courseid]);

                    echo "All completion records marked to be recalculated for course $courseid".PHP_EOL;
                } else {
                    $userids = explode(',', $this->arguments[1]);
                    if (!empty($userids)) {
                        $sql = "UPDATE {course_completions} SET reaggregate = 1 WHERE course = ? AND userid = ?";

                        foreach($userids as $userid) {
                            $userid = trim($userid);
                            if ($DB->record_exists('course_completions', ['course' => $courseid, 'userid' => $userid])) {
                                $DB->execute($sql, [$courseid, $userid]);
                                echo "Completion record marked to be recalculated for course $courseid and user $userid".PHP_EOL;
                            } else {
                                cli_error("Missing course completion record for course with ID $courseid and user with id $userid");
                            }
                        }
                    } else {
                        cli_error("User list not provided. Example: moosh recalculate-completion $courseid 2,3,4");
                    }
                }
            } else {
                cli_error("Course with ID $courseid does not have any completion criteria");
            }
        } else {
            cli_error("Course with ID $courseid not found");
        }
        exit(0);
    }
}