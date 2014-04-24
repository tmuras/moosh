<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2014 Manolescu Dorel - based on Matteo Mosangini CourseUnerol
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Course;

use Moosh\MooshCommand;
use context_course;
use course_enrolment_manager;

class CourseBulkUnenrolInactiveUsers extends MooshCommand {

    public function __construct() {
        parent::__construct('bulk_unenrol_inactive_users', 'course');

        $this->addOption('r|role:', 'role name like- manager, editingteacher, student...');

        $this->addArgument('courseid');
    }

    public function execute() {


        global $CFG, $DB, $PAGE;

        require_once($CFG->dirroot . '/enrol/locallib.php');
        require_once($CFG->dirroot . '/group/lib.php');

        $options = $this->expandedOptions;
        $arguments = $this->arguments;

        $course = $DB->get_record('course', array('id' => $arguments[0]), '*', MUST_EXIST);
        $context = context_course::instance($course->id);
        $manager = new course_enrolment_manager($PAGE, $course);

        #remove all enrolled users who never accessed the course
        try {
            foreach (explode(',', $options['role']) as $role) {
                if (!in_array($role, array('student','teacher', 'manager', 'editingteacher'))) {
                    exit('role is nor valid');
                }
                $role = $DB->get_record('role', array('shortname' => $role));
                $context = context_course::instance($course->id);
                $users = get_role_users($role->id, $context);
                #unenrol inactive users
                foreach ($users as $user) {
                    $lastaccessexists = false;
                    $lastaccessexists = $DB->record_exists('user_lastaccess', 
                                       array('courseid'=>$course->id, 'userid'=>$user->id));
                    if(!$lastaccessexists) {
		        $enrolments = $manager->get_user_enrolments($user->id);
		        foreach ($enrolments as $enrolment) {
		            list ($instance, $plugin) = $manager->get_user_enrolment_components($enrolment);
		            if ($instance && $plugin && $plugin->allow_unenrol_user($instance, $enrolment)) {
		                $plugin->unenrol_user($instance, $enrolment->userid);
		            }
		        }
                    }
                }
            }
        } catch (Exception $e) {
            print get_class($e) . " thrown within the exception handler. Message: " . $e->getMessage() . " on line " . $e->getLine();
        }
    }
}
