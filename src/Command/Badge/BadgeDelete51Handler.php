<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Badge;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * badge:delete implementation for Moodle 5.1.
 */
class BadgeDelete51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'badgeid',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Badge ID(s) to delete',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $badgeIds = $input->getArgument('badgeid');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/badgeslib.php';
        require_once $CFG->dirroot . '/badges/classes/badge.php';

        // Validate all IDs first.
        foreach ($badgeIds as $id) {
            $id = (int) $id;
            if ($id <= 0) {
                $output->writeln("<error>Invalid badge ID: $id</error>");
                return Command::FAILURE;
            }
            $record = $DB->get_record('badge', ['id' => $id]);
            if (!$record) {
                $output->writeln("<error>Badge with ID $id not found.</error>");
                return Command::FAILURE;
            }
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following badges would be deleted (use --run to execute):</info>');
            foreach ($badgeIds as $id) {
                $record = $DB->get_record('badge', ['id' => (int) $id]);
                $output->writeln("  ID=$id, name=\"{$record->name}\"");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Deleting ' . count($badgeIds) . ' badge(s)');

        foreach ($badgeIds as $id) {
            $id = (int) $id;
            $record = $DB->get_record('badge', ['id' => $id]);
            $badge = new \badge($id);

            $verbose->info("Deleting badge \"{$record->name}\" (ID=$id)");
            $badge->delete(false);
            $verbose->done("Deleted badge ID=$id");

            $output->writeln("Deleted badge \"{$record->name}\" (ID=$id).");
        }

        return Command::SUCCESS;
    }
}
