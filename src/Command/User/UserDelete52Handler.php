<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\User;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserDelete52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument(
                'userid',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'Username(s) or user ID(s) to delete',
            )
            ->addOption('id', null, InputOption::VALUE_NONE, 'Treat arguments as user IDs instead of usernames');

        $command->addExampleUsage('Delete a single user by username', 'john --run');
        $command->addExampleUsage('Delete multiple users by username', 'john jane --run');
        $command->addExampleUsage('Delete a user by numeric ID', '5 --id --run');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $users = $input->getArgument('userid');
        $byId = $input->getOption('id');

        require_once $CFG->dirroot . '/user/lib.php';

        if (!$byId) {
            // Make sure no username is ambiguous before touching anything (single query).
            $verbose->step('Checking usernames are unique');
            [$insql, $params] = $DB->get_in_or_equal($users, SQL_PARAMS_NAMED);
            $ambiguous = $DB->get_records_sql(
                "SELECT username, COUNT(*) AS cnt FROM {user} WHERE deleted = 0 AND username $insql GROUP BY username HAVING COUNT(*) > 1",
                $params
            );
            if ($ambiguous) {
                $list = [];
                foreach ($ambiguous as $row) {
                    $list[] = "'{$row->username}' ({$row->cnt} records)";
                }
                $output->writeln('<error>Ambiguous username(s): ' . implode(', ', $list)
                    . '. The same username exists for multiple users (e.g. different authentication/mnet hosts). '
                    . 'Nothing was deleted. Use the --id option with numerical user ids instead.</error>');
                return Command::FAILURE;
            }
        }

        // Validate all users first
        $verbose->step('Validating users');
        $userRecords = [];
        foreach ($users as $identifier) {
            if ($byId) {
                $id = (int) $identifier;
                if ($id <= 0) {
                    $output->writeln("<error>Invalid user ID: $identifier</error>");
                    return Command::FAILURE;
                }
                $record = $DB->get_record('user', ['id' => $id, 'deleted' => 0]);
                if (!$record) {
                    $output->writeln("<error>User with ID $id not found (or already deleted).</error>");
                    return Command::FAILURE;
                }
            } else {
                $record = $DB->get_record('user', ['username' => $identifier, 'deleted' => 0]);
                if (!$record) {
                    $output->writeln("<error>User '$identifier' not found (or already deleted).</error>");
                    return Command::FAILURE;
                }
            }

            // Prevent deleting admin and guest
            if ($record->id == 2) {
                $output->writeln("<error>Cannot delete the admin user (ID=2).</error>");
                return Command::FAILURE;
            }
            if (isguestuser($record)) {
                $output->writeln("<error>Cannot delete the guest user.</error>");
                return Command::FAILURE;
            }

            $userRecords[] = $record;
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following users would be deleted (use --run to execute):</info>');
            foreach ($userRecords as $record) {
                $output->writeln("  ID={$record->id}, username=\"{$record->username}\", name=\"{$record->firstname} {$record->lastname}\", email={$record->email}");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Deleting ' . count($userRecords) . ' user(s)');

        foreach ($userRecords as $record) {
            $verbose->info("Deleting user \"{$record->username}\" (ID={$record->id})");
            user_delete_user($record);
            $output->writeln("Deleted user \"{$record->username}\" (ID={$record->id}).");
        }

        return Command::SUCCESS;
    }
}
