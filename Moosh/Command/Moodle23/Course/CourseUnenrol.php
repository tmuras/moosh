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

class CourseUnenrol extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('unenrol', 'course');
        
        $this->addOption('a|all:', 'unenrol everyone');
        
        $this->addArgument('courseid');
        
        
    }

    public function execute()
            
    {
        
        
        global $CFG, $DB, $PAGE;

        require_once($CFG->dirroot . '/enrol/locallib.php');
        require_once($CFG->dirroot . '/group/lib.php');

        $options = $this->expandedOptions;
        $arguments = $this->arguments;
        
        
        
        
        $course = $DB->get_record('course', array('id' => $arguments[0]), '*', MUST_EXIST);
        $context = context_course::instance($course->id);
        $manager = new course_enrolment_manager($PAGE, $course);
        $instances = $manager->get_enrolment_instances();
        

        
        print $options['all'];
        
        foreach ($instances as $instance) {
            print $instance->enrol."\n";
        }
        
        try{
            $course = $DB->get_record('course', array('id' => $arguments[1]), '*', MUST_EXIST);
            $role = $DB->get_record('role', array('shortname' => 'editingteacher'));
            $context = context_course::instance($course->id);
            $users = get_role_users($role->id, $context);
            foreach ($users as $user) {
                    print_r($user);
                    #echo $user->id . "\n";
                }
            
            
        }catch (Exception $e) {
            print get_class($e)." thrown within the exception handler. Message: ".$e->getMessage()." on line ".$e->getLine();
        }
        
        //some variables you may want to use
        //$this->cwd - the directory where moosh command was executed
        //$this->mooshDir - moosh installation directory
        //$this->expandedOptions - commandline provided options, merged with defaults
        //$this->topDir - top Moodle directory
        //$this->arguments[0] - first argument passed

        //$options = $this->expandedOptions;

        /* if verbose mode was requested, show some more information/debug messages
        if($this->verbose) {
            echo "Say what you're doing now";
        }
        */
    }
}
