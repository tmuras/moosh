<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle35\Info;

use Moosh\MooshCommand;

class InfoPlugins extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('plugins', 'info');
    }

    public function execute()
    {
        global $CFG;

        $manager = \core_plugin_manager::instance();
        $plugins = $manager->get_plugin_types();
        $subplugins = $manager->get_subplugins();

        ksort($plugins);
        $cutcharacters = strlen($CFG->dirroot);
        foreach ($plugins as $type => $directory) {
            echo $type . "," . substr($directory,$cutcharacters). "\n";
        }

    }
}
