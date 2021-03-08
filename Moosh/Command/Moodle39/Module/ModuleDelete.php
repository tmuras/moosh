<?php
/**
 * `moosh delete-missingplugins`
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Module;
use Moosh\MooshCommand;

class ModuleDelete extends MooshCommand {
    public function __construct() {
        parent::__construct('missingplugins', 'delete');
        $this->addOption('v|verbose', 'verbose');
    }

    public function bootstrapLevel() {
        return self::$BOOTSTRAP_FULL_NOCLI;
    }

    public function execute() {
        global $CFG;
        require_once($CFG->dirroot.'/lib/adminlib.php');
        $verbose = $this->expandedOptions['verbose'];
        $pluginmanager = \core_plugin_manager::instance();
        $plugininfo = $pluginmanager->get_plugins();
        foreach ($plugininfo as $type => $plugins) {
            foreach ($plugins as $pluginname => $plugin) {
                $status = $plugin->get_status();
                if ($status === \core_plugin_manager::PLUGIN_STATUS_MISSING) {
                    if ($verbose) {
                        printf("uninstalling: %s\n",$plugin->component);
                    }
                    # code taken from admin/plugin.php lines 83 - 99
                    if (!$pluginmanager->can_uninstall_plugin($plugin->component)) {
                        cli_problem(sprintf("Warning: uninstall is not allowed for %s", $plugin->component));
                        continue;
                    }
                    $progress = new \progress_trace_buffer(new \text_progress_trace(), false);
                    $pluginmanager->uninstall_plugin($plugin->component, $progress);
                    $progress->finished();
                    if ($verbose) {
                        printf("success\n");
                    }
                }
            }
        }
        if ($verbose) {
            printf("all done\n");
        }   
    }
}
