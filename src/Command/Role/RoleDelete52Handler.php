<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Role;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * role:delete implementation for Moodle 5.1.
 */
class RoleDelete52Handler extends BaseHandler
{
    use RoleLookupTrait;

    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'role',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Role shortname(s) or ID(s) to delete',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $identifiers = $input->getArgument('role');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/accesslib.php';

        // Resolve all roles first.
        $roles = [];
        foreach ($identifiers as $identifier) {
            $role = $this->findRole($identifier);
            if (!$role) {
                $output->writeln("<error>Role '$identifier' not found.</error>");
                return Command::FAILURE;
            }
            $roles[] = $role;
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following roles would be deleted (use --run to execute):</info>');
            foreach ($roles as $role) {
                $assignCount = $DB->count_records('role_assignments', ['roleid' => $role->id]);
                $output->writeln("  ID={$role->id} shortname={$role->shortname} ({$role->name}) — $assignCount assignment(s)");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Deleting ' . count($roles) . ' role(s)');

        foreach ($roles as $role) {
            $verbose->info("Deleting role {$role->shortname} (ID={$role->id})");
            delete_role($role->id);
            $verbose->done("Deleted role {$role->shortname}");
            $output->writeln("Deleted role {$role->shortname} (ID={$role->id}).");
        }

        return Command::SUCCESS;
    }
}
