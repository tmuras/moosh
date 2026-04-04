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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * role:create implementation for Moodle 5.1.
 */
class RoleCreate51Handler extends BaseHandler
{
    private const CONTEXT_MAP = [
        'system'   => 10,
        'user'     => 30,
        'coursecat' => 40,
        'course'   => 50,
        'module'   => 70,
        'block'    => 80,
    ];

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('shortname', InputArgument::REQUIRED, 'Short name for the new role')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Full display name')
            ->addOption('description', null, InputOption::VALUE_REQUIRED, 'Role description')
            ->addOption('archetype', null, InputOption::VALUE_REQUIRED, 'Role archetype (e.g. manager, teacher, student)')
            ->addOption('context', null, InputOption::VALUE_REQUIRED, 'Comma-separated context levels: system,user,coursecat,course,module,block');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $shortname = $input->getArgument('shortname');
        $name = $input->getOption('name') ?? $shortname;
        $description = $input->getOption('description') ?? '';
        $archetype = $input->getOption('archetype');
        $contextOpt = $input->getOption('context');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/accesslib.php';

        // Validate: role must not already exist.
        if ($DB->get_record('role', ['shortname' => $shortname])) {
            $output->writeln("<error>Role '$shortname' already exists.</error>");
            return Command::FAILURE;
        }

        // Validate: archetype and context are mutually exclusive.
        if ($archetype !== null && $contextOpt !== null) {
            $output->writeln('<error>Options --archetype and --context are mutually exclusive.</error>');
            return Command::FAILURE;
        }

        // Parse context levels.
        $contextLevels = [];
        if ($contextOpt !== null) {
            foreach (explode(',', $contextOpt) as $levelName) {
                $levelName = strtolower(trim($levelName));
                if (!isset(self::CONTEXT_MAP[$levelName])) {
                    $valid = implode(', ', array_keys(self::CONTEXT_MAP));
                    $output->writeln("<error>Unknown context level '$levelName'. Valid: $valid</error>");
                    return Command::FAILURE;
                }
                $contextLevels[] = self::CONTEXT_MAP[$levelName];
            }
        }

        // Validate archetype.
        if ($archetype !== null) {
            $archetypes = get_role_archetypes();
            if (!isset($archetypes[$archetype])) {
                $valid = implode(', ', array_keys($archetypes));
                $output->writeln("<error>Unknown archetype '$archetype'. Valid: $valid</error>");
                return Command::FAILURE;
            }
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — would create role (use --run to execute):</info>');
            $output->writeln("  shortname: $shortname");
            $output->writeln("  name: $name");
            if ($description) {
                $output->writeln("  description: $description");
            }
            if ($archetype) {
                $output->writeln("  archetype: $archetype");
            }
            if ($contextLevels) {
                $output->writeln('  context levels: ' . $contextOpt);
            }
            return Command::SUCCESS;
        }

        $verbose->step("Creating role '$shortname'");
        $roleId = create_role($name, $shortname, $description, $archetype ?? '');
        $verbose->done("Role created with ID $roleId");

        if ($contextLevels) {
            $verbose->step('Setting context levels');
            set_role_contextlevels($roleId, $contextLevels);
        } elseif (!$archetype) {
            // Default to system context if no archetype and no explicit context.
            set_role_contextlevels($roleId, [CONTEXT_SYSTEM]);
        }

        $role = $DB->get_record('role', ['id' => $roleId]);

        $headers = ['id', 'shortname', 'name', 'archetype'];
        $rows = [[$role->id, $role->shortname, $role->name, $role->archetype]];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
