<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2024 fireartist
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Cohort;
use Moosh\MooshCommand;

class CohortUnSync extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('unsync', 'cohort');

        $this->addOption('idnumber', "Match cohort alphanumeric 'idnumber' field instead of name");
        $this->addOption('role:', "Defaults to 'student'", 'student');

        $this->addArgument('courseid');
        $this->addArgument('cohortname');

        $this->minArguments = 2;
        $this->maxArguments = 2;
    }

    public function execute()
    {
        global $CFG, $DB, $PAGE;

        require_once($CFG->dirroot . '/enrol/locallib.php');

        list($courseid, $cohortname) = $this->arguments;
        $options = $this->expandedOptions;

        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

        $cohort_search_params = [];
        if ($options['idnumber']) {
            $cohort_search_params['idnumber'] = $cohortname;
        }
        else {
            $cohort_search_params['name'] = $cohortname;
        }

        $cohort = $DB->get_record('cohort', $cohort_search_params, '*', MUST_EXIST);

        $role = $DB->get_record('role',array('shortname'=>$options['role']), '*', MUST_EXIST);

        $manager = new \course_enrolment_manager($PAGE, $course);
        $plugins = $manager->get_enrolment_plugins();
        $enrolments = $manager->get_enrolment_instances();

        foreach ($enrolments as $enrolment) {
            if ($enrolment->enrol != "cohort") {
                continue;
            }
            if ($enrolment->customint1 != $cohort->id) {
                continue;
            }
            if ($enrolment->roleid != $role->id) {
                continue;
            }
            $plugin = $plugins[$enrolment->enrol];
            if (!isset($plugin)) {
                continue;
            }
            $id = $enrolment->id;
            $plugin->delete_instance($enrolment);
            echo "Deleted enrolment instance [$id]\n";
        }
    }
}
