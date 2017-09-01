<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle20\Group;
use Moosh\MooshCommand;

class GroupingCreate extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('create', 'grouping');

        //$this->addArgument('name');

        $this->addOption('i|id:', 'grouping idnumber', NULL);
        $this->addOption('d|description:', 'meaningful explanation of grouping role', NULL);
        $this->addOption('f|format:', 'description format', 4);
	// lib/weblib.php defines FORMAT_MARKDOWN
        $this->addOption('k|key:', 'enrolment key', NULL);
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

        // Some variables you may want to use
        //  $this->cwd - the directory where moosh command was executed
        //  $this->mooshDir - moosh installation directory
        //  $this->expandedOptions - commandline provided options, merged with defaults
        //  $this->topDir - top Moodle directory
        //  $this->arguments[0] - first argument passed
        //  $this->pluginInfo - array with information about the current plugin (based on cwd), keys:'type','name','dir'
        //  $this->verbose - if set to true, then "moosh -v" was run - add more verbose / debug information

        $options = $this->expandedOptions;

        if (!empty($options['id'])) {
            $grouping->idnumber = $options['id'];
        }
        if (!empty($options['description'])) {
            $grouping->description = $options['description'];

	}
        $grouping->descriptionformat = $options['format'];

	$newgroupingid = groups_create_grouping($grouping, NULL);
	echo "$newgroupingid\n";


        // if verbose mode was requested, show some more information/debug messages
        if($this->verbose) {
            echo "Say what you're doing now";
        }
    }
}
