<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Config;
use Moosh\MooshCommand;

class ConfigSet extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('set', 'config');

        //@TODO implement
        $this->addOption('d|delete', 'delete instead of setting (not implemented yet)');

        $this->addArgument('name');
        $this->addArgument('value');
        //$this->minArguments = 1;
        $this->maxArguments = 3;
    }

    public function execute()
    {
        global $CFG, $DB;

        $name = trim($this->arguments[0]);
        $value = trim($this->arguments[1]);

        $plugin = NULL;
        if (isset($this->arguments[2])) {
            $plugin = trim($this->arguments[2]);
        }

        set_config($name, $value, $plugin);

        if(!isset($plugin)) {
            $plugin = 'moodle';
        }
        echo "New value: " . get_config($plugin,$name) . "\n";
    }

    protected function getArgumentsHelp()
    {
      return "\n\nARGUMENTS:\n\tname value [plugin]\n";
    }
}
