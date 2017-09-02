<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle20\Group;

use Moosh\MooshCommand;

class GroupList extends MooshCommand {
    public function __construct() {
        parent::__construct('list', 'group');

        $this->addOption('n|idnumber', 'show idnumber');
        $this->addOption('d|description', 'show description');
        $this->addOption('i|id', 'display id only column');
        $this->addOption('G|groupingid:', 'groups from given grouping only');

        $this->addArgument('courseid');
        $this->minArguments = 1;
        $this->maxArguments = 255;
    }

    public function execute() {
        global $CFG, $DB;

        $options = $this->expandedOptions;

        if($this->verbose) {
            echo "Say what you're doing now\n";
        }

        foreach ($this->arguments as $courseid) {
	    echo "\t\tcourseid: $courseid\n";
	    $groupings = $DB->get_records('groupings', array('courseid'=>$courseid) );
	    foreach ($groupings as $grouping) {
		echo "grouping " . $grouping->id . " \"" . $grouping->name . "\" " . $grouping->description . "\n";
		$grouping_groups = $DB->get_records('groupings_groups', array('groupingid'=>$grouping->id) );
		foreach ($grouping_groups as $grouping_group) {
		    $groups = $DB->get_records('groups', array('id'=>$grouping_group->groupid) );
		    foreach ($groups as $group) {
		    echo "\tgroup " . $group->id . " \"" . $group->name . "\" " . $group->description . "\n";
		    }
		}
	    }
	}
    }

}
