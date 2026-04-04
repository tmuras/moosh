<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Cache;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * cache:mod implementation for Moodle 5.1.
 */
class CacheMod51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('application', null, InputOption::VALUE_REQUIRED, 'Set application mode store')
            ->addOption('session', null, InputOption::VALUE_REQUIRED, 'Set session mode store')
            ->addOption('request', null, InputOption::VALUE_REQUIRED, 'Set request mode store')
            ->addOption('definition', null, InputOption::VALUE_REQUIRED, 'Definition ID to map (component/area)')
            ->addOption('store', null, InputOption::VALUE_REQUIRED, 'Store name(s) for definition mapping');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $appStore = $input->getOption('application');
        $sessStore = $input->getOption('session');
        $reqStore = $input->getOption('request');
        $definition = $input->getOption('definition');
        $store = $input->getOption('store');



        $hasModeMappings = $appStore !== null || $sessStore !== null || $reqStore !== null;
        $hasDefMapping = $definition !== null;

        if (!$hasModeMappings && !$hasDefMapping) {
            $output->writeln('<error>Specify --application/--session/--request for mode mappings, or --definition + --store for definition mapping.</error>');
            return Command::FAILURE;
        }

        if ($hasDefMapping && $store === null) {
            $output->writeln('<error>--definition requires --store.</error>');
            return Command::FAILURE;
        }

        $config = \cache_config::instance();
        $allStores = $config->get_all_stores();

        // Handle mode mappings.
        if ($hasModeMappings) {
            return $this->handleModeMappings($appStore, $sessStore, $reqStore, $allStores, $config, $runMode, $format, $output, $verbose);
        }

        // Handle definition mapping.
        return $this->handleDefinitionMapping($definition, $store, $allStores, $config, $runMode, $format, $output, $verbose);
    }

    private function handleModeMappings(
        ?string $appStore,
        ?string $sessStore,
        ?string $reqStore,
        array $allStores,
        \cache_config $config,
        bool $runMode,
        string $format,
        OutputInterface $output,
        VerboseLogger $verbose,
    ): int {
        // Validate store names.
        foreach (['application' => $appStore, 'session' => $sessStore, 'request' => $reqStore] as $mode => $storeName) {
            if ($storeName !== null && !isset($allStores[$storeName])) {
                $output->writeln("<error>Cache store '$storeName' not found (for $mode mode).</error>");
                return Command::FAILURE;
            }
        }

        // Build mappings in Moodle 5.1 format: [MODE_CONSTANT => ['store1', ...]]
        $currentMappings = $config->get_mode_mappings();
        $modeMap = [
            \cache_store::MODE_APPLICATION => 'application',
            \cache_store::MODE_SESSION => 'session',
            \cache_store::MODE_REQUEST => 'request',
        ];
        $newValues = [
            'application' => $appStore,
            'session' => $sessStore,
            'request' => $reqStore,
        ];

        // Start from current mappings grouped by mode.
        $grouped = [];
        foreach ($currentMappings as $mapping) {
            $mode = $mapping['mode'];
            $grouped[$mode][] = $mapping['store'];
        }

        // Apply overrides.
        foreach ($modeMap as $modeConst => $modeName) {
            if ($newValues[$modeName] !== null) {
                $grouped[$modeConst] = [$newValues[$modeName]];
            }
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — would set mode mappings (use --run to execute):</info>');
            foreach ($grouped as $modeConst => $stores) {
                $modeName = $modeMap[$modeConst] ?? "mode_$modeConst";
                $output->writeln("  $modeName → " . implode(', ', $stores));
            }
            return Command::SUCCESS;
        }

        $verbose->step('Setting mode mappings');
        $writer = \cache_config_writer::instance();
        $writer->set_mode_mappings($grouped);

        $formatter = new ResultFormatter($output, $format);
        $headers = ['mode', 'store'];
        $rows = [];
        foreach ($grouped as $modeConst => $stores) {
            $modeName = $modeMap[$modeConst] ?? "mode_$modeConst";
            $rows[] = [$modeName, implode(', ', $stores)];
        }
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }

    private function handleDefinitionMapping(
        string $definition,
        string $store,
        array $allStores,
        \cache_config $config,
        bool $runMode,
        string $format,
        OutputInterface $output,
        VerboseLogger $verbose,
    ): int {
        // Validate definition exists.
        $definitions = $config->get_definitions();
        if (!isset($definitions[$definition])) {
            $output->writeln("<error>Cache definition '$definition' not found.</error>");
            return Command::FAILURE;
        }

        // Validate store(s).
        $storeNames = array_map('trim', explode(',', $store));
        foreach ($storeNames as $sn) {
            if (!isset($allStores[$sn])) {
                $output->writeln("<error>Cache store '$sn' not found.</error>");
                return Command::FAILURE;
            }
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would map definition '$definition' to store(s): $store (use --run to execute).</info>");
            return Command::SUCCESS;
        }

        $verbose->step("Mapping definition '$definition' to '$store'");

        $writer = \cache_config_writer::instance();
        $writer->set_definition_mappings($definition, $storeNames);

        $formatter = new ResultFormatter($output, $format);
        $headers = ['definition', 'store'];
        $rows = [];
        foreach ($storeNames as $sn) {
            $rows[] = [$definition, $sn];
        }
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
