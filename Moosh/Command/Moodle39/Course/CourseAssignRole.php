<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2024 fireartist
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Course;

use Moosh\MooshCommand;
use course_enrolment_manager;

class CourseAssignRole extends MooshCommand {

    public function __construct() {
        parent::__construct('assign-role', 'course');

        $this->addOption('i|id', 'Use id to match a user');
        $this->addOption('plugin:', "Enrol plugin (e.g. 'manual', 'self')", 'manual');

        $this->addArgument('courseid');
        $this->addArgument('role');
        $this->addArgument('user');
        $this->minArguments = 3;
        $this->maxArguments = 255;
    }

    protected function getArgumentsHelp() {
        $help = parent::getArgumentsHelp();
        $help .= "\n\n";
        $help .= "<role> must be the shortname, e.g. 'editingteacher', 'student'.\n";
        $help .= "The user(s) must already be enrolled on the course with the selected --plugin.";

        return $help;
    }

    public function execute() {
        global $CFG, $DB, $PAGE;

        require_once($CFG->dirroot . '/enrol/locallib.php');
        require_once($CFG->dirroot . '/group/lib.php');

        $options = $this->expandedOptions;
        $users = $this->arguments;
        $courseid = array_shift($users);
        $rolename = array_shift($users);

        $role = $DB->get_record('role', array('shortname' => $rolename), '*', MUST_EXIST);
        if ($options['plugin'] == "manual") {
            $component = "";
        }
        else {
            $component = "enrol_" . $options['plugin'];
        }
        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
        $manager = new course_enrolment_manager($PAGE, $course);

        foreach ($users as $userid) {
            if (!$options['id']) {
                $userid = $DB->get_record('user',array('username'=>$userid), 'id', MUST_EXIST)->id;
            }
            $enrolments = $manager->get_user_enrolments($userid);
            $ok = False;
            foreach ($enrolments as $enrolment) {
                list ($instance, $plugin) = $manager->get_user_enrolment_components($enrolment);
                if ($instance->enrol != $options['plugin']) {
                    continue;
                }
                role_assign(
                    $role->id,
                    $userid,
                    \context_course::instance($course->id)->id,
                    $component
                );
                $ok = True;
                break;
            }
            if (!$ok) {
                printf("Did not find user enrolment of type '%s' for user '%d'\n", $options['plugin'], $userid);
            }
        }
    }
}
