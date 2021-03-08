<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Group;

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
            if (empty($options["id"])) {
                echo "\t\tcourseid: $courseid\n";
            }
            if (!empty($options["groupingid"])) {
                $id = $options["groupingid"];
                $groupings = $DB->get_records('groupings', array('id'=>$id) );
            }
            else {
                $groupings = $DB->get_records('groupings', array('courseid'=>$courseid) );
            }
            $dupe = array();
            foreach ($groupings as $grouping) {
                if (empty($options["id"])) {
                    echo "grouping " . $grouping->id . " \"" . $grouping->name . "\" " . $grouping->description . "\n";
                }
                $grouping_groups = $DB->get_records('groupings_groups', array('groupingid'=>$grouping->id) );
                foreach ($grouping_groups as $grouping_group) {
                    $groups = $DB->get_records('groups', array('id'=>$grouping_group->groupid) );
                    foreach ($groups as $group) {
                        if (!empty($options["id"])) {
                            echo $group->id . "\n";
                            }
                        else {
                            echo "\tgroup " . $group->id . " \"" . $group->name . "\" " . $group->description . "\n";
                        }
                        $dupe[$id] = $group;
                    }
                }
            }
            if (empty($options["groupingid"])) {
                $free_groups = $DB->get_records('groups', array('courseid'=>$courseid) );
                foreach ($free_groups as $i => $group) {
                    $id = $group->id;
                    if (isset($dupe[$id])) {
                            unset($free_groups[$i]);
                    }
                }
                if (!empty($free_groups)) {
                    if (empty($options["id"])) {
                            echo "No grouping\n";
                    }
                    foreach ($free_groups as $group) {
                        if (!empty($options["id"])) { echo $id . "\n"; }
                        else {
                            echo "\tgroup " . $group->id . " \"" . $group->name . "\" " . $group->description . "\n";
                        }
                    }
                }
            }
        }
    }
}
