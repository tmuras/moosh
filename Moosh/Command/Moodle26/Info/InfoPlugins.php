<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle26\Info;

use Moosh\MooshCommand;

class InfoPlugins extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('plugins', 'info');
    }

    public function execute()
    {
        $manager = \core_plugin_manager::instance();
        $types = $manager->get_plugin_types();
        ksort($types);
        foreach ($types as $type => $directory) {
            echo $type . "," . $directory. "\n";
        }

    }
}
