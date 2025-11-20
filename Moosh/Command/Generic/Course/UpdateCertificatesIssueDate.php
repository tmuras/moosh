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
 * @introduced 2025-11-19
 * @author     Bartosz Hornik
 */

namespace Moosh\Command\Generic\Course;
use Moosh\MooshCommand;

class UpdateCertificatesIssueDate extends MooshCommand {

    public function __construct() {
        parent::__construct('update-certificate', 'course');

        $this->addOption('c|courses:', 'update certificates from these courses only (comma separated)');
        $this->addOption('u|users:', 'update certificates for these users only (comma separated)');
    }

    /**
     * Execute the command to update certificates issue dates.
     */
    public function execute() {
        global $DB;

        $this->expandOptions();
        $options = $this->expandedOptions;

        $courses = explode(',', $options['courses'] ?? '');
        $courses = array_map('trim', $courses);

        $users = explode(',', $options['users'] ?? '');
        $users = array_map('trim', $users);

        $params = [];
        if (!empty($courses) && $courses[0] !== '') {
            list($courseinsql, $courseparams) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED, 'course');
            $courseinsql1 = ' and c.courseid ' . $courseinsql;
            $courseinsql2 = ' and courseid ' . $courseinsql;
            $params = array_merge($params, $courseparams);
        } else {
            $courseinsql2 = $courseinsql1 = '';
        }

        if (!empty($users) && $users[0] !== '') {
            list($userinsql, $userparams) = $DB->get_in_or_equal($users, SQL_PARAMS_NAMED, 'user');
            $userinsql1 = ' and c.userid ' . $userinsql;
            $userinsql2 = ' and userid ' . $userinsql;
            $params = array_merge($params, $userparams);
        } else {
            $userinsql2 = $userinsql1 = '';
        }

        $sql = "update {tool_certificate_issues} c
                left join {course_completions} cc on (cc.course = c.courseid and cc.userid = c.userid)
                set timecreated = (
                    select timecompleted from {course_completions} cc2
                    where cc2.userid = c.userid and cc2.course = c.courseid and cc2.timecompleted is not null
                    order by cc2.timecompleted desc limit 1
                )
                where cc.timecompleted is not null $courseinsql1 $userinsql1";

        $DB->execute($sql, $params);
        echo "Certificates issue dates updated successfully. Now regenerate the certificates PDFs. Please wait. \n";

        $ids = $DB->get_records_select('tool_certificate_issues', '1=1 '. $courseinsql2 . $userinsql2, $params);
        foreach($ids as $issue) {
            $template = \tool_certificate\template::instance($issue->templateid);
            $template->create_issue_file($issue, true);
        }

        echo "Certificates PDFs regenerated successfully. Count: ".count($ids)."\n";
        exit(0);
    }

    /**
     * Get help text for the command arguments.
     *
     * This method provides detailed help information about the command's arguments,
     * including examples of how to use the command effectively.
     *
     * @return string Help text for the command arguments.
     */
    protected function getArgumentsHelp() {
        $help = parent::getArgumentsHelp();
        $help .= "\n\n";
        $help .= "This command updates certificates issue date in tool_certificate_issues} DB table with the date of user course completions.\n";
        $help .= "You can provide the course(s) ID and/or user(s) ID comma seperated. Non of these params are manatory. If empty - means \"all:\".\n\n";
        $help .= "e.g. update certificates from courses with ID = 2 and 3 and for users with ID = 5, 6, or 7:\n";
        $help .= "    moosh -n course-update-certificate -c 2,3 -u 5,6,7 \n";
        $help .= "or updates all certificates in the DB:\n";
        $help .= "    moosh -n course-update-certificate \n";
        return $help;
    }
}