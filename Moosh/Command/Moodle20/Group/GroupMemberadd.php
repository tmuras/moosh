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

        //$this->addArgument('name');

        $this->addArgument('groupname');
        $this->addArgument('course');
        $this->addArgument('username');

        $this->minArguments = 3;

    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/group/lib.php';

	$group = new \stdClass();
        $group->courseid = $this->arguments[1];
        $group->name = $this->arguments[0];
        $group->member = $this->arguments[2];

        // Some variables you may want to use
        //  $this->cwd - the directory where moosh command was executed
        //  $this->mooshDir - moosh installation directory
        //  $this->expandedOptions - commandline provided options, merged with defaults
        //  $this->topDir - top Moodle directory
        //  $this->arguments[0] - first argument passed
        //  $this->pluginInfo - array with information about the current plugin (based on cwd), keys:'type','name','dir'
        //  $this->verbose - if set to true, then "moosh -v" was run - add more verbose / debug information

        $options = $this->expandedOptions;

	$context = \context_course::instance($group->courseid);
	$groupupdate = groups_add_member($group->name, $group->member);
	if ( $groupupdate ) {
	    echo $group->name . ": " . $group->member . "\n";
	}
	else {
	    echo $group->name . ": " . $group->member . " not added.\n";
	}

        // if verbose mode was requested, show some more information/debug messages
        if($this->verbose) {
            echo "Say what you're doing now\n";
        }
    }
}
