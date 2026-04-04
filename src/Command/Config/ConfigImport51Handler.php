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

class ConfigImport51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('file', InputArgument::REQUIRED, 'Path to JSON file exported by config:export')
            ->addOption('ignore-existing', null, InputOption::VALUE_NONE, 'Skip settings that already have a value');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $filePath = $input->getArgument('file');
        $ignoreExisting = $input->getOption('ignore-existing');

        if (!file_exists($filePath)) {
            $output->writeln("<error>File not found: $filePath</error>");
            return Command::FAILURE;
        }

        $json = file_get_contents($filePath);
        $data = json_decode($json, true);

        if ($data === null || !isset($data['_type'])) {
            $output->writeln('<error>Invalid JSON or missing _type field.</error>');
            return Command::FAILURE;
        }

        return match ($data['_type']) {
            'core' => $this->importCore($data, $ignoreExisting, $runMode, $output, $verbose),
            'plugin' => $this->importPlugin($data, $ignoreExisting, $runMode, $output, $verbose),
            'all' => $this->importAll($data, $ignoreExisting, $runMode, $output, $verbose),
            default => $this->unknownType($data['_type'], $output),
        };
    }

    private function importCore(array $data, bool $ignoreExisting, bool $runMode, OutputInterface $output, VerboseLogger $verbose): int
    {
        if (!isset($data['settings']) || !is_array($data['settings'])) {
            $output->writeln('<error>Invalid core export: missing settings.</error>');
            return Command::FAILURE;
        }

        return $this->importSettings(null, $data['settings'], $ignoreExisting, $runMode, $output, $verbose);
    }

    private function importPlugin(array $data, bool $ignoreExisting, bool $runMode, OutputInterface $output, VerboseLogger $verbose): int
    {
        if (!isset($data['plugin']) || !isset($data['settings'])) {
            $output->writeln('<error>Invalid plugin export: missing plugin or settings.</error>');
            return Command::FAILURE;
        }

        return $this->importSettings($data['plugin'], (array) $data['settings'], $ignoreExisting, $runMode, $output, $verbose);
    }

    private function importAll(array $data, bool $ignoreExisting, bool $runMode, OutputInterface $output, VerboseLogger $verbose): int
    {
        $totalAdded = 0;
        $totalUpdated = 0;
        $totalSkipped = 0;

        // Core settings.
        if (isset($data['core']) && is_array($data['core'])) {
            $verbose->step('Importing core settings');
            [$added, $updated, $skipped] = $this->countAndApply(null, $data['core'], $ignoreExisting, $runMode, $output, $verbose);
            $totalAdded += $added;
            $totalUpdated += $updated;
            $totalSkipped += $skipped;
        }

        // Plugin settings.
        if (isset($data['plugins']) && is_array($data['plugins'])) {
            foreach ($data['plugins'] as $plugin => $settings) {
                $verbose->step("Importing settings for plugin '$plugin'");
                [$added, $updated, $skipped] = $this->countAndApply($plugin, (array) $settings, $ignoreExisting, $runMode, $output, $verbose);
                $totalAdded += $added;
                $totalUpdated += $updated;
                $totalSkipped += $skipped;
            }
        }

        $this->printSummary($totalAdded, $totalUpdated, $totalSkipped, $runMode, $output);

        if ($runMode) {
            purge_all_caches();
            $verbose->done('Caches purged');
        }

        return Command::SUCCESS;
    }

    private function importSettings(?string $plugin, array $settings, bool $ignoreExisting, bool $runMode, OutputInterface $output, VerboseLogger $verbose): int
    {
        [$added, $updated, $skipped] = $this->countAndApply($plugin, $settings, $ignoreExisting, $runMode, $output, $verbose);

        $pluginLabel = $plugin ?? 'core';
        $this->printSummary($added, $updated, $skipped, $runMode, $output, $pluginLabel);

        if ($runMode) {
            purge_all_caches();
            $verbose->done('Caches purged');
        }

        return Command::SUCCESS;
    }

    /**
     * @return array{int, int, int} [added, updated, skipped]
     */
    private function countAndApply(?string $plugin, array $settings, bool $ignoreExisting, bool $runMode, OutputInterface $output, VerboseLogger $verbose): array
    {
        $added = 0;
        $updated = 0;
        $skipped = 0;
        $pluginArg = $plugin ?? '';

        foreach ($settings as $name => $value) {
            $current = get_config($pluginArg, $name);

            if ($current === false) {
                // New setting.
                $added++;
                if ($runMode) {
                    set_config($name, $value, $plugin);
                }
            } elseif ((string) $current === (string) $value) {
                // Same value — skip silently.
                $skipped++;
            } elseif ($ignoreExisting) {
                // Different but --ignore-existing.
                $skipped++;
                $verbose->info("Skipped $name (existing value preserved)");
            } else {
                // Different — update.
                $updated++;
                if (!$runMode) {
                    $pluginLabel = $plugin ?? 'core';
                    $output->writeln("  [$pluginLabel] $name: \"$current\" → \"$value\"");
                }
                if ($runMode) {
                    set_config($name, $value, $plugin);
                }
            }
        }

        return [$added, $updated, $skipped];
    }

    private function printSummary(int $added, int $updated, int $skipped, bool $runMode, OutputInterface $output, ?string $pluginLabel = null): void
    {
        $prefix = $pluginLabel !== null ? "[$pluginLabel] " : '';

        if (!$runMode) {
            $output->writeln("<info>Dry run — {$prefix}would add $added, update $updated, skip $skipped settings (use --run to execute).</info>");
        } else {
            $output->writeln("{$prefix}Added $added, updated $updated, skipped $skipped settings.");
        }
    }

    private function unknownType(string $type, OutputInterface $output): int
    {
        $output->writeln("<error>Unknown export type: '$type'. Expected core, plugin, or all.</error>");
        return Command::FAILURE;
    }
}
