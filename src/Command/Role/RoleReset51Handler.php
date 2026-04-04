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
 * role:reset implementation for Moodle 5.1.
 */
class RoleReset51Handler extends BaseHandler
{
    use RoleLookupTrait;

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('role', InputArgument::REQUIRED, 'Role shortname or ID to reset')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to XML definition file');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $identifier = $input->getArgument('role');
        $filePath = $input->getArgument('file');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/accesslib.php';
        require_once $CFG->dirroot . '/admin/roles/lib.php';

        $role = $this->findRole($identifier);
        if (!$role) {
            $output->writeln("<error>Role '$identifier' not found.</error>");
            return Command::FAILURE;
        }

        if (!is_readable($filePath)) {
            $output->writeln("<error>Cannot read file '$filePath'.</error>");
            return Command::FAILURE;
        }

        $xmlData = file_get_contents($filePath);
        if (!$xmlData) {
            $output->writeln('<error>File is empty or unreadable.</error>');
            return Command::FAILURE;
        }

        $importData = \core_role_preset::parse_preset($xmlData);
        if (!$importData) {
            $output->writeln('<error>Unable to parse XML role definition.</error>');
            return Command::FAILURE;
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would reset role '{$role->shortname}' (ID: {$role->id}) from $filePath (use --run to execute).</info>");
            return Command::SUCCESS;
        }

        $verbose->step("Resetting role '{$role->shortname}' (ID: {$role->id})");

        $options = [
            'shortname'     => 1,
            'name'          => 1,
            'description'   => 1,
            'permissions'   => 1,
            'archetype'     => 1,
            'contextlevels' => 1,
            'allowassign'   => 1,
            'allowoverride' => 1,
            'allowswitch'   => 1,
            'allowview'     => 1,
        ];

        $systemContext = \context_system::instance();
        $definitionTable = new \core_role_define_role_table_advanced($systemContext, $role->id);

        // Load permissions from XML into $_POST so read_submitted_permissions works.
        $_POST = $importData['permissions'];
        $definitionTable->read_submitted_permissions();

        $definitionTable->force_preset($xmlData, $options);

        // Clear self-references in allow* arrays to avoid duplicate key errors.
        foreach (['allowassign', 'allowoverride', 'allowswitch', 'allowview'] as $prop) {
            $ref = new \ReflectionProperty($definitionTable, $prop);
            $ref->setAccessible(true);
            $values = $ref->getValue($definitionTable);
            if (is_array($values)) {
                $values = array_filter($values, fn($v) => $v != -1 && $v != $role->id);
                $ref->setValue($definitionTable, $values);
            }
        }

        $definitionTable->save_changes();

        $verbose->done('Role reset');
        $output->writeln("Reset role '{$role->shortname}' (ID: {$role->id}) from $filePath.");

        return Command::SUCCESS;
    }
}
