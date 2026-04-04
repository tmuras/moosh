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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * role:import implementation for Moodle 5.1.
 */
class RoleImport51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('file', InputArgument::OPTIONAL, 'Path to XML file (omit if using --stdin)')
            ->addOption('stdin', null, InputOption::VALUE_NONE, 'Read XML from standard input')
            ->addOption('skip-validate', null, InputOption::VALUE_NONE, 'Skip XSD schema validation');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $filePath = $input->getArgument('file');
        $useStdin = $input->getOption('stdin');
        $skipValidate = $input->getOption('skip-validate');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/filelib.php';
        require_once $CFG->dirroot . '/admin/roles/lib.php';

        // Read XML data.
        if ($useStdin) {
            $verbose->step('Reading XML from stdin');
            $xmlData = stream_get_contents(STDIN);
        } elseif ($filePath) {
            if (!is_readable($filePath)) {
                $output->writeln("<error>Cannot read file '$filePath'.</error>");
                return Command::FAILURE;
            }
            $verbose->step("Reading XML from $filePath");
            $xmlData = file_get_contents($filePath);
        } else {
            $output->writeln('<error>Provide a file path or use --stdin.</error>');
            return Command::FAILURE;
        }

        if (!$xmlData) {
            $output->writeln('<error>Empty or invalid XML data.</error>');
            return Command::FAILURE;
        }

        // Validate schema.
        if (!$skipValidate) {
            $schemaFile = $CFG->dirroot . '/admin/roles/role_schema.xml';
            if (file_exists($schemaFile)) {
                $verbose->step('Validating XML schema');
                $dom = new \DOMDocument('1.0', 'utf-8');
                $dom->loadXML($xmlData, LIBXML_NOBLANKS);
                if (!$dom->schemaValidate($schemaFile)) {
                    $output->writeln('<error>XML schema validation failed. Use --skip-validate to bypass.</error>');
                    return Command::FAILURE;
                }
                $verbose->done('Schema validated');
            }
        }

        // Parse the XML.
        $verbose->step('Parsing role definition');
        $importData = \core_role_preset::parse_preset($xmlData);
        if (!$importData) {
            $output->writeln('<error>Unable to parse XML role data.</error>');
            return Command::FAILURE;
        }

        $roleName = $importData['shortname'] ?? '';
        $archetype = $importData['archetype'] ?? '';
        $existingRole = $roleName ? $DB->get_record('role', ['shortname' => $roleName]) : null;

        $action = $existingRole ? 'update' : 'create';

        if (!$runMode) {
            $output->writeln("<info>Dry run — would $action role '$roleName' (use --run to execute):</info>");
            if ($archetype) {
                $output->writeln("  archetype: $archetype");
            }
            if ($existingRole) {
                $output->writeln("  existing role ID: {$existingRole->id}");
            }
            return Command::SUCCESS;
        }

        // Validate archetype if present.
        if ($archetype) {
            $archetypes = get_role_archetypes();
            if (!isset($archetypes[$archetype])) {
                $output->writeln("<error>Unknown archetype '$archetype' in XML.</error>");
                return Command::FAILURE;
            }
        }

        $verbose->step(ucfirst($action) . "ing role '$roleName'");

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

        if (!$existingRole) {
            // Create the role first so the definition table has a valid ID.
            $verbose->info('Creating empty role shell');
            $newName = $importData['name'] ?? $roleName;
            $newDesc = $importData['description'] ?? '';
            $roleId = create_role($newName, $roleName, $newDesc, $archetype);
        } else {
            $roleId = $existingRole->id;
        }

        $definitionTable = new \core_role_define_role_table_advanced($systemContext, $roleId);

        if ($existingRole) {
            $verbose->info('Preloading existing role data');
            $definitionTable->force_duplicate($existingRole->id, $options);
        } elseif ($archetype) {
            $verbose->info("Applying archetype '$archetype' defaults");
            $definitionTable->force_archetype($archetype, $options);
        } else {
            $definitionTable->force_duplicate($roleId, $options);
        }

        // Load permissions into $_POST so the table can read them.
        $_POST = $importData['permissions'] ?? [];
        $definitionTable->read_submitted_permissions();

        $definitionTable->force_preset($xmlData, $options);
        $definitionTable->save_changes();
        $newRoleId = $definitionTable->get_role_id();

        $output->writeln(ucfirst($action) . "d role '$roleName' (ID: $newRoleId).");

        return Command::SUCCESS;
    }
}
