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
class CourseEnrolmethod extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('enrolmethod', 'course');
        $this->addOption('i|id', 'use numeric IDs instead of user name(s)');
        $this->addOption('r|enrole:', 'enrole name');

        //possible other options
        //duration
        //startdate
        //recovergrades

        $this->addArgument('courseid');
        $this->addArgument('enrolm');
        $this->maxArguments = 255;
    }

    public function execute()
    {

        echo "aljadjad" . "\n";
        global $CFG, $DB, $PAGE;

        require_once($CFG->dirroot . '/enrol/locallib.php');
        require_once($CFG->dirroot . '/group/lib.php');

        $options = $this->expandedOptions;
        $arguments = $this->arguments;

        //find role id for given role
//        $role = $DB->get_record('role', array('shortname' => $options['role']), '*', MUST_EXIST);

        $course = $DB->get_record('course', array('id' => $arguments[0]), '*', MUST_EXIST);
        $context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);
        $manager = new course_enrolment_manager($PAGE, $course);

        $instances = $manager->get_enrolment_instances();
        //find the manual one
        foreach ($instances as $instance) {
            //print_r("cvb");
            echo $instance->enrol . "\n";
        }


        //$maninstance1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $instance1 = $DB->get_record('enrol', array('courseid'=>$course->id, 'enrol'=>'self'), '*', MUST_EXIST);
        //$instance1->expirythreshold = 60*60*24*4;
        //$instance1->expirynotify    = 1;
        //$instance1->notifyall       = 1;
        $instance1->status          = ENROL_INSTANCE_ENABLED;
        $DB->update_record('enrol', $instance1);


        /*
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

        $today = time();
        $today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);

        array_shift($arguments);
        foreach ($arguments as $argument) {
            if ($options['id']) {
                $user = $DB->get_record('user', array('id' => $argument), '*', MUST_EXIST);
            } else {
                $user = $DB->get_record('user', array('username' => $argument), '*', MUST_EXIST);
            }
            if(!$user) {
                cli_problem("User '$user' not found");
                continue;
            }
            $plugin->enrol_user($instance, $user->id, $role->id, $today, 0);
        }
        */
    }

}
