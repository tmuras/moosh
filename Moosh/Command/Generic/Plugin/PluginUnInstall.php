<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @pluginauthor     Nadav kavalerchik <nadavkav@gmail.com>
 */

namespace Moosh\Command\Generic\Plugin;
use Moosh\MooshCommand;
use core_plugin_manager, moodle_exception, progress_trace_buffer, text_progress_trace;

class PluginUnInstall extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('uninstall', 'plugin');

        $this->addArgument('plugin_name');
    }

    public function execute()
    {
        global $CFG, $DB;
        $CFG->debugdisplay = 0;

        require_once($CFG->libdir.'/adminlib.php');       // various admin-only functions
        require_once($CFG->libdir.'/upgradelib.php');     // general upgrade/install related functions
        require_once($CFG->libdir.'/filelib.php');

        $pluginname = $this->arguments[0];

        if ($pluginname) {

            $pluginman = core_plugin_manager::instance();

            $pluginfo = $pluginman->get_plugin_info($pluginname);

            // Make sure we know the plugin.
            if (is_null($pluginfo)) {
                echo 'core_plugin_manager::get_plugin_info() returned null for the plugin to be uninstalled'. PHP_EOL;
                if ($CFG->debugdisplay) {
                    throw new moodle_exception('err_uninstalling_unknown_plugin', 'core_plugin', '', array('plugin' => $pluginname),
                        'core_plugin_manager::get_plugin_info() returned null for the plugin to be uninstalled');
                }
                die;
            }

            $pluginname = $pluginman->plugin_name($pluginfo->component);
            echo "Uninstalling {$pluginname} plugin.\n";

            if (!$pluginman->can_uninstall_plugin($pluginfo->component)) {
                // Remove it form DB. if it is not installed properly.
                if ($DB->get_record('config_plugins', array('plugin' => $pluginfo->component))) {
                    $ok = $DB->delete_records('config_plugins', array('plugin' => $pluginfo->component));
                    echo 'Plugin not installed properly, deleting redundant plugin records from mdl_config_plugins'. PHP_EOL;
                    upgrade_noncore(true);
                    die;
                }
                echo 'core_plugin_manager::can_uninstall_plugin() returned false'. PHP_EOL;
                if ($CFG->debugdisplay) {
                    throw new moodle_exception('err_cannot_uninstall_plugin', 'core_plugin', '',
                        array('plugin' => $pluginfo->component),
                        'core_plugin_manager::can_uninstall_plugin() returned false');
                }
                die;
            }

            // Make sure it is not installed.
            if (is_null($pluginfo->versiondb)) {
                echo 'core_plugin_manager::get_plugin_info() returned not-null versiondb for the plugin to be deleted'. PHP_EOL;
                if ($CFG->debugdisplay) {
                    throw new moodle_exception('err_removing_installed_plugin', 'core_plugin', '',
                        array('plugin' => $pluginfo->component, 'versiondb' => $pluginfo->versiondb),
                        'core_plugin_manager::get_plugin_info() returned not-null versiondb for the plugin to be deleted');
                }
                die;
            }

            if (empty($pluginfo->rootdir)) {
                $pluginfo->rootdir = $pluginfo->typerootdir.'/'.$pluginfo->name; //$CFG->dirroot.'/'.$pluginfo->type;
            }

            if (file_exists($pluginfo->rootdir)) {
                // Make sure the folder is removable.
                if (!$pluginman->is_plugin_folder_removable($pluginfo->component)) {
                    echo 'plugin root folder is not removable as expected '. PHP_EOL;
                    echo ' Use CLI "sudo chmod -R +w '.$pluginfo->rootdir.'" to enable write (delete) permission on that folder'. PHP_EOL;
                    if ($CFG->debugdisplay) {
                        throw new moodle_exception('err_removing_unremovable_folder', 'core_plugin', '',
                            array('plugin' => $pluginfo->component, 'rootdir' => $pluginfo->rootdir),
                            'plugin root folder is not removable as expected'. PHP_EOL.
                            ' Use CLI "sudo chmod -R +w '.$pluginfo->rootdir.'" to enable write (delete) permission on that folder');
                    }
                    die;
                }
            } else {
                echo 'Plugin folder ('.$pluginfo->rootdir.') does not exist, skipping...'. PHP_EOL;
            }

            // Make sure the folder is within Moodle installation tree.
            if (strpos($pluginfo->rootdir, $CFG->dirroot) !== 0) {
                echo 'plugin root folder not in the moodle dirroot'. PHP_EOL;
                if ($CFG->debugdisplay) {
                    throw new moodle_exception('err_unexpected_plugin_rootdir', 'core_plugin', '',
                        array('plugin' => $pluginfo->component, 'rootdir' => $pluginfo->rootdir, 'dirroot' => $CFG->dirroot),
                        'plugin root folder not in the moodle dirroot');
                }
                die;
            }

            echo 'Uninstalling plugins...'. PHP_EOL;
            $progress = new progress_trace_buffer(new text_progress_trace(), false);
            $pluginman->uninstall_plugin($pluginfo->component, $progress);
            $progress->finished();

            // So long, and thanks for all the bugs.
            if ($CFG->debugdisplay) {
                print_r($pluginfo);
            }

            echo "Deleting folder: {$pluginfo->rootdir} (if exists)". PHP_EOL ;
            fulldelete($pluginfo->rootdir);

            // Reset op code caches.
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }

            echo "Upgrade noncore.". PHP_EOL;
            upgrade_noncore(true);

            echo "Uninstalled :-) So long, and thanks for all the bugs.". PHP_EOL;

        }
    }

}
