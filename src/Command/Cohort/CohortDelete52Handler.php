<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Cohort;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * cohort:delete implementation for Moodle 5.1.
 */
class CohortDelete52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'cohortid',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Cohort ID(s) to delete',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $cohortIds = $input->getArgument('cohortid');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->dirroot . '/cohort/lib.php';

        // Validate all IDs first.
        foreach ($cohortIds as $id) {
            $id = (int) $id;
            if ($id <= 0) {
                $output->writeln("<error>Invalid cohort ID: $id</error>");
                return Command::FAILURE;
            }
            $record = $DB->get_record('cohort', ['id' => $id]);
            if (!$record) {
                $output->writeln("<error>Cohort with ID $id not found.</error>");
                return Command::FAILURE;
            }
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following cohorts would be deleted (use --run to execute):</info>');
            foreach ($cohortIds as $id) {
                $cohort = $DB->get_record('cohort', ['id' => (int) $id]);
                $output->writeln("  ID={$cohort->id}, name=\"{$cohort->name}\"");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Deleting ' . count($cohortIds) . ' cohort(s)');

        foreach ($cohortIds as $id) {
            $id = (int) $id;
            $cohort = $DB->get_record('cohort', ['id' => $id]);

            $verbose->info("Deleting cohort '{$cohort->name}' (ID={$cohort->id})");
            cohort_delete_cohort($cohort);
            $verbose->done("Deleted cohort ID=$id");

            $output->writeln("Deleted cohort '{$cohort->name}' (ID={$cohort->id}).");
        }

        return Command::SUCCESS;
    }
}
