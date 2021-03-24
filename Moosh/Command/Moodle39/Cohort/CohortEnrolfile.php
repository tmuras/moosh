<?php
/**
 * Enroll a list of users from a CSV (username) into a Cohort

 * @author     Marty Gilbert (at) gmail
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Cohort;
use Moosh\MooshCommand;

class CohortEnrolfile extends MooshCommand
{
    public function __construct() {
        parent::__construct('enrolfile', 'cohort');

        $this->addArgument('filename');
    }

    protected function getArgumentsHelp() {
        $ret = "\n\nARGUMENTS:";
        $ret .= "\n\t";
        $ret .= implode(' ', $this->argumentNames);
        $ret .= "\n\n\tThe CSV filename containing the username/email AND cohortid/cohortname.\n".
            "\tIf both are given, username and cohortid take precedence.";

        return $ret;
    }

    public function execute() {

        global $CFG, $DB;
        require_once($CFG->libdir . '/csvlib.class.php');
        require_once($this->topDir . '/cohort/lib.php');

        // Read and parse the CSV file using csv library.
        $file = fopen($this->arguments[0], 'r') or die ("Cannot open $this->arguments[0]");

        $usersprocessed = 0;
        $entries = array();
        $header = null;
        $rownum = 0;
        while (($data = fgetcsv($file)) !== false) {
            $rownum++;
            if ($header === null) {
                $header = $data;

                // Field cohortid OR field cohortname must exist.
                if (!in_array('cohortid', $header) && !in_array('cohortname', $header)) {
                    echo "Error. CSV must contain cohortid or cohortname fields. Exiting.\n";
                    return;
                }

                // Field username OR field email must exist.
                if (!in_array('username', $header) && !in_array('email', $header)) {
                    echo "Error. CSV must contain email or username fields. Exiting.\n";
                    return;
                }

                continue;
            }

            $newrow = new \stdClass();
            for ($i = 0; $i < count($data); $i++) {
                $newrow->{$header[$i]} = $data[$i];
            }

            // Make sure there is at least one value in cohortid/name and username/email.
            if (empty($newrow->cohortid) && empty($newrow->cohortname)) {
                echo "Entry cohortid OR cohortname not provided for row $rownum. Skipping.\n";
            } else if (empty($newrow->username) && empty($newrow->email)) {
                echo "Entry username OR email not provided for row $rownum. Skipping.\n";
            } else {
                $entries[] = $newrow;
            }
        }
        fclose ($file);

        foreach ($entries as $entry) {

            $udesc = '';
            $user = null;
            if (!empty($entry->username)) {
                $udesc = $entry->username;
                $user = $DB->get_record('user', array('username' => $entry->username), 'id,firstname,lastname');
            } else {
                // Entry email must exist if username does not, due to above check.
                $udesc = $entry->email;
                $user = $DB->get_record('user', array('email' => $entry->email), 'id,firstname,lastname');
            }

            if (!$user) {
                echo "Error. User $udesc not in DB. Skipping.\n";
                continue;
            }

            $cdesc = '';
            if (!empty($entry->cohortid)) {
                $cdesc = $entry->cohortid;
                $cohort = $DB->get_record('cohort', array('id' => $entry->cohortid), 'id,name');
            } else {
                // Entry cohortname must exist if cohortid does not, due to check above.
                $cdesc = $entry->cohortname;
                $cohort = $DB->get_record('cohort', array('name' => $entry->cohortname), 'id,name');
            }

            if (!$cohort) {
                echo "Cohort $cdesc does not exist. Skipping.\n";
                continue;
            }

            if ($this->verbose) {
                echo "Adding $user->firstname $user->lastname to $cohort->name.\n";
            }

            cohort_add_member($cohort->id, $user->id);
            $usersprocessed++;
        }

        if ($this->verbose) {
            echo 'Processed '.$usersprocessed.' cohort assignments.'."\n";
        }
    }
}
