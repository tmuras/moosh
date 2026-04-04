<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Cohort;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CohortMod51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('cohortid', InputArgument::REQUIRED, 'Cohort ID')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Set cohort name')
            ->addOption('description', 'd', InputOption::VALUE_REQUIRED, 'Set description')
            ->addOption('idnumber', null, InputOption::VALUE_REQUIRED, 'Set ID number')
            ->addOption('visible', null, InputOption::VALUE_REQUIRED, 'Set visible (1 or 0)')
            ->addOption('add-member', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Add member by username or user ID')
            ->addOption('remove-member', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Remove member by username or user ID')
            ->addOption('import', null, InputOption::VALUE_REQUIRED, 'Import members from CSV file (username or email column)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $cohortId = (int) $input->getArgument('cohortid');
        $newName = $input->getOption('name');
        $newDesc = $input->getOption('description');
        $newIdnumber = $input->getOption('idnumber');
        $newVisible = $input->getOption('visible');
        $addMembers = $input->getOption('add-member');
        $removeMembers = $input->getOption('remove-member');
        $importFile = $input->getOption('import');

        require_once $CFG->dirroot . '/cohort/lib.php';

        $cohort = $DB->get_record('cohort', ['id' => $cohortId]);
        if (!$cohort) {
            $output->writeln("<error>Cohort with ID $cohortId not found.</error>");
            return Command::FAILURE;
        }

        $hasChanges = $newName !== null || $newDesc !== null || $newIdnumber !== null
            || $newVisible !== null || !empty($addMembers) || !empty($removeMembers) || $importFile !== null;

        if (!$hasChanges) {
            $output->writeln('<error>No modifications specified. Use --name, --description, --idnumber, --visible, --add-member, --remove-member, or --import.</error>');
            return Command::FAILURE;
        }

        // Resolve users for add/remove.
        $usersToAdd = $this->resolveUsers($addMembers, $output);
        $usersToRemove = $this->resolveUsers($removeMembers, $output);
        if ($usersToAdd === null || $usersToRemove === null) {
            return Command::FAILURE;
        }

        // Resolve import file.
        $importUsers = [];
        if ($importFile !== null) {
            $importUsers = $this->resolveImportFile($importFile, $output);
            if ($importUsers === null) {
                return Command::FAILURE;
            }
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would modify cohort '{$cohort->name}' (ID=$cohortId) (use --run to execute):</info>");
            if ($newName !== null) { $output->writeln("  name → \"$newName\""); }
            if ($newDesc !== null) { $output->writeln("  description → \"$newDesc\""); }
            if ($newIdnumber !== null) { $output->writeln("  idnumber → \"$newIdnumber\""); }
            if ($newVisible !== null) { $output->writeln("  visible → $newVisible"); }
            if (!empty($usersToAdd)) { $output->writeln("  add " . count($usersToAdd) . " member(s)"); }
            if (!empty($usersToRemove)) { $output->writeln("  remove " . count($usersToRemove) . " member(s)"); }
            if (!empty($importUsers)) { $output->writeln("  import " . count($importUsers) . " member(s) from CSV"); }
            return Command::SUCCESS;
        }

        $verbose->step("Modifying cohort '{$cohort->name}'");

        // Apply property changes.
        if ($newName !== null) { $cohort->name = $newName; }
        if ($newDesc !== null) { $cohort->description = $newDesc; }
        if ($newIdnumber !== null) { $cohort->idnumber = $newIdnumber; }
        if ($newVisible !== null) { $cohort->visible = (int) $newVisible; }

        if ($newName !== null || $newDesc !== null || $newIdnumber !== null || $newVisible !== null) {
            cohort_update_cohort($cohort);
            $verbose->info('Updated cohort properties');
        }

        // Add members.
        $added = 0;
        foreach (array_merge($usersToAdd, $importUsers) as $userId) {
            if (!cohort_is_member($cohortId, $userId)) {
                cohort_add_member($cohortId, $userId);
                $added++;
            }
        }
        if ($added > 0) {
            $output->writeln("Added $added member(s).");
        }

        // Remove members.
        $removed = 0;
        foreach ($usersToRemove as $userId) {
            if (cohort_is_member($cohortId, $userId)) {
                cohort_remove_member($cohortId, $userId);
                $removed++;
            }
        }
        if ($removed > 0) {
            $output->writeln("Removed $removed member(s).");
        }

        // Output updated cohort.
        $cohort = $DB->get_record('cohort', ['id' => $cohortId]);
        $memberCount = $DB->count_records('cohort_members', ['cohortid' => $cohortId]);

        $headers = ['id', 'name', 'idnumber', 'visible', 'members'];
        $rows = [[$cohort->id, $cohort->name, $cohort->idnumber ?? '', $cohort->visible, $memberCount]];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }

    /**
     * @return int[]|null  Null on error.
     */
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

    /**
     * @return int[]|null  Null on error.
     */
    private function resolveImportFile(string $path, OutputInterface $output): ?array
    {
        global $DB;

        if (!file_exists($path)) {
            $output->writeln("<error>File not found: $path</error>");
            return null;
        }

        $fh = fopen($path, 'r');
        if (!$fh) {
            $output->writeln("<error>Cannot open file: $path</error>");
            return null;
        }

        $headers = fgetcsv($fh);
        if (!$headers) {
            fclose($fh);
            $output->writeln('<error>Empty CSV or invalid format.</error>');
            return null;
        }

        // Find username or email column.
        $usernameCol = array_search('username', $headers);
        $emailCol = array_search('email', $headers);

        if ($usernameCol === false && $emailCol === false) {
            fclose($fh);
            $output->writeln('<error>CSV must have a "username" or "email" column.</error>');
            return null;
        }

        $userIds = [];
        $lineNum = 1;
        while (($row = fgetcsv($fh)) !== false) {
            $lineNum++;
            if ($usernameCol !== false && isset($row[$usernameCol])) {
                $user = $DB->get_record('user', ['username' => $row[$usernameCol]]);
            } elseif ($emailCol !== false && isset($row[$emailCol])) {
                $user = $DB->get_record('user', ['email' => $row[$emailCol]]);
            } else {
                continue;
            }
            if ($user) {
                $userIds[] = $user->id;
            } else {
                $output->writeln("<comment>Warning: User on line $lineNum not found, skipping.</comment>");
            }
        }
        fclose($fh);

        return $userIds;
    }
}
