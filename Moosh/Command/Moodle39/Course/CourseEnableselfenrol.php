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
            $DB->update_record('enrol', $instance_fromDB);
        }
    }
}
