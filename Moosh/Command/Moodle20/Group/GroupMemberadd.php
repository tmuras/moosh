<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle20\Group;
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

        // Some variables you may want to use
        //  $this->cwd - the directory where moosh command was executed
        //  $this->mooshDir - moosh installation directory
        //  $this->expandedOptions - commandline provided options, merged with defaults
        //  $this->topDir - top Moodle directory
        //  $this->arguments[0] - first argument passed
        //  $this->pluginInfo - array with information about the current plugin (based on cwd), keys:'type','name','dir'
        //  $this->verbose - if set to true, then "moosh -v" was run - add more verbose / debug information

        $options = $this->expandedOptions;

        $membership = new \stdClass();
        if (!empty($options['course'])) {
            $membership->courseid = $options['course'];
        }
        $membership->groupid = $options['group'];

        $useridlist = array();
        if (!empty($membership->courseid)) {
            $enrolledusers = user_get_participants( $membership->courseid, 0, 0, 0, '');
            foreach ($enrolledusers as $user) {
                $useridlist[$user->firstname] = $user->id;
            }
            $enrolledusers->close();
        }
        foreach ($this->arguments as $argument) {
            $this->expandOptionsManually(array($argument));
            $options = $this->expandedOptions;

            if (!empty($membership->courseid)) {
                $userfirstname = $argument;
                $userid = $useridlist[$userfirstname];
                $groupupdate = groups_add_member($membership->groupid, $userid);
                if ( $groupupdate ) {
                    echo $membership->groupid . ": " . $argument . "\n";
                }
                else {
                    echo $membership->groupid . ": " . $argument . " not added.\n";
                }
            }
            else {
                $groupupdate = groups_add_member($membership->groupid, $argument);
                if ( $groupupdate ) {
                    echo $membership->groupid . ": " . $argument . "\n";
                }
                else {
                    echo $membership->groupid . ": " . $argument . " not added.\n";
                }
            }
        }

        // if verbose mode was requested, show some more information/debug messages
        if($this->verbose) {
            echo "Say what you're doing now\n";
        }
    }
}
