<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Group;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * group:delete implementation for Moodle 5.1.
 */
class GroupDelete51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'groupid',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Group ID(s) to delete',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $groupIds = $input->getArgument('groupid');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->dirroot . '/group/lib.php';

        // Validate all IDs first.
        foreach ($groupIds as $id) {
            $id = (int) $id;
            if ($id <= 0) {
                $output->writeln("<error>Invalid group ID: $id</error>");
                return Command::FAILURE;
            }
            $group = groups_get_group($id);
            if (!$group) {
                $output->writeln("<error>Group with ID $id not found.</error>");
                return Command::FAILURE;
            }
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following groups would be deleted (use --run to execute):</info>');
            foreach ($groupIds as $id) {
                $group = groups_get_group((int) $id);
                $output->writeln("  ID={$group->id}, name=\"{$group->name}\"");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Deleting ' . count($groupIds) . ' group(s)');

        foreach ($groupIds as $id) {
            $id = (int) $id;
            $group = groups_get_group($id);

            $verbose->info("Deleting group '{$group->name}' (ID={$group->id})");
            groups_delete_group($id);
            $verbose->done("Deleted group ID=$id");

            $output->writeln("Deleted group '{$group->name}' (ID={$group->id}).");
        }

        return Command::SUCCESS;
    }
}
