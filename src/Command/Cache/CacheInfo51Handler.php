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
 * cache:info implementation for Moodle 5.1.
 */
class CacheInfo51Handler extends BaseHandler
{
    private const MODE_NAMES = [
        1 => 'application',
        2 => 'session',
        4 => 'request',
    ];

    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('stores', null, InputOption::VALUE_NONE, 'Show configured stores')
            ->addOption('definitions', null, InputOption::VALUE_NONE, 'Show cache definitions')
            ->addOption('mappings', null, InputOption::VALUE_NONE, 'Show mode and definition mappings')
            ->addOption('locks', null, InputOption::VALUE_NONE, 'Show lock configuration');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');

        $showStores = $input->getOption('stores');
        $showDefs = $input->getOption('definitions');
        $showMappings = $input->getOption('mappings');
        $showLocks = $input->getOption('locks');

        // If no specific flag, show all.
        $showAll = !$showStores && !$showDefs && !$showMappings && !$showLocks;



        $verbose->step('Loading cache configuration');
        $config = \cache_config::instance();

        $formatter = new ResultFormatter($output, $format);
        $sections = 0;

        if ($showAll || $showStores) {
            $this->displayStores($config, $formatter, $output);
            $sections++;
        }

        if ($showAll || $showMappings) {
            if ($sections > 0) {
                $output->writeln('');
            }
            $this->displayMappings($config, $formatter, $output);
            $sections++;
        }

        if ($showAll || $showDefs) {
            if ($sections > 0) {
                $output->writeln('');
            }
            $this->displayDefinitions($config, $formatter, $output);
            $sections++;
        }

        if ($showAll || $showLocks) {
            if ($sections > 0) {
                $output->writeln('');
            }
            $this->displayLocks($config, $formatter, $output);
        }

        return Command::SUCCESS;
    }

    private function displayStores(\cache_config $config, ResultFormatter $formatter, OutputInterface $output): void
    {
        $output->writeln('<info>=== Cache Stores ===</info>');
        $stores = $config->get_all_stores();
        $headers = ['name', 'plugin', 'modes', 'default'];
        $rows = [];
        foreach ($stores as $name => $store) {
            $modes = [];
            foreach (self::MODE_NAMES as $bit => $label) {
                if ($store['modes'] & $bit) {
                    $modes[] = $label;
                }
            }
            $rows[] = [
                $name,
                $store['plugin'],
                implode(', ', $modes),
                !empty($store['default']) ? 'yes' : 'no',
            ];
        }
        $formatter->display($headers, $rows);
    }

    private function displayMappings(\cache_config $config, ResultFormatter $formatter, OutputInterface $output): void
    {
        $output->writeln('<info>=== Mode Mappings ===</info>');
        $modeMappings = $config->get_mode_mappings();
        $headers = ['mode', 'store'];
        $rows = [];
        foreach ($modeMappings as $mapping) {
            $modeName = self::MODE_NAMES[$mapping['mode']] ?? "mode_{$mapping['mode']}";
            $rows[] = [$modeName, $mapping['store']];
        }
        $formatter->display($headers, $rows);

        $defMappings = $config->get_definition_mappings();
        if (!empty($defMappings)) {
            $output->writeln('');
            $output->writeln('<info>=== Definition Mappings ===</info>');
            $headers = ['definition', 'store'];
            $rows = [];
            foreach ($defMappings as $mapping) {
                $rows[] = [$mapping['definition'], $mapping['store']];
            }
            $formatter->display($headers, $rows);
        }
    }

    private function displayDefinitions(\cache_config $config, ResultFormatter $formatter, OutputInterface $output): void
    {
        $output->writeln('<info>=== Cache Definitions ===</info>');
        $definitions = $config->get_definitions();
        $headers = ['id', 'mode', 'component', 'area'];
        $rows = [];
        foreach ($definitions as $id => $def) {
            $modeName = self::MODE_NAMES[$def['mode']] ?? "mode_{$def['mode']}";
            $rows[] = [$id, $modeName, $def['component'], $def['area']];
        }
        $formatter->display($headers, $rows);
    }

    private function displayLocks(\cache_config $config, ResultFormatter $formatter, OutputInterface $output): void
    {
        $output->writeln('<info>=== Cache Locks ===</info>');
        $locks = $config->get_locks();
        $headers = ['name', 'type', 'default'];
        $rows = [];
        foreach ($locks as $name => $lock) {
            $rows[] = [$name, $lock['type'], !empty($lock['default']) ? 'yes' : 'no'];
        }
        $formatter->display($headers, $rows);
    }
}
