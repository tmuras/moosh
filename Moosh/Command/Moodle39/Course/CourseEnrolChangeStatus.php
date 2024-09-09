<?php
/**
 * moosh - Moodle Shell
 * @copyright 2021 unistra {@link http://unistra.fr}
 * @author 2021 Céline Perves <cperves@unistra.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Course;
use Moosh\MooshCommand;

class CourseEnrolChangeStatus extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('enrol-change-status', 'course');

        $this->addOption('i|instanceid:', 'enrolment instance id, if 0 or not entered you\'ll pass in interactive mode' , 0);
        $this->addOption('s|status:', 'status to be applied 1 for disable, 0 for enable, default value to 0',0);
        $this->addArgument('courseid');
        $this->maxArguments = 255;
    }

    function choice_value_check_and_prompt($promptmsg, $values){
        $var = trim(cli_input($promptmsg));
        if (!is_numeric($var) && ( in_array($var, $values))) {
            cli_writeln('Entered value must be an int in ('.implode(',', $values).')');
            return self::choice_value_check_and_prompt();
        }
        return (int)$var;
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/course/lib.php';
        $instanceid = $this->expandedOptions['instanceid'];
        $status = $this->expandedOptions['status'];
        $courseid = $this->arguments[0];
        if($courseid == 0) {
            cli_error('you must enter a valid courseid');
        }
        $course = $DB->get_record('course', array('id' => $courseid));
        if (!$course) {
            cli_error("course $courseid not found");
        }
        $instances = enrol_get_instances($course->id, false);
        if( $instanceid == 0) {
            // Enter in interactive mode
            cli_writeln('Interactive mode');
            $options = array();
            foreach ($instances as $instance) {
                $options[] = $instance->id;
                cli_writeln("$instance->id : $instance->name - $instance->enrol - "
                    .($instance->status == ENROL_INSTANCE_ENABLED ? "enabled" : "disabled"));
            }
            $instanceid = self::choice_value_check_and_prompt(
                "please choose what enrol method you want to enable/disable by typeing is instance id",
                $options
            );
            $status = self::choice_value_check_and_prompt(
                "enable ".ENROL_INSTANCE_ENABLED." or disable ".ENROL_INSTANCE_DISABLED,
                array(ENROL_INSTANCE_ENABLED,ENROL_INSTANCE_DISABLED));
            //$progress = new \progress_trace_buffer(new \text_progress_trace(), false);
            $plugin = enrol_get_plugin($instances[$instanceid]->enrol);
            $plugin->update_status($instances[$instanceid],$status);
            cli_writeln("instance $instanceid as be passed to ".($status == ENROL_INSTANCE_ENABLED? "enabled" : "disabled" ));
        } else {
            // non interactive mode change status by id
            $plugin = enrol_get_plugin($instances[$instanceid]->enrol);
            $plugin->update_status($instances[$instanceid],$status);
            cli_writeln("instance $instanceid as be passed to ".($status == ENROL_INSTANCE_ENABLED? "enabled" : "disabled" ));
        }
    }
    protected function getArgumentsHelp() {
        $help = parent::getArgumentsHelp();
        $help .= "\n\n";
        $help .= "Change enrol status";

        return $help;
    }
}
