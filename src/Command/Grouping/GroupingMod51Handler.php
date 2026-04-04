<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Grouping;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GroupingMod51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('groupingid', InputArgument::REQUIRED, 'Grouping ID')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Set grouping name')
            ->addOption('description', 'd', InputOption::VALUE_REQUIRED, 'Set description')
            ->addOption('idnumber', null, InputOption::VALUE_REQUIRED, 'Set ID number')
            ->addOption('add-group', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Assign group ID to grouping')
            ->addOption('remove-group', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Unassign group ID from grouping');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $groupingId = (int) $input->getArgument('groupingid');
        $newName = $input->getOption('name');
        $newDesc = $input->getOption('description');
        $newIdnumber = $input->getOption('idnumber');
        $addGroups = $input->getOption('add-group');
        $removeGroups = $input->getOption('remove-group');

        require_once $CFG->dirroot . '/group/lib.php';

        $grouping = groups_get_grouping($groupingId);
        if (!$grouping) {
            $output->writeln("<error>Grouping with ID $groupingId not found.</error>");
            return Command::FAILURE;
        }

        $hasChanges = $newName !== null || $newDesc !== null || $newIdnumber !== null
            || !empty($addGroups) || !empty($removeGroups);

        if (!$hasChanges) {
            $output->writeln('<error>No modifications specified.</error>');
            return Command::FAILURE;
        }

        // Validate group IDs.
        foreach (array_merge($addGroups, $removeGroups) as $gid) {
            if (!$DB->record_exists('groups', ['id' => (int) $gid])) {
                $output->writeln("<error>Group with ID $gid not found.</error>");
                return Command::FAILURE;
            }
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would modify grouping '{$grouping->name}' (ID=$groupingId) (use --run to execute):</info>");
            if ($newName !== null) { $output->writeln("  name → \"$newName\""); }
            if ($newDesc !== null) { $output->writeln("  description → \"$newDesc\""); }
            if ($newIdnumber !== null) { $output->writeln("  idnumber → \"$newIdnumber\""); }
            if (!empty($addGroups)) { $output->writeln("  assign " . count($addGroups) . " group(s)"); }
            if (!empty($removeGroups)) { $output->writeln("  unassign " . count($removeGroups) . " group(s)"); }
            return Command::SUCCESS;
        }

        // Apply property changes.
        $needsUpdate = false;
        if ($newName !== null) { $grouping->name = $newName; $needsUpdate = true; }
        if ($newDesc !== null) { $grouping->description = $newDesc; $needsUpdate = true; }
        if ($newIdnumber !== null) { $grouping->idnumber = $newIdnumber; $needsUpdate = true; }

        if ($needsUpdate) {
            groups_update_grouping($grouping);
            $verbose->info('Updated grouping properties');
        }

        // Assign groups.
        foreach ($addGroups as $gid) {
            groups_assign_grouping($groupingId, (int) $gid);
            $verbose->info("Assigned group $gid");
        }
        if (!empty($addGroups)) {
            $output->writeln("Assigned " . count($addGroups) . " group(s).");
        }

        // Unassign groups.
        foreach ($removeGroups as $gid) {
            groups_unassign_grouping($groupingId, (int) $gid);
            $verbose->info("Unassigned group $gid");
        }
        if (!empty($removeGroups)) {
            $output->writeln("Unassigned " . count($removeGroups) . " group(s).");
        }

        // Output.
        $grouping = groups_get_grouping($groupingId);
        $groupCount = $DB->count_records('groupings_groups', ['groupingid' => $groupingId]);

        $headers = ['id', 'name', 'idnumber', 'groups'];
        $rows = [[$grouping->id, $grouping->name, $grouping->idnumber ?? '', $groupCount]];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
