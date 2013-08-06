<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Course;

use Moosh\MooshCommand;
use context_course;
use course_enrolment_manager;

class CourseUnenrol extends MooshCommand {

    public function __construct() {
        parent::__construct('unenrol', 'course');

        $this->addOption('c|cohort:', 'unenrol all cohorts sync');
        $this->addOption('r|role:', 'roles');

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

        
        #remove all cohort sync
        if ($options['cohort'] == 1) {
            $plugins = $manager->get_enrolment_plugins();
            $instances = $DB->get_records('enrol', array('courseid' => $arguments[0], 'enrol' => 'cohort'), 'id ASC');

            if (!isset($plugins['cohort'])) {
                die("No cohort enrolment plugin for the course\n");
            }
            $cohortPlugin = $plugins['cohort'];

            try {
                foreach ($instances as $instance) {
                    $cohortPlugin->delete_instance($instance);
                }
            } catch (Exception $e) {
                print get_class($e) . " thrown within the exception handler. Message: " . $e->getMessage() . " on line " . $e->getLine();
            }
        }

        #remove all enrolled removable users
        try {
            foreach (explode(',', $options['role']) as $role) {
                $role = $DB->get_record('role', array('shortname' => $role));
                $context = context_course::instance($course->id);
                $users = get_role_users($role->id, $context);
                #unenrol 
                foreach ($users as $user) {
                    $enrolments = $manager->get_user_enrolments($user->id);
                        foreach ($enrolments as $enrolment) {
                        list ($instance, $plugin) = $manager->get_user_enrolment_components($enrolment);
                        if ($instance && $plugin && $plugin->allow_unenrol_user($instance, $enrolment)) {
                            $plugin->unenrol_user($instance, $enrolment->userid);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            print get_class($e) . " thrown within the exception handler. Message: " . $e->getMessage() . " on line " . $e->getLine();
        }
    }

}
