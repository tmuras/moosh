<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle31\Group;
use Moosh\MooshCommand;

class GroupAssigngrouping extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('assigngrouping', 'group');

        $this->addOption('G|grouping:', 'id of grouping');

        $this->addArgument('group');
        $this->maxArguments = 255;

    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/group/lib.php';

        $options = $this->expandedOptions;

        $assignment = new \stdClass();
        $assignment->id = $options['grouping'];
        $grouping = $DB->get_record('groupings',
                array('id'=>$assignment->id), '*', MUST_EXIST);

        if($this->verbose) {
            echo "Assigning to grouping: " . $grouping->description . "\n";
            }

        foreach ($this->arguments as $argument) {
            $update = groups_assign_grouping($assignment->id, $argument);
            $group = $DB->get_record('groups',
                    array('id'=>$argument), '*', MUST_EXIST);
            echo "$group->name ($group->id)\n";
        }
    }
}
