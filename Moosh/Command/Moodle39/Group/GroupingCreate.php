<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Group;
use Moosh\MooshCommand;

class GroupingCreate extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('create', 'grouping');

        $this->addOption('i|id:', 'grouping idnumber', NULL);
        $this->addOption('d|description:', 'meaningful explanation of grouping role', NULL);
        $this->addOption('f|format:', 'description format', 4);
	// lib/weblib.php defines FORMAT_MARKDOWN
        $this->addArgument('groupingname');
        $this->addArgument('course');

        $this->minArguments = 2;

    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/group/lib.php';

	$grouping = new \stdClass();
        $grouping->courseid = $this->arguments[1];
        $grouping->name = $this->arguments[0];

        $options = $this->expandedOptions;

        if (!empty($options['id'])) {
            $grouping->idnumber = $options['id'];
        }
        if (!empty($options['description'])) {
            $grouping->description = $options['description'];

	}
        $grouping->descriptionformat = $options['format'];

	$newgroupingid = groups_create_grouping($grouping, NULL);
	echo "$grouping->name ($newgroupingid)\n";
    }
}
