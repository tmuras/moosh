<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Filter;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FilterList51Handler extends BaseHandler
{
    // TEXTFILTER_ON=1, OFF=-1, DISABLED=-9999, INHERIT=0
    private const STATE_NAMES = [
        1 => 'on',
        -1 => 'off',
        -9999 => 'disabled',
        0 => 'inherit',
    ];

    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('enabled', null, InputOption::VALUE_NONE, 'Show only enabled (not disabled) filters')
            ->addOption('context', null, InputOption::VALUE_REQUIRED, 'Show filter states for a specific context ID')
            ->addOption('name-only', null, InputOption::VALUE_NONE, 'Show filter names only');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $idOnly = $input->getOption('name-only');
        $onlyEnabled = $input->getOption('enabled');
        $contextId = $input->getOption('context');

        require_once $CFG->libdir . '/filterlib.php';

        $verbose->step('Loading filter information');

        if ($contextId !== null) {
            return $this->listContextFilters((int) $contextId, $idOnly, $format, $output);
        }

        // Global filter listing.
        $allFilters = filter_get_all_installed();
        $globalStates = filter_get_global_states();

        if ($idOnly) {
            foreach ($allFilters as $name => $displayName) {
                if ($onlyEnabled) {
                    $state = $globalStates[$name]->active ?? -9999;
                    if ($state == -9999) {
                        continue;
                    }
                }
                $output->writeln($name);
            }
            return Command::SUCCESS;
        }

        $headers = ['name', 'displayname', 'state', 'sortorder', 'applytostrings'];
        $rows = [];

        foreach ($allFilters as $name => $displayName) {
            $stateRecord = $globalStates[$name] ?? null;
            $stateValue = $stateRecord ? $stateRecord->active : -9999;
            $sortOrder = $stateRecord ? $stateRecord->sortorder : '';
            $applyToStrings = ($stateRecord && !empty($stateRecord->stringstatus)) ? 'yes' : '';

            if ($onlyEnabled && $stateValue == -9999) {
                continue;
            }

            $stateName = self::STATE_NAMES[$stateValue] ?? "unknown($stateValue)";
            $rows[] = [$name, $displayName, $stateName, $sortOrder, $applyToStrings];
        }

        if (empty($rows)) {
            $output->writeln('No filters found matching criteria.');
            return Command::SUCCESS;
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }

    private function listContextFilters(int $contextId, bool $idOnly, string $format, OutputInterface $output): int
    {
        $context = \context::instance_by_id($contextId, IGNORE_MISSING);
        if (!$context) {
            $output->writeln("<error>Context $contextId not found.</error>");
            return Command::FAILURE;
        }

        $available = filter_get_available_in_context($context);

        if ($idOnly) {
            foreach ($available as $name => $info) {
                $output->writeln($name);
            }
            return Command::SUCCESS;
        }

        $headers = ['name', 'localstate', 'inheritedstate'];
        $rows = [];

        foreach ($available as $name => $info) {
            $localState = self::STATE_NAMES[$info->localstate] ?? "unknown({$info->localstate})";
            $inheritedState = self::STATE_NAMES[$info->inheritedstate] ?? "unknown({$info->inheritedstate})";
            $rows[] = [$name, $localState, $inheritedState];
        }

        if (empty($rows)) {
            $output->writeln('No filters available in this context.');
            return Command::SUCCESS;
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
