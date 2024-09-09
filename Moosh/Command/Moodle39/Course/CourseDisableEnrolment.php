<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2024 fireartist
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Course;
use Moosh\MooshCommand;

class CourseDisableEnrolment extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('disable-enrolment', 'course');

        $this->addOption('r|role:', "User role (e.g. 'student', 'guest')", "student");
        $this->addOption('n|norole', "Match enrolments with no role (set to zero)");
        $this->addOption('c|cohort-name:', "Matched against either the Name (name) or Cohort ID (idnumber) - not the 'cohort.id' field.");

        $this->addArgument('plugin');
        $this->addArgument('courseid');

        $this->maxArguments = 255;
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/course/lib.php';

        $courseids = $this->arguments;
        $plugintype = array_shift($courseids);

        $options = $this->expandedOptions;
        if ($options['role']) {
            $role = $DB->get_record('role', array('shortname'=>$options['role']), '*', MUST_EXIST);

            if (!$role) {
                cli_error("--role did not match a role");
            }
        }

        if ($options['cohort-name']) {
            if ($plugintype != "cohort") {
                cli_error("--cohort-name should only used with 'cohort' plugin-type");
            }
            $systemcontext = \context_system::instance();
        }

        foreach ($courseids as $courseid) {
            // get the details for the course
            $course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);

            if ($options['cohort-name']) {
                $catcontext = \context_coursecat::instance($course->category);

                $str1 = $DB->sql_compare_text('name');
                $str1ph = $DB->sql_compare_text(':name');
                $str2 = $DB->sql_compare_text('idnumber');
                $str2ph = $DB->sql_compare_text(':idnumber');

                $cohort = $DB->get_records_sql(
                    "SELECT * FROM {cohort} WHERE "
                    ."( contextid = :systemcontext OR contextid = :catcontext ) "
                    ."AND ({$str1} = {$str1ph} OR {$str2} = {$str2ph})",
                    [
                        'systemcontext' => $systemcontext->id,
                        'catcontext' => $catcontext->id,
                        'name' => $options['cohort-name'],
                        'idnumber' => $options['cohort-name'],
                    ]);

                if (!$cohort) {
                    cli_error("--cohort-name did not match a cohort");
                }
                if (count($cohort) > 1) {
                    cli_error("--cohort-name matched more than 1 cohort");
                }
                list($cohort) = array_values($cohort);
            }

            // get the details of the enrolment plugin
            $plugin = enrol_get_plugin($plugintype);
            if(!$plugin){
                cli_error("could not find '$plugintype' enrolment plugin");
            }

            // get the enrolment plugin instances for the course
            $instances = enrol_get_instances($courseid, false);

            // loop through the instances to find the instance ID for the enrolment plugin
            $matchingInstances = [];
            foreach($instances as $instance){
                $match = False;
                if ($instance->enrol === $plugintype){
                    $match = True;
                    if ($options['norole']) {
                        if ($instance->roleid != 0) {
                            $match = False;
                        }
                    }
                    elseif ($options['role'] && $instance->roleid != $role->id) {
                        $match = False;
                    }

                    if ($options['cohort-name'] && $instance->customint1 != $cohort->id){
                        $match = False;
                    }
                }
                if ($match) {
                    $matchingInstances[] = $instance;
                }
            }

            if ($matchingInstances) {
                if(count($matchingInstances) == 1){
                    $enrolInstance = $matchingInstances[0];
                    // Deactivate enrolment
                    if ($enrolInstance->status == ENROL_INSTANCE_ENABLED) {
                        $plugin->update_status($enrolInstance, ENROL_INSTANCE_DISABLED);
                    }
                }
                else {
                    cli_error("enrolment plugin '$plugintype' matched more than 1 instance: "
                        ."try using the --role or --cohort-name options to narrow the selection");
                }
            }
        }
    }
}
