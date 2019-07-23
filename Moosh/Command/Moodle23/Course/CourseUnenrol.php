<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2013 Matteo Mosangini
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
        $this->addArgument('userid');
        $this->minArguments = 1;
        $this->maxArguments = 255;
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

            foreach ($instances as $instance) {
                $cohortPlugin->delete_instance($instance);
            }
        }

        $usersid = $this->arguments;
        array_shift($usersid);
        $users = array();

        if ($options['role'] && $usersid) {
            $rolesid = array();
            $roles = explode(',', $options['role']);
            foreach ($roles as $role) {
                $role = $DB->get_record('role', array('shortname' => $role));
                $rolesid[] = $role->id;
            }
            // this can be shortened by combining the two functions above
            // maybe?
            foreach($usersid as $userid) {
                foreach($rolesid as $singlerole) {
                    $record = $DB->get_record('role_assignments', array('roleid' => $singlerole, 'userid' => $userid, 'contextid' => $context->id));
                    if ($record) {
                        $users[] = $record;
                    }
                }
            }
            foreach ($users as $user) {
                $DB->delete_records('role_assignments', array('userid' => $user->userid, 'roleid' => $user->roleid, 'contextid' => $user->contextid));
                echo "Succesfully unenroled user $user->userid from role $user->roleid\n";
            }
            exit();
        } elseif ($usersid && !$options['role']) {
            foreach($usersid as $singleuser) {
                $user = $DB->get_record('user', array('id' => $singleuser));
                $users[] = $user;
            }
        } elseif ($options['role'] && !$usersid) {
            $roles = explode(',', $options['role']);
            foreach ($roles as $role) {
                $role = $DB->get_record('role', array('shortname' => $role));
                $users += get_role_users($role->id, $context);
            }
        } else {
            $allroles = get_all_roles();
            foreach ($allroles as $role) {
                $users += get_role_users($role->id, $context);
            }
        }

        #remove all enrolled removable users
        foreach ($users as $user) {
            $enrolments = $manager->get_user_enrolments($user->id);

            foreach ($enrolments as $enrolment) {
                list ($instance, $plugin) = $manager->get_user_enrolment_components($enrolment);
                if ($instance && $plugin && $plugin->allow_unenrol_user($instance, $enrolment)) {
                    $plugin->unenrol_user($instance, $enrolment->userid);
                    echo "Succesfully unenroled user $enrolment->userid\n";
                }
            }
        }
    }
}
