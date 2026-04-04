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

class PluginReinstall52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'plugin',
            InputArgument::REQUIRED,
            'Frankenstyle plugin name (e.g. mod_attendance)',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $pluginName = $input->getArgument('plugin');

        $parts = explode('_', $pluginName, 2);
        if (count($parts) < 2) {
            $output->writeln("<error>Invalid plugin name '$pluginName'. Use frankenstyle format (e.g. mod_attendance).</error>");
            return Command::FAILURE;
        }

        [$type, $name] = $parts;

        require_once $CFG->libdir . '/adminlib.php';
        require_once $CFG->libdir . '/upgradelib.php';

        // Verify plugin exists on disk.
        $pluginManager = \core_plugin_manager::instance();
        $pluginInfo = $pluginManager->get_plugin_info($pluginName);
        if ($pluginInfo === null) {
            $output->writeln("<error>Plugin '$pluginName' not found.</error>");
            return Command::FAILURE;
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would uninstall and reinstall '$pluginName' (use --run to execute).</info>");
            $output->writeln("  Type: $type");
            $output->writeln("  Name: $name");
            $output->writeln("  Directory: {$pluginInfo->rootdir}");
            return Command::SUCCESS;
        }

        $verbose->step("Uninstalling '$pluginName'");

        $progress = new \progress_trace_buffer(new \text_progress_trace(), false);
        uninstall_plugin($type, $name);
        $progress->finished();

        $verbose->done('Plugin uninstalled');

        // Reset caches so Moodle detects the plugin as new.
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        \core_component::reset();
        \core_plugin_manager::reset_caches();

        $verbose->step("Reinstalling '$pluginName' via upgrade");

        upgrade_noncore(true);

        $verbose->done('Plugin reinstalled');
        $output->writeln("Reinstalled '$pluginName'.");

        return Command::SUCCESS;
    }
}
