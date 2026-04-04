<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Block;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * block:delete implementation for Moodle 5.1.
 */
class BlockDelete52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'instanceid',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Block instance ID(s) to delete',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $instanceIds = $input->getArgument('instanceid');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->dirroot . '/lib/blocklib.php';

        // Validate all IDs first.
        foreach ($instanceIds as $id) {
            $id = (int) $id;
            if ($id <= 0) {
                $output->writeln("<error>Invalid block instance ID: $id</error>");
                return Command::FAILURE;
            }
            $record = $DB->get_record('block_instances', ['id' => $id]);
            if (!$record) {
                $output->writeln("<error>Block instance with ID $id not found.</error>");
                return Command::FAILURE;
            }
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following block instances would be deleted (use --run to execute):</info>');
            foreach ($instanceIds as $id) {
                $instance = $DB->get_record('block_instances', ['id' => (int) $id]);
                $output->writeln("  ID={$instance->id} ({$instance->blockname}, region={$instance->defaultregion})");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Deleting ' . count($instanceIds) . ' block instance(s)');

        foreach ($instanceIds as $id) {
            $id = (int) $id;
            $instance = $DB->get_record('block_instances', ['id' => $id]);

            $verbose->info("Deleting block instance {$instance->id} ({$instance->blockname})");
            blocks_delete_instance($instance);
            $verbose->done("Deleted block instance ID=$id");

            $output->writeln("Deleted block instance {$instance->id} ({$instance->blockname}).");
        }

        return Command::SUCCESS;
    }
}
