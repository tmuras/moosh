<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Plugin;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PluginUninstall51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('plugin', InputArgument::REQUIRED, 'Frankenstyle plugin name (e.g. mod_attendance)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $pluginName = $input->getArgument('plugin');

        require_once $CFG->libdir . '/adminlib.php';
        require_once $CFG->libdir . '/upgradelib.php';
        require_once $CFG->libdir . '/filelib.php';

        $pluginman = \core_plugin_manager::instance();

        $pluginfo = $pluginman->get_plugin_info($pluginName);
        if ($pluginfo === null) {
            $output->writeln("<error>Plugin '$pluginName' not found.</error>");
            return Command::FAILURE;
        }

        $canUninstall = $pluginman->can_uninstall_plugin($pluginfo->component);

        if (empty($pluginfo->rootdir)) {
            $pluginfo->rootdir = $pluginfo->typerootdir . '/' . $pluginfo->name;
        }

        $folderExists = file_exists($pluginfo->rootdir);
        $folderRemovable = $folderExists && $pluginman->is_plugin_folder_removable($pluginfo->component);

        if (!$runMode) {
            $output->writeln('<info>Dry run — would uninstall the following plugin (use --run to execute):</info>');
            $output->writeln("  plugin:    {$pluginfo->component}");
            $output->writeln("  directory: {$pluginfo->rootdir}");
            $output->writeln("  exists:    " . ($folderExists ? 'yes' : 'no'));
            if ($folderExists) {
                $output->writeln("  removable: " . ($folderRemovable ? 'yes' : 'no'));
            }
            $output->writeln("  can uninstall: " . ($canUninstall ? 'yes' : 'no'));
            if (!$canUninstall) {
                $output->writeln('  <comment>Warning: Moodle reports this plugin cannot be uninstalled.</comment>');
            }
            return Command::SUCCESS;
        }

        if (!$canUninstall) {
            // Try to clean up improperly installed plugins
            if ($DB->get_record('config_plugins', ['plugin' => $pluginfo->component])) {
                $verbose->step('Plugin not properly installed, removing config_plugins records');
                $DB->delete_records('config_plugins', ['plugin' => $pluginfo->component]);
                upgrade_noncore(true);
                $output->writeln("Removed orphan config records for {$pluginfo->component}.");
                return Command::SUCCESS;
            }

            $output->writeln("<error>Cannot uninstall plugin '{$pluginfo->component}'.</error>");
            return Command::FAILURE;
        }

        $verbose->step("Uninstalling {$pluginfo->component}");

        $progress = new \progress_trace_buffer(new \text_progress_trace(), false);
        $pluginman->uninstall_plugin($pluginfo->component, $progress);
        $progress->finished();

        if ($folderExists) {
            $verbose->step("Deleting directory {$pluginfo->rootdir}");
            fulldelete($pluginfo->rootdir);
        }

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        $verbose->step('Finalizing');
        $cacheFile = $CFG->cachedir . '/core_component.php';
        if (file_exists($cacheFile)) {
            @unlink($cacheFile);
        }
        \core_component::reset(true);
        \core_plugin_manager::reset_caches();
        upgrade_noncore(true);

        $verbose->done("Plugin {$pluginfo->component} uninstalled successfully");
        $output->writeln("Uninstalled {$pluginfo->component}.");

        return Command::SUCCESS;
    }
}
