<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Config;
use Moosh\MooshCommand;

class ConfigGet extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('get', 'config');

        $this->minArguments = 0;
        $this->maxArguments = 2;
    }

    public function execute()
    {
        global $CFG, $DB;

        $name = NULL;
        $plugin = NULL;

        if(isset($this->arguments[0])) {
            $plugin = trim($this->arguments[0]);
        }

        if(isset($this->arguments[1])) {
            $name =  trim($this->arguments[1]);
        }

        print_r(get_config($plugin,$name));
        echo "\n";
    }

    protected function getArgumentsHelp()
    {
        $ret = "\n\nARGUMENTS:";
        $ret .= "\n\t";
        $ret .= "<plugin> <name>";

        return $ret;
    }
}
