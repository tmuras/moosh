<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Enrol;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * enrol:delete implementation for Moodle 5.1.
 */
class EnrolDelete52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'instanceid',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Enrolment instance ID(s) to delete',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $instanceIds = $input->getArgument('instanceid');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/enrollib.php';

        // Validate all IDs first.
        foreach ($instanceIds as $id) {
            $id = (int) $id;
            if ($id <= 0) {
                $output->writeln("<error>Invalid enrolment instance ID: $id</error>");
                return Command::FAILURE;
            }
            $record = $DB->get_record('enrol', ['id' => $id]);
            if (!$record) {
                $output->writeln("<error>Enrolment instance with ID $id not found.</error>");
                return Command::FAILURE;
            }
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following enrolment instances would be deleted (use --run to execute):</info>');
            foreach ($instanceIds as $id) {
                $inst = $DB->get_record('enrol', ['id' => (int) $id]);
                $output->writeln("  ID={$inst->id} ({$inst->enrol}, course={$inst->courseid})");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Deleting ' . count($instanceIds) . ' enrolment instance(s)');

        foreach ($instanceIds as $id) {
            $id = (int) $id;
            $inst = $DB->get_record('enrol', ['id' => $id]);

            $verbose->info("Deleting enrolment instance {$inst->id} ({$inst->enrol}, course={$inst->courseid})");
            $plugin = enrol_get_plugin($inst->enrol);
            if ($plugin) {
                $plugin->delete_instance($inst);
            }
            $verbose->done("Deleted enrolment instance ID=$id");

            $output->writeln("Deleted enrolment instance {$inst->id} ({$inst->enrol}, course={$inst->courseid}).");
        }

        return Command::SUCCESS;
    }
}
