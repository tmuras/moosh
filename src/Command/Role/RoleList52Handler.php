<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Role;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * role:list implementation for Moodle 5.1.
 */
class RoleList52Handler extends BaseHandler
{
    use RoleLookupTrait;

    private const LEVEL_NAMES = [
        10 => 'system',
        30 => 'user',
        40 => 'coursecat',
        50 => 'course',
        70 => 'module',
        80 => 'block',
    ];

    public function configureCommand(Command $command): void
    {
        $command->addOption(
            'id-only',
            'i',
            InputOption::VALUE_NONE,
            'Output only role IDs, space-separated',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $idOnly = $input->getOption('id-only');

        $verbose->step('Fetching roles');
        $roles = $DB->get_records('role', null, 'sortorder ASC');

        if (!$roles) {
            $output->writeln('No roles found.');
            return Command::SUCCESS;
        }

        if ($idOnly) {
            $ids = array_map(fn($r) => $r->id, $roles);
            $output->writeln(implode(' ', $ids));
            return Command::SUCCESS;
        }

        // Fetch context levels for all roles.
        $verbose->step('Fetching context levels');
        $contextLevels = $DB->get_records('role_context_levels', null, '', 'id, roleid, contextlevel');
        $roleLevels = [];
        foreach ($contextLevels as $cl) {
            $name = self::LEVEL_NAMES[$cl->contextlevel] ?? (string) $cl->contextlevel;
            $roleLevels[$cl->roleid][] = $name;
        }

        // Count assignments per role.
        $verbose->step('Counting role assignments');
        $assignCounts = $DB->get_records_sql(
            "SELECT roleid, COUNT(*) AS c FROM {role_assignments} GROUP BY roleid",
        );

        $headers = ['id', 'shortname', 'name', 'archetype', 'context_levels', 'assignments'];
        $rows = [];
        foreach ($roles as $role) {
            $levels = isset($roleLevels[$role->id]) ? implode(',', $roleLevels[$role->id]) : '';
            $count = isset($assignCounts[$role->id]) ? (int) $assignCounts[$role->id]->c : 0;
            $rows[] = [$role->id, $role->shortname, $role->name, $role->archetype, $levels, $count];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
