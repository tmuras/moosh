<?php
/**
 * Enrol user(s) in a course. Uses manual enrollment plugin.
 * moosh course-enrol
 *      -i --id
 *      -r --role
 *      -S --startdate
 *      -E --enddate
 *      courseid username1 [<username2> ...]
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Course;

use Moosh\MooshCommand;
use context_course;
use course_enrolment_manager;

class CourseEnrol extends MooshCommand {
    public function __construct() {
        parent::__construct('enrol', 'course');
        $this->addOption('i|id', 'use numeric IDs instead of user name(s)');
        $this->addOption('s|shortname', 'use course short name instead of course ID as first argument');
        $this->addOption('r|role:', 'role short name');
	$this->addOption('S|startdate:', 'any date php strtotime can parse');
	$this->addOption('E|enddate:', 'any date php strtotime can parse, or duration in # of days');

        //possible other options
        //recovergrades

        $this->addArgument('courseid');
        $this->addArgument('username');
        $this->maxArguments = 255;
    }

    public function execute() {
        global $CFG, $DB, $PAGE;

        require_once($CFG->dirroot . '/enrol/locallib.php');
        require_once($CFG->dirroot . '/group/lib.php');

        $options = $this->expandedOptions;
        $arguments = $this->arguments;

        //find role id for given role
        $role = $DB->get_record('role', array('shortname' => $options['role']), '*', MUST_EXIST);

        if ($options['shortname']) {
            $course = $DB->get_record('course', array('shortname' => $arguments[0]), '*', MUST_EXIST);
        } else {
            $course = $DB->get_record('course', array('id' => $arguments[0]), '*', MUST_EXIST);
        }

        $context = context_course::instance($course->id);
        $manager = new course_enrolment_manager($PAGE, $course);

        $instances = $manager->get_enrolment_instances();
        //find the manual one
        foreach ($instances as $instance) {
            if ($instance->enrol == 'manual') {
                break;
            }
        }

        if ($instance->enrol != 'manual') {
            die("No manual enrolment instance for the course\n");
        }

        $plugins = $manager->get_enrolment_plugins();

        //only one manual enrolment in a course
        if (!isset($plugins['manual'])) {
            die("No manual enrolment plugin for the course\n");
        }
        $plugin = $plugins['manual'];
	
	$startdate = $options['startdate'] ? strtotime($options['startdate']) : time();
	if ($startdate === false) {
            cli_error('invalid start date');
        }
	$startdate = make_timestamp(date('Y', $startdate), date('m', $startdate), date('d', $startdate), date('H', $startdate), date('i', $startdate), date('s', $startdate));
				
	if($options['enddate']) {
		if(strtotime($options['enddate'])) {
			$enddate = strtotime($options['enddate']);
			$enddate = make_timestamp(date('Y', $enddate), date('m', $enddate), date('d', $enddate), date('H', $enddate), date('i', $enddate), date('s', $enddate));
		} else if(preg_match("/^[1-9]\d*$/", $options['enddate'])) {
			$enddate = $startdate + ($options['enddate'] * 86400);
		} else {
			cli_error('invalid end date or duration');
		}
	} else {
		$enddate = 0;
	}
		
	if ($enddate === false) {
		cli_error('invalid end date');
	}
		
        if ($enddate != 0 && $enddate < $startdate) {
            cli_error('end date date must be higher than start date');
        }
		
        array_shift($arguments);
        foreach ($arguments as $argument) {
            if ($options['id']) {
                $user = $DB->get_record('user', array('id' => $argument), '*', MUST_EXIST);
            } else {
                $user = $DB->get_record('user', array('username' => $argument), '*', MUST_EXIST);
            }
            if (!$user) {
                cli_problem("User '$user' not found");
                continue;
            }
            $plugin->enrol_user($instance, $user->id, $role->id, $startdate, $enddate);
        }
    }

}
