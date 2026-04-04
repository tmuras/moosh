<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Block;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * block:mod implementation for Moodle 5.1.
 */
class BlockMod51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('instanceid', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Block instance ID(s)')
            ->addOption('region', null, InputOption::VALUE_REQUIRED, 'Move to region (side-pre, side-post, content)')
            ->addOption('weight', null, InputOption::VALUE_REQUIRED, 'Set weight/position')
            ->addOption('pagetypepattern', null, InputOption::VALUE_REQUIRED, 'Change page type pattern')
            ->addOption('showinsubcontexts', null, InputOption::VALUE_REQUIRED, 'Set showinsubcontexts (1 or 0)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $instanceIds = $input->getArgument('instanceid');
        $newRegion = $input->getOption('region');
        $newWeight = $input->getOption('weight');
        $newPagetype = $input->getOption('pagetypepattern');
        $newSubcontexts = $input->getOption('showinsubcontexts');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->dirroot . '/lib/blocklib.php';

        // Check that at least one modification was requested.
        if ($newRegion === null && $newWeight === null && $newPagetype === null && $newSubcontexts === null) {
            $output->writeln('<error>No modifications specified. Use --region, --weight, --pagetypepattern, or --showinsubcontexts.</error>');
            return Command::FAILURE;
        }

        // Validate all instance IDs.
        $instances = [];
        foreach ($instanceIds as $id) {
            $id = (int) $id;
            if ($id <= 0) {
                $output->writeln("<error>Invalid block instance ID: $id</error>");
                return Command::FAILURE;
            }
            $instance = $DB->get_record('block_instances', ['id' => $id]);
            if (!$instance) {
                $output->writeln("<error>Block instance with ID $id not found.</error>");
                return Command::FAILURE;
            }
            $instances[] = $instance;
        }

        // Handle modifications.
        return $this->handleModify($instances, $newRegion, $newWeight, $newPagetype, $newSubcontexts,
            $runMode, $format, $output, $verbose);
    }

    private function handleModify(
        array $instances,
        ?string $newRegion,
        ?string $newWeight,
        ?string $newPagetype,
        ?string $newSubcontexts,
        bool $runMode,
        string $format,
        OutputInterface $output,
        VerboseLogger $verbose,
    ): int {
        global $DB;

        // Build summary of changes.
        $changes = [];
        if ($newRegion !== null) {
            $changes[] = "region → $newRegion";
        }
        if ($newWeight !== null) {
            $changes[] = "weight → $newWeight";
        }
        if ($newPagetype !== null) {
            $changes[] = "pagetypepattern → $newPagetype";
        }
        if ($newSubcontexts !== null) {
            $changes[] = "showinsubcontexts → $newSubcontexts";
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following modification(s) would be applied (use --run to execute):</info>');
            foreach ($instances as $instance) {
                $output->writeln("  ID={$instance->id} ({$instance->blockname}): " . implode(', ', $changes));
            }
            return Command::SUCCESS;
        }

        $verbose->step('Modifying block instance(s)');
        $rows = [];

        foreach ($instances as $instance) {
            $update = new \stdClass();
            $update->id = $instance->id;
            $update->timemodified = time();

            if ($newRegion !== null) {
                $update->defaultregion = $newRegion;
            }
            if ($newWeight !== null) {
                $update->defaultweight = (int) $newWeight;
            }
            if ($newPagetype !== null) {
                $update->pagetypepattern = $newPagetype;
            }
            if ($newSubcontexts !== null) {
                $update->showinsubcontexts = (int) $newSubcontexts;
            }

            $DB->update_record('block_instances', $update);
            $verbose->info("Modified instance {$instance->id}");

            // Reload for output.
            $updated = $DB->get_record('block_instances', ['id' => $instance->id]);
            $rows[] = [
                $updated->id,
                $updated->blockname,
                $updated->defaultregion,
                $updated->defaultweight,
                $updated->pagetypepattern,
                $updated->showinsubcontexts,
            ];
        }

        $headers = ['id', 'blockname', 'region', 'weight', 'pagetypepattern', 'showinsubcontexts'];
        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
