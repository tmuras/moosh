<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Badge;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * badge:mod implementation for Moodle 5.1.
 */
class BadgeMod51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('badgeid', InputArgument::REQUIRED, 'Badge ID to modify')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Set badge name')
            ->addOption('description', 'd', InputOption::VALUE_REQUIRED, 'Set badge description')
            ->addOption('status', null, InputOption::VALUE_REQUIRED, 'Set status: active or inactive');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $runMode = $input->getOption('run');

        $badgeId = (int) $input->getArgument('badgeid');
        $newName = $input->getOption('name');
        $newDescription = $input->getOption('description');
        $newStatus = $input->getOption('status');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/badgeslib.php';
        require_once $CFG->dirroot . '/badges/classes/badge.php';

        $record = $DB->get_record('badge', ['id' => $badgeId]);
        if (!$record) {
            $output->writeln("<error>Badge with ID $badgeId not found.</error>");
            return Command::FAILURE;
        }

        if ($newName === null && $newDescription === null && $newStatus === null) {
            $output->writeln('<error>No modifications specified. Use --name, --description, or --status.</error>');
            return Command::FAILURE;
        }

        if ($newStatus !== null && !in_array($newStatus, ['active', 'inactive'], true)) {
            $output->writeln("<error>Invalid status '$newStatus'. Allowed: active, inactive.</error>");
            return Command::FAILURE;
        }

        // Build change summary.
        $changes = [];
        if ($newName !== null) {
            $changes[] = "name: \"{$record->name}\" -> \"$newName\"";
        }
        if ($newDescription !== null) {
            $changes[] = 'description: (updated)';
        }
        if ($newStatus !== null) {
            $currentStatus = $record->status == BADGE_STATUS_ACTIVE ? 'active' : 'inactive';
            $changes[] = "status: $currentStatus -> $newStatus";
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would modify badge \"$record->name\" (ID: $badgeId) (use --run to execute):</info>");
            foreach ($changes as $change) {
                $output->writeln("  $change");
            }
            return Command::SUCCESS;
        }

        $verbose->step("Modifying badge $badgeId");

        if ($newName !== null) {
            $verbose->info("Setting name: $newName");
            $DB->set_field('badge', 'name', $newName, ['id' => $badgeId]);
        }

        if ($newDescription !== null) {
            $verbose->info('Setting description');
            $DB->set_field('badge', 'description', $newDescription, ['id' => $badgeId]);
        }

        if ($newStatus !== null) {
            $badge = new \badge($badgeId);
            $targetStatus = $newStatus === 'active' ? BADGE_STATUS_ACTIVE : BADGE_STATUS_INACTIVE;
            if ((int) $record->status !== $targetStatus) {
                $verbose->info("Setting status: $newStatus");
                $badge->set_status($targetStatus);
            }
        }

        $DB->set_field('badge', 'timemodified', time(), ['id' => $badgeId]);
        $verbose->done('Badge modified');

        // Output updated state.
        $updated = $DB->get_record('badge', ['id' => $badgeId]);
        $statusLabel = $updated->status == BADGE_STATUS_ACTIVE ? 'active' : 'inactive';
        $typeLabel = $updated->type == BADGE_TYPE_COURSE ? 'course' : 'site';

        $headers = ['id', 'name', 'type', 'status'];
        $rows = [[$updated->id, $updated->name, $typeLabel, $statusLabel]];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
