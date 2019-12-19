<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Module;
use Moosh\MooshCommand;

class ModuleManage extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('manage', 'module');

        $this->addArgument('action');
        $this->addArgument('blockname');
        $this->addOption('f|force', 'force delete of module from disk');

    }

    public function execute()
    {
        global $DB;

        $action = $this->arguments[0];
        $modulename = $this->arguments[1]; // name of the module (in English)

        // Does module exists?
        if (!empty($modulename)) {
            if (!$module = $DB->get_record('modules', array('name'=>$modulename))) {
                print_error('moduledoesnotexist', 'error');
            }
        }

        switch ($action) {
        case 'show':
            $DB->set_field('modules', 'visible', '1', array('id'=>$module->id));      // Show module.
            break;

        case 'hide':
            $DB->set_field('modules', 'visible', '0', array('id'=>$module->id));      // Hide module.
            break;

        case 'delete':
            // Delete module from DB. Should we also delete it from disk?
            if ($this->expandedOptions['force']) {
                // Delete module from disk too!
            }
            break;
        }

    }

    protected function getArgumentsHelp()
    {
        return "\n\nARGUMENTS:".
                "\n\taction[show|hide|delete] modulename";
                //"\n\n\t-f|--force delete module from disk too";
    }
}
