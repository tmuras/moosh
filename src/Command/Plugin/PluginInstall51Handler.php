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
use Moosh2\Service\PluginApiClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PluginInstall51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('plugin', InputArgument::REQUIRED, 'Frankenstyle plugin name (e.g. mod_attendance)')
            ->addOption('release', null, InputOption::VALUE_REQUIRED, 'Specific plugin version number (e.g. 2024010700)')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force install even if Moodle version is unsupported')
            ->addOption('delete', 'd', InputOption::VALUE_NONE, 'Remove existing plugin directory before installing')
            ->addOption('proxy', null, InputOption::VALUE_REQUIRED, 'Proxy URI (e.g. tcp://user:pass@host:port)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $pluginName = $input->getArgument('plugin');
        $releaseVersion = $input->getOption('release');
        $force = $input->getOption('force');
        $delete = $input->getOption('delete');
        $proxy = $input->getOption('proxy');

        require_once $CFG->libdir . '/adminlib.php';
        require_once $CFG->libdir . '/upgradelib.php';
        require_once $CFG->libdir . '/filelib.php';

        $moodleRelease = moodle_major_version();

        $verbose->step("Resolving plugin $pluginName for Moodle $moodleRelease");

        $client = new PluginApiClient($proxy);

        try {
            $version = $client->findBestVersion($pluginName, (string) $moodleRelease, $releaseVersion, $force);
        } catch (\RuntimeException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        // Determine installation path
        $split = explode('_', $pluginName, 2);
        if (count($split) !== 2) {
            $output->writeln("<error>Invalid plugin name '$pluginName'. Expected format: type_name (e.g. mod_attendance).</error>");
            return Command::FAILURE;
        }

        [$type, $component] = $split;
        $pluginTypes = \core_component::get_plugin_types();

        if (!isset($pluginTypes[$type])) {
            $output->writeln("<error>Unknown plugin type '$type'.</error>");
            return Command::FAILURE;
        }

        $installPath = $pluginTypes[$type];
        $targetPath = $installPath . DIRECTORY_SEPARATOR . $component;

        $exists = file_exists($targetPath);

        if (!$runMode) {
            $output->writeln('<info>Dry run — would install the following plugin (use --run to execute):</info>');
            $output->writeln("  plugin:   $pluginName");
            $output->writeln("  version:  {$version->version}");
            $output->writeln("  url:      {$version->downloadurl}");
            $output->writeln("  target:   $targetPath");
            if ($exists && $delete) {
                $output->writeln("  action:   Delete existing directory and reinstall");
            } elseif ($exists) {
                $output->writeln("  warning:  Target directory already exists (use --delete to overwrite)");
            }
            return Command::SUCCESS;
        }

        if ($exists && !$delete) {
            $output->writeln("<error>Directory already exists at $targetPath. Use --delete to remove it first.</error>");
            return Command::FAILURE;
        }

        // Download to temp directory
        $tempDir = sys_get_temp_dir() . '/moosh_plugin_' . uniqid();
        mkdir($tempDir, 0755, true);
        $zipFile = $tempDir . '/' . $component . '.zip';

        $verbose->step('Downloading plugin');
        try {
            $client->downloadFile($version->downloadurl, $zipFile);
        } catch (\RuntimeException $e) {
            $this->cleanup($tempDir);
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        // Extract ZIP
        $verbose->step('Extracting archive');
        $extractDir = $tempDir . '/extracted';
        mkdir($extractDir, 0755, true);

        $zip = new \ZipArchive();
        if ($zip->open($zipFile) !== true) {
            $this->cleanup($tempDir);
            $output->writeln('<error>Failed to open ZIP archive.</error>');
            return Command::FAILURE;
        }
        $zip->extractTo($extractDir);
        $zip->close();

        // Find the directory containing version.php
        $pluginDir = $this->findPluginDir($extractDir);
        if ($pluginDir === null) {
            $this->cleanup($tempDir);
            $output->writeln('<error>The ZIP does not contain a valid plugin (no version.php found).</error>');
            return Command::FAILURE;
        }

        // Remove existing if --delete
        if ($exists && $delete) {
            $verbose->step("Removing existing directory $targetPath");
            \fulldelete($targetPath);
        }

        // Move to target
        $verbose->step("Installing to $targetPath");
        rename($pluginDir, $targetPath);

        // Cleanup temp
        $this->cleanup($tempDir);

        // Run Moodle upgrade — must fully reset component caches so Moodle sees the new plugin.
        // The on-disk core_component.php cache must be deleted first, otherwise
        // core_component::init() reloads the stale cached plugin list.
        $verbose->step('Running upgrade_noncore()');
        $cacheFile = $CFG->cachedir . '/core_component.php';
        if (file_exists($cacheFile)) {
            @unlink($cacheFile);
        }
        \core_component::reset(true);
        \core_plugin_manager::reset_caches();
        upgrade_noncore(true);

        $verbose->done("Plugin $pluginName version {$version->version} installed successfully");
        $output->writeln("Installed $pluginName ({$version->version}) to $targetPath.");

        return Command::SUCCESS;
    }

    private function findPluginDir(string $dir): ?string
    {
        if (file_exists($dir . '/version.php')) {
            return $dir;
        }

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $found = $this->findPluginDir($path);
                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }

    private function cleanup(string $dir): void
    {
        if (function_exists('fulldelete')) {
            fulldelete($dir);
        } elseif (is_dir($dir)) {
            $items = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST,
            );
            foreach ($items as $item) {
                $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
            }
            rmdir($dir);
        }
    }
}
