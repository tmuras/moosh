<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Course;
use Moosh\MooshCommand;

class CourseEnableselfenrol extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('enableselfenrol', 'course');

        $this->addOption('k|key:', 'enrolment key - defaults to none');

        $this->addOption('r|roleid:', 'numerical id of the role to assign to users');

        $this->addOption('n|name:', 'custom name for the self enrolment instance');

        $this->addOption(
            's|sendmessage:',
            'send welcome message to new users - accepts integer values:
             0 - Do not send the welcome email.
             1 - Send welcome email from course contact.
             2 - Send welcome email from course key holder.
             3 - Send welcome email from no reply.',
            '__NOT_SET__'
        );

        $this->addOption('m|message:', 'welcome message to new users');

        $this->addArgument('id');

        $this->maxArguments = 255;
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/course/lib.php';

        foreach ($this->arguments as $argument) {
            $this->expandOptionsManually(array($argument));
            $options = $this->expandedOptions;

            // retrieve and validate the role id if provided
            $roleid = null;
            if ($options['roleid']) {
                $roleidInput = trim($options['roleid']);
                if (!ctype_digit($roleidInput)) {
                    throw new \Exception('The --roleid option must be an integer.');
                }
                $roleid = intval($roleidInput);
                if (!$DB->record_exists('role', array('id' => $roleid))) {
                    throw new \Exception('Invalid role id provided.');
                }
            }

            // retrieve and validate the send message option if provided
            if ($options['sendmessage'] !== '__NOT_SET__') {
                $sendMessageInput = trim($options['sendmessage']);
                if (!ctype_digit($sendMessageInput)) {
                    throw new \Exception('The --sendmessage option must be an integer between 0 and 3.');
                }
                $sendMessage = intval($sendMessageInput);
                if (!in_array($sendMessage, [0, 1, 2, 3], true)) {
                    throw new \Exception('Invalid value for --sendmessage. Allowed values are 0, 1, 2, or 3.');
                }
            } else {
                $sendMessage = null;
            }

            // get the details for the course
            $course = $DB->get_record('course', array('id'=>$argument), '*', MUST_EXIST);

            // get the details of the self enrolment plugin
            $plugin = enrol_get_plugin('self');
            if(!$plugin){
                throw new \Exception('could not find self enrolment plugin');
            }

            // get the enrolment plugin instances for the course
            $instances = enrol_get_instances($course->id, false);

            // loop through the instances to find the instance ID for the self-enrolment plugin
            $selfEnrolInstance = 0;
            foreach($instances as $instance){
                if($instance->enrol === 'self'){
                    $selfEnrolInstance = $instance;
                    break;
                }
            }

            // if we didn't find an instance for the self enrolment plugin then we need to add
            // one to the course
            if(!$selfEnrolInstance){
                // first try add an instance
                $plugin->add_default_instance($course);

                // then try retreive it
                $instances = enrol_get_instances($course->id, false);
                $selfEnrolInstance = 0;
                foreach($instances as $instance){
                    if($instance->enrol === 'self'){
                        $selfEnrolInstance = $instance;
                        break;
                    }
                }

                // if we still didn't get an instance - give up
                if(!$selfEnrolInstance){
                    throw new \Exception('failed to create instance of self enrolment plugin');
                }
            }

            // activate self enrolment
            if ($selfEnrolInstance->status != ENROL_INSTANCE_ENABLED) {
                $plugin->update_status($selfEnrolInstance, ENROL_INSTANCE_ENABLED);
            }

            // set the enrolment key (always do this so running without the -k option will blank a pre-existing key)
            $instance_fromDB = $DB->get_record('enrol', array('courseid'=>$course->id, 'enrol'=>'self', 'id'=>$selfEnrolInstance->id), '*', MUST_EXIST);
            $instance_fromDB->password = $options['key'];

            // set the role if provided
            if ($roleid) {
                $instance_fromDB->roleid = $roleid;
            }

            // set the custom name if provided
            if ($options['name']) {
                $instance_fromDB->name = $options['name'];
            }

            // set the send message option if provided
            if ($sendMessage !== null) {
                $instance_fromDB->customint4 = $sendMessage;
            }

            // set the welcome message if provided
            if ($options['message']) {
                $instance_fromDB->customtext1 = $options['message'];
            }

            $DB->update_record('enrol', $instance_fromDB);
        }
    }
}
