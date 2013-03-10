<?php
/**
 * Enable erol method(s) in a course.
 * moosh course-enrol-method-enable
 *      -i --id
 *      -r --role
 *      courseid enrolmethod1 [<enrolmethod2> ...]
 *
 * @copyright  2013 onwards Mirko Otto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class CourseSwapUserRole extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('swapuserrole', 'course');
        $this->addOption('i|id', 'use numeric IDs instead of user name(s)');
        $this->addOption('r|enrole:', 'enrole method name');
        $this->addOption('m|maxenrol:', 'max enrolled user');

        //possible other options
        //duration
        //startdate
        //recovergrades

        $this->addArgument('courseid');
        $this->addArgument('enrolmethod');
        $this->addArgument('maxenrol');
        $this->maxArguments = 255;
    }

    public function execute()
    {

        echo "asdf" . "\n";
        global $CFG, $DB, $PAGE, $USER;

        require_once($CFG->dirroot . '/enrol/externallib.php');
        require_once($CFG->dirroot . '/enrol/locallib.php');
        //require_once($CFG->dirroot . '/group/lib.php');


        $instances = $DB->get_records('enrol', array('enrol' => 'waitlist', 'courseid' => 24));
        //$instances = $DB->get_records('enrol', array('enrol' => 'waitlist'));
        if($instances) {
            foreach($instances as $instance) {
                echo "asdf" . "\n";
            }
        }

        
        $courses = $DB->get_records('course', array('category' => 5));
        if($courses) {
            foreach($courses as $cour) {
                echo $cour->category . " " . $cour->id . "\n";
            }
        }



        return;

        $options = $this->expandedOptions;
        $arguments = $this->arguments;


        $course = $DB->get_record('course', array('id' => $arguments[0]), '*', MUST_EXIST);
        $context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);
        $students = get_role_users(5, $context);
        //$students = get_enrolled_users($context);
        print_r($students);

        return ;

        //$enrolledusers = core_enrol_external::get_enrolled_users(2);
        //$enrolledusers = core_enrol_external::get_enrolled_users($course->id);

        // Set the required capabilities by the external function.
        //$context = context_course::instance($course->id);
        //$roleid = $this->assignUserCapability('moodle/course:viewparticipants', $context->id);
        //$this->assignUserCapability('moodle/user:viewdetails', $context->id, $roleid);
        // Enrol the users in the course.
        // We use the manual plugin.

        $enrolinstances = enrol_get_instances($course->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            //if ($courseenrolinstance->enrol == "manual") {
            //    $instance = $courseenrolinstance;
            //    break;
            print_r($courseenrolinstance->enrol);
            echo("ddd");
            //}
        }

    }
} 
