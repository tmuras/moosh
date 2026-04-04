<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Group;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GroupMod52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('groupid', InputArgument::REQUIRED, 'Group ID')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Set group name')
            ->addOption('description', 'd', InputOption::VALUE_REQUIRED, 'Set description')
            ->addOption('idnumber', null, InputOption::VALUE_REQUIRED, 'Set ID number')
            ->addOption('visibility', null, InputOption::VALUE_REQUIRED, 'Set visibility (0-3)')
            ->addOption('enrolmentkey', null, InputOption::VALUE_REQUIRED, 'Set enrolment key')
            ->addOption('add-member', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Add member by username or user ID')
            ->addOption('remove-member', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Remove member by username or user ID')
            ->addOption('empty', null, InputOption::VALUE_NONE, 'Remove all members');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $groupId = (int) $input->getArgument('groupid');
        $newName = $input->getOption('name');
        $newDesc = $input->getOption('description');
        $newIdnumber = $input->getOption('idnumber');
        $newVisibility = $input->getOption('visibility');
        $newKey = $input->getOption('enrolmentkey');
        $addMembers = $input->getOption('add-member');
        $removeMembers = $input->getOption('remove-member');
        $doEmpty = $input->getOption('empty');

        require_once $CFG->dirroot . '/group/lib.php';

        $group = groups_get_group($groupId);
        if (!$group) {
            $output->writeln("<error>Group with ID $groupId not found.</error>");
            return Command::FAILURE;
        }

        $hasChanges = $doEmpty || $newName !== null || $newDesc !== null
            || $newIdnumber !== null || $newVisibility !== null || $newKey !== null
            || !empty($addMembers) || !empty($removeMembers);

        if (!$hasChanges) {
            $output->writeln('<error>No modifications specified.</error>');
            return Command::FAILURE;
        }

        // Resolve users.
        $usersToAdd = $this->resolveUsers($addMembers, $output);
        $usersToRemove = $this->resolveUsers($removeMembers, $output);
        if ($usersToAdd === null || $usersToRemove === null) {
            return Command::FAILURE;
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would modify group '{$group->name}' (ID=$groupId) (use --run to execute):</info>");
            if ($newName !== null) { $output->writeln("  name → \"$newName\""); }
            if ($newDesc !== null) { $output->writeln("  description → \"$newDesc\""); }
            if ($newIdnumber !== null) { $output->writeln("  idnumber → \"$newIdnumber\""); }
            if ($newVisibility !== null) { $output->writeln("  visibility → $newVisibility"); }
            if ($newKey !== null) { $output->writeln("  enrolmentkey → \"$newKey\""); }
            if ($doEmpty) { $output->writeln("  empty all members"); }
            if (!empty($usersToAdd)) { $output->writeln("  add " . count($usersToAdd) . " member(s)"); }
            if (!empty($usersToRemove)) { $output->writeln("  remove " . count($usersToRemove) . " member(s)"); }
            return Command::SUCCESS;
        }

        // Apply property changes.
        $needsUpdate = false;
        if ($newName !== null) { $group->name = $newName; $needsUpdate = true; }
        if ($newDesc !== null) { $group->description = $newDesc; $needsUpdate = true; }
        if ($newIdnumber !== null) { $group->idnumber = $newIdnumber; $needsUpdate = true; }
        if ($newVisibility !== null) { $group->visibility = (int) $newVisibility; $needsUpdate = true; }
        if ($newKey !== null) { $group->enrolmentkey = $newKey; $needsUpdate = true; }

        if ($needsUpdate) {
            groups_update_group($group);
            $verbose->info('Updated group properties');
        }

        // Empty group.
        if ($doEmpty) {
            $members = groups_get_members($groupId, 'u.id');
            foreach ($members as $member) {
                groups_remove_member($groupId, $member->id);
            }
            $output->writeln("Removed " . count($members) . " member(s).");
        }

        // Add members.
        $added = 0;
        foreach ($usersToAdd as $userId) {
            if (!groups_is_member($groupId, $userId)) {
                groups_add_member($groupId, $userId);
                $added++;
            }
        }
        if ($added > 0) { $output->writeln("Added $added member(s)."); }

        // Remove members.
        $removed = 0;
        foreach ($usersToRemove as $userId) {
            if (groups_is_member($groupId, $userId)) {
                groups_remove_member($groupId, $userId);
                $removed++;
            }
        }
        if ($removed > 0) { $output->writeln("Removed $removed member(s)."); }

        // Output.
        $group = groups_get_group($groupId);
        $memberCount = $DB->count_records('groups_members', ['groupid' => $groupId]);

        $headers = ['id', 'name', 'idnumber', 'visibility', 'members'];
        $rows = [[$group->id, $group->name, $group->idnumber ?? '', $group->visibility ?? 0, $memberCount]];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }

    private function resolveUsers(array $identifiers, OutputInterface $output): ?array
    {
        global $DB;
        $userIds = [];
        foreach ($identifiers as $ident) {
            if (ctype_digit($ident)) {
                $user = $DB->get_record('user', ['id' => (int) $ident]);
            } else {
                $user = $DB->get_record('user', ['username' => $ident]);
            }
            if (!$user) {
                $output->writeln("<error>User '$ident' not found.</error>");
                return null;
            }
            $userIds[] = $user->id;
        }
        return $userIds;
    }
}
