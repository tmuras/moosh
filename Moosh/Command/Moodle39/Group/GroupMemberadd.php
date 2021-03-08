<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Group;
use Moosh\MooshCommand;

class GroupMemberadd extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('memberadd', 'group');

        $this->addOption('g|group:', 'id of group');
        $this->addOption('c|course:', 'id of course');

        $this->addArgument('username');
        $this->maxArguments = 255;

    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/group/lib.php';
        require_once $CFG->dirroot . '/user/lib.php';

        $options = $this->expandedOptions;

        $membership = new \stdClass();
        if (!empty($options['course'])) {
            $membership->courseid = $options['course'];
        }
        $membership->groupid = $options['group'];

        $useridlist = array();
        if (!empty($membership->courseid)) {
            $enrolledusers = user_get_participants( $membership->courseid, 0, 0, 0, '',
                0, '');
            foreach ($enrolledusers as $user) {
                $useridlist[$user->firstname] = $user->id;
            }
            $enrolledusers->close();
        }
        $names = "";
        foreach ($this->arguments as $argument) {

            $this->expandOptionsManually(array($argument));
            $options = $this->expandedOptions;

            if (!empty($membership->courseid)) {
                $userfirstname = $argument;
                $userid = $useridlist[$userfirstname];
                $groupupdate = groups_add_member($membership->groupid, $userid);
                if ( $groupupdate ) {
                    $names .= "\t$userfirstname";
                }
                else {
                    echo $membership->groupid . ": " . $userfirstname . " not added.\n";
                }
            }
            else {
                $groupupdate = groups_add_member($membership->groupid, $argument);
                $user = $DB->get_record('user',
                        array('id'=>$argument), '*', MUST_EXIST);
                if ( $groupupdate ) {
                    $names .= "\t$user->firstname";
                }
                else {
                    echo $membership->groupid . ": " . $user->firstname . " not added.\n";
                }
            }
        }
        echo "$names\n";
    }
}
