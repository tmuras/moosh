<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Grouping;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * grouping:delete implementation for Moodle 5.1.
 */
class GroupingDelete51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'groupingid',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Grouping ID(s) to delete',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $ids = $input->getArgument('groupingid');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->dirroot . '/group/lib.php';

        // Validate all IDs first.
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id <= 0) {
                $output->writeln("<error>Invalid grouping ID: $id</error>");
                return Command::FAILURE;
            }
            $grouping = groups_get_grouping($id);
            if (!$grouping) {
                $output->writeln("<error>Grouping with ID $id not found.</error>");
                return Command::FAILURE;
            }
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following groupings would be deleted (use --run to execute):</info>');
            foreach ($ids as $id) {
                $grouping = groups_get_grouping((int) $id);
                $output->writeln("  ID=$id, name=\"{$grouping->name}\"");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Deleting ' . count($ids) . ' grouping(s)');

        foreach ($ids as $id) {
            $id = (int) $id;
            $grouping = groups_get_grouping($id);

            $verbose->info("Deleting grouping '{$grouping->name}' (ID=$id)");
            groups_delete_grouping($id);
            $verbose->done("Deleted grouping ID=$id");

            $output->writeln("Deleted grouping '{$grouping->name}' (ID=$id).");
        }

        return Command::SUCCESS;
    }
}
