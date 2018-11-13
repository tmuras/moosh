<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle31\Group;
use Moosh\MooshCommand;

class GroupCreate extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('create', 'group');

        $this->addOption('i|id:', 'group idnumber', NULL);
        $this->addOption('d|description:', 'meaningful explanation of group role', NULL);
        $this->addOption('f|format:', 'description format', 4);
        // lib/weblib.php defines FORMAT_MARKDOWN
        $this->addOption('k|key:', 'enrolment key', NULL);
        $this->addArgument('groupname');
        $this->addArgument('course');

        $this->minArguments = 2;

    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/group/lib.php';

        $group = new \stdClass();
        $group->courseid = $this->arguments[1];
        $group->name = $this->arguments[0];

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
            $group->idnumber = $options['id'];
        }
        if (!empty($options['description'])) {
            $group->description = $options['description'];

        }
        $group->descriptionformat = $options['format'];
        if (!empty($options['key'])) {
            $group->key = $options['key'];

        }

        $newgroupid = groups_create_group($group, false, false);
        echo "$group->name ($newgroupid)\n";
    }
}
