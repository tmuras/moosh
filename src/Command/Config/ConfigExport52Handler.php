<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Config;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigExport52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('file', InputArgument::OPTIONAL, 'Output file path (default: stdout)')
            ->addOption('plugin', null, InputOption::VALUE_REQUIRED, 'Plugin component (e.g. mod_forum) or "all" for all plugins');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $file = $input->getArgument('file');
        $plugin = $input->getOption('plugin');

        if ($plugin === 'all') {
            $data = $this->exportAll($verbose);
        } elseif ($plugin !== null) {
            $data = $this->exportPlugin($plugin, $verbose);
        } else {
            $data = $this->exportCore($verbose);
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($file !== null) {
            file_put_contents($file, $json . "\n");
            $output->writeln("<info>Exported to $file</info>");
        } else {
            $output->writeln($json);
        }

        return Command::SUCCESS;
    }

    private function exportCore(VerboseLogger $verbose): array
    {
        $verbose->step('Exporting core configuration');
        $config = get_config('');
        $settings = [];
        foreach ((array) $config as $name => $value) {
            $settings[$name] = $value;
        }
        ksort($settings);
        $verbose->done('Exported ' . count($settings) . ' core settings');

        return [
            '_type' => 'core',
            'settings' => $settings,
        ];
    }

    private function exportPlugin(string $plugin, VerboseLogger $verbose): array
    {
        $verbose->step("Exporting configuration for plugin '$plugin'");
        $config = get_config($plugin);

        if ($config === false || empty((array) $config)) {
            return [
                '_type' => 'plugin',
                'plugin' => $plugin,
                'settings' => (object) [],
            ];
        }

        $settings = [];
        foreach ((array) $config as $name => $value) {
            $settings[$name] = $value;
        }
        ksort($settings);
        $verbose->done('Exported ' . count($settings) . " settings for '$plugin'");

        return [
            '_type' => 'plugin',
            'plugin' => $plugin,
            'settings' => $settings,
        ];
    }

    private function exportAll(VerboseLogger $verbose): array
    {
        global $DB;

        $verbose->step('Exporting all configuration');

        // Core settings.
        $coreData = $this->exportCore($verbose);

        // All plugin settings.
        $verbose->step('Discovering plugins with configuration');
        $plugins = $DB->get_records_sql(
            'SELECT DISTINCT plugin FROM {config_plugins} ORDER BY plugin',
        );

        $pluginSettings = [];
        foreach ($plugins as $record) {
            $pluginName = $record->plugin;
            $config = get_config($pluginName);
            $settings = [];
            foreach ((array) $config as $name => $value) {
                $settings[$name] = $value;
            }
            ksort($settings);
            $pluginSettings[$pluginName] = $settings;
        }

        ksort($pluginSettings);
        $verbose->done('Exported settings for ' . count($pluginSettings) . ' plugins');

        return [
            '_type' => 'all',
            'core' => $coreData['settings'],
            'plugins' => $pluginSettings,
        ];
    }
}
