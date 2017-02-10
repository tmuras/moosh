<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle27\Event;

use Moosh\MooshCommand;

class EventFire extends MooshCommand
{
    public function __construct() {
        parent::__construct('fire', 'event');

        $this->addArgument('name');
        $this->addArgument('data');

        //$this->addOption('t|test', 'option with no value');
        //$this->addOption('o|option:', 'option with value and default', 'default');

    }

    public function execute() {
        global $CFG;
        //some variables you may want to use
        //$this->cwd - the directory where moosh command was executed
        //$this->mooshDir - moosh installation directory
        //$this->expandedOptions - commandline provided options, merged with defaults
        //$this->topDir - top Moodle directory
        //$this->arguments[0] - first argument passed
        //$this->pluginInfo - array with information about the current plugin (based on cwd), keys:'type','name','dir'

        $options = $this->expandedOptions;

        //name of the event
        $name = $this->arguments[0];

        //json serialized data
        $data = $this->arguments[1];

        $data = json_decode($data,true);

        if(!$data) {
            cli_error("Could not decode json data.");
        }

        //load cache file manually as there seems to be no way to get it using Moodle API
        $cachefile = "$CFG->cachedir/core_component.php";
        include($cachefile);

        //match the name of the event
        //if was used than assume full namespace was given

        if (strpos($name, '\\') !== false) {
            $fullname = $name;
        } else {
            $matches = array();
            //first look for single match after last \
            foreach ($cache['classmap'] as $k => $class) {
                if (preg_match("/\\\\$name$/",$k)) {
                    $matches[] = $k;
                }
            }

            if(count($matches) > 1) {
                print_r($matches);
                cli_error("More than one matching event");
            }
            $fullname = $matches[0];
        }

        $class = $cache['classmap'][$fullname];

        if(!$class) {
            cli_error("Class '$fullname' not found");
        }

        if($this->verbose) {
            cli_problem("Loading class $fullname");
        }
        $event = $fullname::create($data);
        //$event->set_legacy_logdata(array(666, "course", "report log", "report/log/index.php?id=666", 666));
        $event->trigger();
    }
}
