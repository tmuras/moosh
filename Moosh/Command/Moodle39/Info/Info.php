<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Info;
use Moosh\MooshCommand;

class Info extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('info');
    }

    public function execute()
    {
        echo "Plugin type: ".$this->pluginInfo['type'] . "\n";
        echo "Plugin name: ".$this->pluginInfo['name'] . "\n";
        echo "Plugin directory: ".$this->pluginInfo['dir'] . '/' . $this->pluginInfo['name'] . "\n";
    }
}
