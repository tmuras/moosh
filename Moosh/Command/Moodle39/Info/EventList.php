<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Info;
use Moosh\MooshCommand;

class EventList extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('list', 'event');

        //$this->addArgument('name');

        //$this->addOption('t|test', 'option with no value');
        //$this->addOption('o|option:', 'option with value and default', 'default');

    }

    public function execute()
    {
        global $CFG;
        //some variables you may want to use
        //$this->cwd - the directory where moosh command was executed
        //$this->mooshDir - moosh installation directory
        //$this->expandedOptions - commandline provided options, merged with defaults
        //$this->topDir - top Moodle directory
        //$this->arguments[0] - first argument passed
        //$this->pluginInfo - array with information about the current plugin (based on cwd), keys:'type','name','dir'

        $options = $this->expandedOptions;

        /* if verbose mode was requested, show some more information/debug messages
        if($this->verbose) {
            echo "Say what you're doing now";
        }
        */
        //load cache file manually as there seems to be no way to get it using Moodle API
        $cachefile = "$CFG->cachedir/core_component.php";
        include($cachefile);
        foreach($cache['classmap'] as $k=>$class) {
            if(strstr($k,'\\event\\')) {
                echo $k . "\n";
            }
        }
    }
}
