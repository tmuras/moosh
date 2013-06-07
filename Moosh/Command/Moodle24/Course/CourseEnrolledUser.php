<?php
/**
 * Enable erol method(s) in a course.
 * moosh course-enrolleduser
 *      enrol-role courseid
 *
 * @copyright  2013 onwards Mirko Otto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class CourseEnrolledUser extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('enrolleduser', 'course');
        
        $this->addArgument('role');
        $this->addArgument('courseid');
        //$this->minArguments = 0;
        $this->maxArguments = 255;
    }

    public function execute()
    {
        
        global $CFG, $DB;

        require_once($CFG->dirroot . '/enrol/locallib.php');

        $options = $this->expandedOptions;
        $arguments = $this->arguments;

        //print_r($arguments);
        
        try {
            $course = $DB->get_record('course', array('id' => $arguments[1]), '*', MUST_EXIST);
            $role = $DB->get_record('role', array('shortname' => $arguments[0]));
            $context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);
            $users = get_role_users($role->id , $context);
            //$count = count($users);
            //echo "count role_assigns: " . $count . "\n";
            if ($users) {
                foreach ($users as $user) {
                    //print_r($user);
                    echo $user->id . "\n";
                }
            }        
        } catch (Exception $e) {
            print get_class($e)." thrown within the exception handler. Message: ".$e->getMessage()." on line ".$e->getLine();
        }
        return(0);
    }
} 
