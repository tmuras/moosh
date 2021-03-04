<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Module;
use Moosh\MooshCommand;

class ModuleReinstall extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('reinstall', 'module');

        $this->addArgument('name');

        //$this->addOption('t|test', 'option with no value');
        //$this->addOption('o|option:', 'option with value and default', 'default');

    }

    public function execute()
    {
        global $CFG;
        require_once($CFG->libdir.'/adminlib.php');       // various admin-only functions
        require_once($CFG->libdir.'/upgradelib.php');     // general upgrade/install related functions
        require_once($CFG->libdir.'/environmentlib.php');
        @include_once($CFG->libdir.'/pluginlib.php');
        require_once($CFG->dirroot.'/course/lib.php');

        //some variables you may want to use
        //$this->cwd - the directory where moosh command was executed
        //$this->mooshDir - moosh installation directory
        //$this->expandedOptions - commandline provided options, merged with defaults
        //$this->topDir - top Moodle directory
        //$this->arguments[0] - first argument passed

        //$options = $this->expandedOptions;
        $split = explode('_',$this->arguments[0],2);

        uninstall_plugin($split[0],$split[1]);
        upgrade_noncore(true);

        /* if verbose mode was requested, show some more information/debug messages
        if($this->verbose) {
            echo "Say what you're doing now";
        }
        */
    }
}
