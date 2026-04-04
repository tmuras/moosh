<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Filter;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FilterMod51Handler extends BaseHandler
{
    // TEXTFILTER_ON=1, OFF=-1, DISABLED=-9999
    private const STATE_MAP = [
        'on' => 1,
        'off' => -1,
        'disabled' => -9999,
    ];

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('filter', InputArgument::REQUIRED, 'Filter name (e.g. mathjaxloader, multilang, tex)')
            ->addOption('state', null, InputOption::VALUE_REQUIRED, 'Set state: on, off, disabled')
            ->addOption('context', null, InputOption::VALUE_REQUIRED, 'Apply to specific context ID (default: global)')
            ->addOption('move', null, InputOption::VALUE_REQUIRED, 'Reorder: up or down')
            ->addOption('apply-to-strings', null, InputOption::VALUE_REQUIRED, 'Set applies to strings: 1 or 0')
            ->addOption('config', null, InputOption::VALUE_REQUIRED, 'Set local config: key=value (requires --context)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');

        $filterName = $input->getArgument('filter');
        $newState = $input->getOption('state');
        $contextId = $input->getOption('context');
        $move = $input->getOption('move');
        $applyToStrings = $input->getOption('apply-to-strings');
        $config = $input->getOption('config');

        require_once $CFG->libdir . '/filterlib.php';

        // Validate filter exists.
        $allFilters = filter_get_all_installed();
        if (!isset($allFilters[$filterName])) {
            $output->writeln("<error>Filter '$filterName' not found. Available: " . implode(', ', array_keys($allFilters)) . "</error>");
            return Command::FAILURE;
        }

        $hasChanges = $newState !== null || $move !== null || $applyToStrings !== null || $config !== null;
        if (!$hasChanges) {
            $output->writeln('<error>No modifications specified. Use --state, --move, --apply-to-strings, or --config.</error>');
            return Command::FAILURE;
        }

        // Validate state.
        if ($newState !== null && !isset(self::STATE_MAP[$newState])) {
            $output->writeln("<error>Invalid state '$newState'. Use: on, off, disabled.</error>");
            return Command::FAILURE;
        }

        // Validate move.
        if ($move !== null && !in_array($move, ['up', 'down'], true)) {
            $output->writeln("<error>Invalid move '$move'. Use: up, down.</error>");
            return Command::FAILURE;
        }

        // Validate config requires context.
        if ($config !== null && $contextId === null) {
            $output->writeln('<error>--config requires --context.</error>');
            return Command::FAILURE;
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would modify filter '$filterName' (use --run to execute):</info>");
            if ($newState !== null) {
                $scope = $contextId !== null ? "context $contextId" : 'global';
                $output->writeln("  state → $newState ($scope)");
            }
            if ($move !== null) { $output->writeln("  move $move"); }
            if ($applyToStrings !== null) { $output->writeln("  apply-to-strings → $applyToStrings"); }
            if ($config !== null) { $output->writeln("  config → $config (context $contextId)"); }
            return Command::SUCCESS;
        }

        $verbose->step("Modifying filter '$filterName'");

        // Set state.
        if ($newState !== null) {
            $stateValue = self::STATE_MAP[$newState];
            if ($contextId !== null) {
                // Local state (per-context).
                $localState = $stateValue == TEXTFILTER_DISABLED ? TEXTFILTER_OFF : $stateValue;
                filter_set_local_state($filterName, (int) $contextId, $localState);
                $verbose->info("Set local state to $newState in context $contextId");
            } else {
                // Global state.
                filter_set_global_state($filterName, $stateValue);
                $verbose->info("Set global state to $newState");
            }
        }

        // Reorder.
        if ($move !== null) {
            $moveValue = $move === 'up' ? -1 : 1;
            // filter_set_global_state needs the current state when reordering.
            $globalStates = filter_get_global_states();
            $currentState = isset($globalStates[$filterName]) ? $globalStates[$filterName]->active : TEXTFILTER_ON;
            filter_set_global_state($filterName, $currentState, $moveValue);
            $verbose->info("Moved $move");
        }

        // Apply to strings.
        if ($applyToStrings !== null) {
            filter_set_applies_to_strings($filterName, (int) $applyToStrings);
            $verbose->info("Set applies-to-strings: $applyToStrings");
        }

        // Local config.
        if ($config !== null) {
            $parts = explode('=', $config, 2);
            if (count($parts) !== 2) {
                $output->writeln('<error>Config must be in key=value format.</error>');
                return Command::FAILURE;
            }
            filter_set_local_config($filterName, (int) $contextId, $parts[0], $parts[1]);
            $verbose->info("Set local config {$parts[0]}={$parts[1]}");
        }

        // Reset caches.
        reset_text_filters_cache();
        \core_plugin_manager::reset_caches();

        $output->writeln("Modified filter '$filterName'.");

        return Command::SUCCESS;
    }
}
