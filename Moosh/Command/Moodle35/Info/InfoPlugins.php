<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle35\Info;

use Moosh\MooshCommand;
use Symfony\Component\Finder\Finder;

class InfoPlugins extends MooshCommand {
    public function __construct() {
        parent::__construct('plugins', 'info');
        $this->addOption('e|export', 'idnumber', false);

    }

    public function execute() {
        global $CFG;

        $options = $this->expandedOptions;

        $manager = \core_plugin_manager::instance();
        $plugins = $manager->get_plugin_types();

        asort($plugins);
        $cutcharacters = strlen($CFG->dirroot);
        foreach ($plugins as $type => $directory) {
            $pluginsinside = array();
            $finder = new Finder();
            $iterator = $finder
                    ->directories()
                    ->depth(0)
                    ->in($directory);
            foreach ($iterator as $dir) {
                $pluginsinside[] = $dir->getBasename();
            }

            echo $type . "\t" . substr($directory, $cutcharacters) . "\t" . implode(',', $pluginsinside) . "\n";
        }

        if ($options['export']) {
            // Also export for use in path command
            uasort($plugins, function($a, $b) {
                return (strlen($a) < strlen($b));
            });

            foreach ($plugins as $name => $directory) {
                $plugins[$name] = array('dir' => substr($directory, $cutcharacters + 1));
            }
            var_export($plugins);
        }
    }
}
