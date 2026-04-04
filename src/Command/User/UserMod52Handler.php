<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\User;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * user:mod implementation for Moodle 5.1.
 *
 * Merges moosh1's user-mod, user-assign-system-role, and user-unassign-system-role.
 */
class UserMod52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('userid', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Username(s) or user ID(s) to modify')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'Set email address')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Set password')
            ->addOption('auth', null, InputOption::VALUE_REQUIRED, 'Set authentication method')
            ->addOption('username', null, InputOption::VALUE_REQUIRED, 'Set username (single user only)')
            ->addOption('firstname', null, InputOption::VALUE_REQUIRED, 'Set first name')
            ->addOption('lastname', null, InputOption::VALUE_REQUIRED, 'Set last name')
            ->addOption('city', null, InputOption::VALUE_REQUIRED, 'Set city')
            ->addOption('country', null, InputOption::VALUE_REQUIRED, 'Set country code')
            ->addOption('suspended', null, InputOption::VALUE_REQUIRED, 'Set suspended status: 0 or 1')
            ->addOption('ignore-policy', null, InputOption::VALUE_NONE, 'Ignore password policy when setting password')
            ->addOption('global-admin', null, InputOption::VALUE_NONE, 'Make user a site administrator')
            ->addOption('remove-global-admin', null, InputOption::VALUE_NONE, 'Remove user from site administrators')
            ->addOption('assign-role', null, InputOption::VALUE_REQUIRED, 'Assign a system role by shortname')
            ->addOption('unassign-role', null, InputOption::VALUE_REQUIRED, 'Unassign a system role by shortname');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');
        $identifiers = $input->getArgument('userid');

        $newEmail = $input->getOption('email');
        $newPassword = $input->getOption('password');
        $newAuth = $input->getOption('auth');
        $newUsername = $input->getOption('username');
        $newFirstname = $input->getOption('firstname');
        $newLastname = $input->getOption('lastname');
        $newCity = $input->getOption('city');
        $newCountry = $input->getOption('country');
        $newSuspended = $input->getOption('suspended');
        $ignorePolicy = $input->getOption('ignore-policy');
        $globalAdmin = $input->getOption('global-admin');
        $removeGlobalAdmin = $input->getOption('remove-global-admin');
        $assignRole = $input->getOption('assign-role');
        $unassignRole = $input->getOption('unassign-role');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->dirroot . '/user/lib.php';
        require_once $CFG->libdir . '/accesslib.php';

        // Check at least one modification is requested.
        $hasChange = $newEmail !== null || $newPassword !== null || $newAuth !== null
            || $newUsername !== null || $newFirstname !== null || $newLastname !== null
            || $newCity !== null || $newCountry !== null || $newSuspended !== null
            || $globalAdmin || $removeGlobalAdmin || $assignRole !== null || $unassignRole !== null;

        if (!$hasChange) {
            $output->writeln('<error>No modifications specified. Use --email, --password, --auth, --username, --firstname, --lastname, --city, --country, --suspended, --global-admin, --remove-global-admin, --assign-role, or --unassign-role.</error>');
            return Command::FAILURE;
        }

        // Validate: --username only makes sense for a single user.
        if ($newUsername !== null && count($identifiers) > 1) {
            $output->writeln('<error>Option --username can only be used with a single user.</error>');
            return Command::FAILURE;
        }

        // Validate suspended value.
        if ($newSuspended !== null && !in_array($newSuspended, ['0', '1'], true)) {
            $output->writeln("<error>Invalid --suspended value '$newSuspended'. Must be 0 or 1.</error>");
            return Command::FAILURE;
        }

        // Validate password policy.
        if ($newPassword !== null && !$ignorePolicy) {
            $error = '';
            if (!check_password_policy($newPassword, $error)) {
                $output->writeln('<error>' . strip_tags($error) . ' Use --ignore-policy to bypass.</error>');
                return Command::FAILURE;
            }
        }

        // Resolve role if assigning/unassigning.
        $assignRoleObj = null;
        $unassignRoleObj = null;
        if ($assignRole !== null) {
            $assignRoleObj = $this->resolveSystemRole($assignRole, $DB, $output);
            if ($assignRoleObj === null) {
                return Command::FAILURE;
            }
        }
        if ($unassignRole !== null) {
            $unassignRoleObj = $this->resolveSystemRole($unassignRole, $DB, $output);
            if ($unassignRoleObj === null) {
                return Command::FAILURE;
            }
        }

        // Resolve all users.
        $users = [];
        foreach ($identifiers as $identifier) {
            $user = $this->findUser($identifier, $DB);
            if (!$user) {
                $output->writeln("<error>User '$identifier' not found.</error>");
                return Command::FAILURE;
            }
            $users[] = $user;
        }

        // Build change summary.
        $changes = [];
        if ($newEmail !== null) {
            $changes[] = "email: $newEmail";
        }
        if ($newPassword !== null) {
            $changes[] = 'password: (updated)';
        }
        if ($newAuth !== null) {
            $changes[] = "auth: $newAuth";
        }
        if ($newUsername !== null) {
            $changes[] = "username: $newUsername";
        }
        if ($newFirstname !== null) {
            $changes[] = "firstname: $newFirstname";
        }
        if ($newLastname !== null) {
            $changes[] = "lastname: $newLastname";
        }
        if ($newCity !== null) {
            $changes[] = "city: $newCity";
        }
        if ($newCountry !== null) {
            $changes[] = "country: $newCountry";
        }
        if ($newSuspended !== null) {
            $changes[] = 'suspended: ' . ($newSuspended === '1' ? 'yes' : 'no');
        }
        if ($globalAdmin) {
            $changes[] = 'global admin: add';
        }
        if ($removeGlobalAdmin) {
            $changes[] = 'global admin: remove';
        }
        if ($assignRoleObj) {
            $changes[] = "assign system role: {$assignRoleObj->shortname}";
        }
        if ($unassignRoleObj) {
            $changes[] = "unassign system role: {$unassignRoleObj->shortname}";
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — would modify ' . count($users) . ' user(s) (use --run to execute):</info>');
            foreach ($users as $user) {
                $output->writeln("  {$user->username} (ID: {$user->id})");
            }
            foreach ($changes as $change) {
                $output->writeln("  $change");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Modifying ' . count($users) . ' user(s)');

        if ($ignorePolicy && $newPassword !== null) {
            unset($CFG->passwordpolicy);
        }

        foreach ($users as $user) {
            $verbose->info("Modifying user {$user->username} (ID: {$user->id})");

            // Property changes.
            $updated = false;
            if ($newEmail !== null) {
                $user->email = $newEmail;
                $updated = true;
            }
            if ($newPassword !== null) {
                $user->password = hash_internal_user_password($newPassword);
                $updated = true;
            }
            if ($newAuth !== null) {
                $user->auth = $newAuth;
                $updated = true;
            }
            if ($newUsername !== null) {
                $user->username = $newUsername;
                $updated = true;
            }
            if ($newFirstname !== null) {
                $user->firstname = $newFirstname;
                $updated = true;
            }
            if ($newLastname !== null) {
                $user->lastname = $newLastname;
                $updated = true;
            }
            if ($newCity !== null) {
                $user->city = $newCity;
                $updated = true;
            }
            if ($newCountry !== null) {
                $user->country = $newCountry;
                $updated = true;
            }
            if ($newSuspended !== null) {
                $user->suspended = (int) $newSuspended;
                $updated = true;
            }

            if ($updated) {
                $DB->update_record('user', $user);
                $verbose->done("Updated user record for {$user->username}");
            }

            // Global admin changes.
            if ($globalAdmin) {
                $this->setGlobalAdmin($user->id, true, $verbose);
            }
            if ($removeGlobalAdmin) {
                $this->setGlobalAdmin($user->id, false, $verbose);
            }

            // System role changes.
            $systemContextId = \context_system::instance()->id;
            if ($assignRoleObj) {
                role_assign($assignRoleObj->id, $user->id, $systemContextId);
                $verbose->done("Assigned system role {$assignRoleObj->shortname} to {$user->username}");
            }
            if ($unassignRoleObj) {
                role_unassign($unassignRoleObj->id, $user->id, $systemContextId);
                $verbose->done("Unassigned system role {$unassignRoleObj->shortname} from {$user->username}");
            }
        }

        // Output result.
        $headers = ['id', 'username', 'email', 'auth', 'suspended'];
        $rows = [];
        foreach ($users as $user) {
            $fresh = $DB->get_record('user', ['id' => $user->id]);
            $rows[] = [$fresh->id, $fresh->username, $fresh->email, $fresh->auth, $fresh->suspended];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }

    /**
     * Find a user by username or numeric ID.
     */
    private function findUser(string $identifier, object $DB): ?object
    {
        if (ctype_digit($identifier)) {
            $user = $DB->get_record('user', ['id' => (int) $identifier]);
            if ($user) {
                return $user;
            }
        }

        $user = $DB->get_record('user', ['username' => $identifier]);
        return $user ?: null;
    }

    /**
     * Resolve a system role by shortname, verifying it's assignable at system level.
     */
    private function resolveSystemRole(string $shortname, object $DB, OutputInterface $output): ?object
    {
        $role = $DB->get_record('role', ['shortname' => $shortname]);
        if (!$role) {
            $output->writeln("<error>Role '$shortname' not found.</error>");
            return null;
        }

        $isSystem = $DB->count_records('role_context_levels', ['roleid' => $role->id, 'contextlevel' => CONTEXT_SYSTEM]);
        if (!$isSystem) {
            $output->writeln("<error>Role '$shortname' is not a system role.</error>");
            return null;
        }

        return $role;
    }

    /**
     * Add or remove a user from the site administrators list.
     */
    private function setGlobalAdmin(int $userId, bool $add, VerboseLogger $verbose): void
    {
        global $CFG;

        $admins = [];
        foreach (explode(',', $CFG->siteadmins) as $admin) {
            $admin = (int) $admin;
            if ($admin) {
                $admins[$admin] = $admin;
            }
        }

        if ($add) {
            $admins[$userId] = $userId;
            $verbose->done("Added user $userId to site administrators");
        } else {
            unset($admins[$userId]);
            $verbose->done("Removed user $userId from site administrators");
        }

        set_config('siteadmins', implode(',', $admins));
    }
}
