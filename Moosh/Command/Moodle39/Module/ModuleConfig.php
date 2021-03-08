<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Module;
use Moosh\MooshCommand;

class ModuleConfig extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('config', 'module');

        $this->addArgument('action');
        $this->addArgument('blockname');
        $this->addArgument('setting');
        $this->addArgument('value');

    }

    public function execute()
    {
        global $DB;

        $action = $this->arguments[0]; // set or get
        $modulename = $this->arguments[1]; // name of the module (in English)
        $setting = $this->arguments[2]; // Setting to change
        $value = $this->arguments[3]; // New value for setting

        // Does module exists?
        /*
        if (!empty($modulename)) {
            if (!$module = $DB->get_record('modules', array('name'=>$modulename))) {
                print_error('moduledoesnotexist', 'error');
            }
        }
        */

        switch ($action) {
            case 'set':
                $DB->set_field('config_plugins', 'value', $value, array('plugin'=>$modulename, 'name'=>$setting));
                break;

            case 'get':
                if ($result = $DB->get_record('config_plugins', array('plugin'=>$modulename, 'name'=>$setting))) {
                    echo $setting." = ". $result->value."\n";
                }
                break;
        }



    }

    protected function getArgumentsHelp()
    {
        return "\n\nARGUMENTS:".
                "\n\taction[get|set] modulename setting value".
                "\n\n\t( When using the GET action, add a '.' at the end of the command as the VALUE )";
    }
}
