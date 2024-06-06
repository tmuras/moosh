<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2024 fireartist
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Course;
use Moosh\MooshCommand;

class CourseEnableguest extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('enableguest', 'course');

        $this->addOption('k|key:', 'enrolment key - defaults to none');

        $this->addArgument('id');

        $this->maxArguments = 255;
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/course/lib.php';

        foreach ($this->arguments as $courseid) {
            $this->expandOptionsManually(array($courseid));
            $options = $this->expandedOptions;

            // get the details for the course
            $course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);

            // get the details of the guest enrolment plugin
            $plugin = enrol_get_plugin('guest');
            if(!$plugin){
                cli_error('could not find guest enrolment plugin');
            }

            // get the enrolment plugin instances for the course
            $instances = enrol_get_instances($courseid, false);

            // loop through the instances to find the instance ID for the guest-enrolment plugin
            $guestEnrolInstance = 0;
            foreach($instances as $instance){
                if($instance->enrol === 'guest'){
                    $guestEnrolInstance = $instance;
                    break;
                }
            }

            // if we didn't find an instance for the guest enrolment plugin then we need to add
            // one to the course
            if(!$guestEnrolInstance){
                // first try add an instance
                $plugin->add_default_instance($course);

                // then try retreive it
                $instances = enrol_get_instances($courseid, false);
                $guestEnrolInstance = 0;
                foreach($instances as $instance){
                    if($instance->enrol === 'guest'){
                        $guestEnrolInstance = $instance;
                        break;
                    }
                }

                // if we still didn't get an instance - give up
                if(!$guestEnrolInstance){
                    cli_error('failed to create instance of guest enrolment plugin');
                }
            }

            // activate self enrolment
            if ($guestEnrolInstance->status != ENROL_INSTANCE_ENABLED) {
                $plugin->update_status($guestEnrolInstance, ENROL_INSTANCE_ENABLED);
            }

            $plugin->update_instance($guestEnrolInstance, [
                // set the enrolment key (always do this so running without the -k option will blank a pre-existing key)
                'password' => $options['key'],
                'status' => ENROL_INSTANCE_ENABLED,
            ]);

            // set the enrolment key (always do this so running without the -k option will blank a pre-existing key)
            $instance_fromDB = $DB->get_record('enrol', array('courseid'=>$course->id, 'enrol'=>'guest', 'id'=>$guestEnrolInstance->id), '*', MUST_EXIST);
            $instance_fromDB->password = $options['key'];
            $DB->update_record('enrol', $instance_fromDB);
        }
    }
}
