<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Task;
use Moosh\MooshCommand;

class LockCheck extends MooshCommand
{
    public function __construct()
{
    parent::__construct('lock-check', 'task');

    //$this->addArgument('name');

    //$this->addOption('t|test', 'option with no value');
    //$this->addOption('o|option:', 'option with value and default', 'default');

}

    public function execute()
{
    // Some variables you may want to use
    //  $this->cwd - the directory where moosh command was executed
    //  $this->mooshDir - moosh installation directory
    //  $this->expandedOptions - commandline provided options, merged with defaults
    //  $this->topDir - top Moodle directory
    //  $this->arguments[0] - first argument passed
    //  $this->pluginInfo - array with information about the current plugin (based on cwd), keys:'type','name','dir'
    //  $this->verbose - if set to true, then "moosh -v" was run - add more verbose / debug information

    $options = $this->expandedOptions;

    // Get the list of all tasks

    // calculate lock name
    // sha1($this->dbprefix . $resource);
    // Example
    // \mod_assign\task\cron_task
    // 68ad53033898d7104066b1bdb33524986c97c870
    /* if verbose mode was requested, show some more information/debug messages
    if($this->verbose) {
        echo "Say what you're doing now";
    }
    */
}
}
