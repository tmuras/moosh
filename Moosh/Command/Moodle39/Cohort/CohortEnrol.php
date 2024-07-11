<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace Moosh\Command\Moodle39\Cohort;
use Moosh\MooshCommand;

class CohortEnrol extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('enrol', 'cohort');

        $this->addOption('u|userid:', 'userid');
        $this->addOption('c|courseid:', 'courseid');
        $this->addOption('idnumber', "Match cohort alphanumeric 'idnumber' field instead of name");
        $this->addOption('role:', "Defaults to 'student'", 'student');
        $format_help = "Defaults to '%n_sync'. ";
        $format_help .= "'%n' is replaced by cohort name, ";
        $format_help .= "'%i' is replaced by cohort idnumber, ";
        $format_help .= "'%r' is replaced by role name, ";
        $format_help .= "'%s' is replaced by role shortname.";
        $this->addOption('name-pattern:', $format_help, '%n_sync');

        $this->addArgument('name');

        $this->maxArguments = 255;
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/cohort/lib.php';
        require_once $CFG->dirroot . '/enrol/cohort/locallib.php';

        foreach ($this->arguments as $argument) {
            $this->expandOptionsManually(array($argument));
            $options = $this->expandedOptions;

            $role = $DB->get_record('role',array('shortname'=>$options['role']), '*', MUST_EXIST);

            // Sanity Checks.
            // Check if cohorst exists.
            $cohort_search_params = [];
            if ($options['idnumber']) {
                $cohort_search_params['idnumber'] = $argument;
            }
            else {
                $cohort_search_params['name'] = $argument;
            }
            if (!$cohorts = $DB->get_records('cohort', $cohort_search_params)) {
                echo "Cohort does not exist\n";
                exit(0);
            }

            // Check if enough arguments.
            if (empty($options['courseid']) && empty($options['userid'])) {
                echo "Not enough arguments, provide userid or courseid\n";
            }

            // Check if course exists.
            $course = '';
            if (!empty($options['courseid'])) {
                if (!$course = $DB->get_record('course',array('id'=>$options['courseid']))) {
                    echo "Course does not exist\n";
                    exit(0);
                }
            }

            // Check if user exists.
            if (!empty($options['userid'])) {
                if (!$user = $DB->get_record('user',array('id'=>$options['userid']))) {
                    echo "User does not exist\n";
                    exit(0);
                }
            }

            // Add cohort to course
            if (!empty($course)) {

                foreach($cohorts as $cohort) {

                    // Check if cohort enrolment already exists.
                    if ($cohortenrolment = $DB->get_record('enrol', ['enrol' => 'cohort', 'customint1' => $cohort->id,
                        'courseid' => $options['courseid'], 'roleid' => $role->id])) {
                        echo " Notice: Cohort already enrolled into course\n";
                    } else {

                        $enrol = enrol_get_plugin('cohort');

                        $enrol->add_instance($course, array(
                            'name'=>$this->build_sync_name($options['name-pattern'], $cohort, $role),
                            'status'=>0,
                            'customint1'=>$cohort->id,
                            'roleid'=>$role->id,
                            'customint2'=>'0'
                        ));
                        echo "Cohort enrolled\n";
                    }
                    $this->enrol_cohort_sync($course->id);
                }
            }


            if (!empty($user)) {
                foreach($cohorts as $cohort) {
                    cohort_add_member($cohort->id,$options['userid']);
                    echo "User " . $options['userid'] . " enrolled\n";
                    if (!empty($course)) {
                        $this->enrol_cohort_sync($course->id);
                    }
                }
            }


        }
    }

    protected function enrol_cohort_sync($courseid)
    {
        $trace = new \null_progress_trace();
        enrol_cohort_sync($trace, $courseid);
        $trace->finished();
    }

    protected function build_sync_name($pattern, $cohort, $role)
    {
        $pattern = preg_replace('/\%n/', $cohort->name, $pattern);
        $pattern = preg_replace('/\%i/', $cohort->idnumber, $pattern);
        $pattern = preg_replace('/\%r/', role_get_name($role), $pattern);
        $pattern = preg_replace('/\%s/', $role->shortname, $pattern);

        return $pattern;
    }
}
