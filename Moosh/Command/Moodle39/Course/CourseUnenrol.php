<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2013 Matteo Mosangini
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Course;

use Moosh\MooshCommand;
use course_enrolment_manager;

class CourseUnenrol extends MooshCommand {

    public function __construct() {
        parent::__construct('unenrol', 'course');

        $this->addArgument('courseid');
        $this->addArgument('userid');
        $this->minArguments = 2;
        $this->maxArguments = 255;
    }

    public function execute() {
        global $CFG, $DB, $PAGE;

        require_once($CFG->dirroot . '/enrol/locallib.php');
        require_once($CFG->dirroot . '/group/lib.php');

        $arguments = $this->arguments;

        $course = $DB->get_record('course', array('id' => $arguments[0]), '*', MUST_EXIST);
        $manager = new course_enrolment_manager($PAGE, $course);

        $userids = $this->arguments;
        array_shift($userids);

        foreach ($userids as $userid) {
            $enrolments = $manager->get_user_enrolments($userid);

            foreach ($enrolments as $enrolment) {
                list ($instance, $plugin) = $manager->get_user_enrolment_components($enrolment);
                if ($instance && $plugin && $plugin->allow_unenrol_user($instance, $enrolment)) {
                    $plugin->unenrol_user($instance, $userid);
                    echo "Succesfully unenroled user $userid\n";
                }
            }
        }
    }
}
