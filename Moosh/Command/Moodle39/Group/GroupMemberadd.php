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
            $coursecontext = \context_course::instance($membership->id);
            $enrolledusers = get_enrolled_users($coursecontext);
            foreach ($enrolledusers as $user) {
                $useridlist[$user->username] = $user->id;
            }
            $enrolledusers->close();
        }
        $names = "";
        foreach ($this->arguments as $argument) {

            $this->expandOptionsManually(array($argument));
            $options = $this->expandedOptions;

            if (!empty($membership->courseid)) {
                $username = $argument;
                $userid = $useridlist[$username];
                $groupupdate = groups_add_member($membership->groupid, $userid);
                if ( $groupupdate ) {
                    $names .= "\t$username";
                }
                else {
                    echo $membership->groupid . ": " . $username . " not added.\n";
                }
            }
            else {
                $groupupdate = groups_add_member($membership->groupid, $argument);
                $user = $DB->get_record('user',
                        array('id'=>$argument), '*', MUST_EXIST);
                if ( $groupupdate ) {
                    $names .= "\t$user->username";
                }
                else {
                    echo $membership->groupid . ": " . $user->username . " not added.\n";
                }
            }
        }
        echo "$names\n";
    }
}
