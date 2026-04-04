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
 * role:mod implementation for Moodle 5.1.
 *
 * Merges moosh1's role-update-capability and role-update-contextlevel,
 * plus basic property editing (name, description).
 */
class RoleMod51Handler extends BaseHandler
{
    use RoleLookupTrait;

    private const CONTEXT_MAP = [
        'system'   => 10,
        'user'     => 30,
        'coursecat' => 40,
        'course'   => 50,
        'module'   => 70,
        'block'    => 80,
    ];

    private const PERMISSION_MAP = [
        'inherit'  => 0,    // CAP_INHERIT
        'allow'    => 1,    // CAP_ALLOW
        'prevent'  => -1,   // CAP_PREVENT
        'prohibit' => -1000, // CAP_PROHIBIT
    ];

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
        $command
            ->addArgument('role', InputArgument::REQUIRED, 'Role shortname or ID')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Set role display name')
            ->addOption('description', null, InputOption::VALUE_REQUIRED, 'Set role description')
            ->addOption(
                'capability',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Set capability permission: "capability_name=permission" (repeatable). Permission: inherit, allow, prevent, prohibit',
            )
            ->addOption(
                'context-on',
                null,
                InputOption::VALUE_REQUIRED,
                'Enable context levels (comma-separated): system, user, coursecat, course, module, block',
            )
            ->addOption(
                'context-off',
                null,
                InputOption::VALUE_REQUIRED,
                'Disable context levels (comma-separated): system, user, coursecat, course, module, block',
            );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');
        $identifier = $input->getArgument('role');

        $newName = $input->getOption('name');
        $newDescription = $input->getOption('description');
        $capChanges = $input->getOption('capability');
        $contextOn = $input->getOption('context-on');
        $contextOff = $input->getOption('context-off');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/accesslib.php';

        $role = $this->findRole($identifier);
        if (!$role) {
            $output->writeln("<error>Role '$identifier' not found.</error>");
            return Command::FAILURE;
        }

        // Check that at least one modification is specified.
        if ($newName === null && $newDescription === null && empty($capChanges) && $contextOn === null && $contextOff === null) {
            $output->writeln('<error>No modifications specified. Use --name, --description, --capability, --context-on, or --context-off.</error>');
            return Command::FAILURE;
        }

        // Parse capability changes.
        $parsedCaps = [];
        foreach ($capChanges as $spec) {
            $eqPos = strrpos($spec, '=');
            if ($eqPos === false) {
                $output->writeln("<error>Invalid capability format: '$spec'. Expected: capability_name=permission</error>");
                return Command::FAILURE;
            }
            $capName = substr($spec, 0, $eqPos);
            $permName = strtolower(substr($spec, $eqPos + 1));
            if (!isset(self::PERMISSION_MAP[$permName])) {
                $valid = implode(', ', array_keys(self::PERMISSION_MAP));
                $output->writeln("<error>Unknown permission '$permName'. Valid: $valid</error>");
                return Command::FAILURE;
            }
            $parsedCaps[] = ['capability' => $capName, 'permission' => self::PERMISSION_MAP[$permName], 'label' => $permName];
        }

        // Parse context level changes.
        $levelsOn = $this->parseContextLevels($contextOn, $output);
        $levelsOff = $this->parseContextLevels($contextOff, $output);
        if ($levelsOn === false || $levelsOff === false) {
            return Command::FAILURE;
        }

        // Build change summary.
        $changes = [];
        if ($newName !== null) {
            $changes[] = "name: \"{$role->name}\" -> \"$newName\"";
        }
        if ($newDescription !== null) {
            $changes[] = 'description: (updated)';
        }
        foreach ($parsedCaps as $cap) {
            $changes[] = "capability: {$cap['capability']} = {$cap['label']}";
        }
        if ($levelsOn) {
            $names = array_map(fn($l) => self::LEVEL_NAMES[$l] ?? $l, $levelsOn);
            $changes[] = 'context levels ON: ' . implode(', ', $names);
        }
        if ($levelsOff) {
            $names = array_map(fn($l) => self::LEVEL_NAMES[$l] ?? $l, $levelsOff);
            $changes[] = 'context levels OFF: ' . implode(', ', $names);
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would modify role '{$role->shortname}' (ID: {$role->id}) (use --run to execute):</info>");
            foreach ($changes as $change) {
                $output->writeln("  $change");
            }
            return Command::SUCCESS;
        }

        $verbose->step("Modifying role '{$role->shortname}' (ID: {$role->id})");

        // Apply property changes.
        if ($newName !== null) {
            $verbose->info("Setting name: $newName");
            $DB->set_field('role', 'name', $newName, ['id' => $role->id]);
        }
        if ($newDescription !== null) {
            $verbose->info('Setting description');
            $DB->set_field('role', 'description', $newDescription, ['id' => $role->id]);
        }

        // Apply capability changes.
        foreach ($parsedCaps as $cap) {
            $verbose->info("Setting {$cap['capability']} = {$cap['label']}");
            assign_capability($cap['capability'], $cap['permission'], $role->id, \context_system::instance()->id, true);
        }

        // Apply context level changes.
        foreach ($levelsOn as $level) {
            if (!$DB->get_record('role_context_levels', ['roleid' => $role->id, 'contextlevel' => $level])) {
                $record = new \stdClass();
                $record->roleid = $role->id;
                $record->contextlevel = $level;
                $DB->insert_record('role_context_levels', $record);
                $verbose->info('Enabled context level: ' . (self::LEVEL_NAMES[$level] ?? $level));
            }
        }
        foreach ($levelsOff as $level) {
            $DB->delete_records('role_context_levels', ['roleid' => $role->id, 'contextlevel' => $level]);
            $verbose->info('Disabled context level: ' . (self::LEVEL_NAMES[$level] ?? $level));
        }

        $verbose->done('Role modified');

        // Output updated state.
        $updated = $DB->get_record('role', ['id' => $role->id]);
        $currentLevels = $DB->get_records('role_context_levels', ['roleid' => $role->id]);
        $levelNames = [];
        foreach ($currentLevels as $cl) {
            $levelNames[] = self::LEVEL_NAMES[$cl->contextlevel] ?? (string) $cl->contextlevel;
        }

        $headers = ['id', 'shortname', 'name', 'archetype', 'context_levels'];
        $rows = [[$updated->id, $updated->shortname, $updated->name, $updated->archetype, implode(',', $levelNames)]];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }

    /**
     * Parse a comma-separated list of context level names into numeric values.
     *
     * @return int[]|false Array of level constants, or false on error.
     */
    private function parseContextLevels(?string $input, OutputInterface $output): array|false
    {
        if ($input === null) {
            return [];
        }

        $levels = [];
        foreach (explode(',', $input) as $name) {
            $name = strtolower(trim($name));
            if ($name === '') {
                continue;
            }
            if (!isset(self::CONTEXT_MAP[$name])) {
                $valid = implode(', ', array_keys(self::CONTEXT_MAP));
                $output->writeln("<error>Unknown context level '$name'. Valid: $valid</error>");
                return false;
            }
            $levels[] = self::CONTEXT_MAP[$name];
        }

        return $levels;
    }
}
