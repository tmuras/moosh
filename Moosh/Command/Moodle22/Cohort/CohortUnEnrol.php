<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle22\Cohort;
use Moosh\MooshCommand;

class CohortUnEnrol extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('unenrol', 'cohort');

        $this->addArgument('cohortid');
        $this->addArgument('userid');

        $this->maxArguments = 255;
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/cohort/lib.php';
        require_once $CFG->dirroot . '/enrol/cohort/locallib.php';

        $cohortid = $this->arguments[0];

        // Check if cohort exists.
        if (!$cohort = $DB->get_record('cohort', array('id' => $cohortid))) {
            echo "Cohort does not exist\n";
            exit(0);
        }

        $users = array_slice($this->arguments, 1);
        foreach ($users as $key => $userid) {
            cohort_remove_member($cohortid, $userid);
            echo "User " . $userid . " un-enrolled\n";
        }
    }
}
