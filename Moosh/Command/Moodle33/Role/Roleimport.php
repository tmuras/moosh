<?php
/**
 * moosh - Moodle Shell - RoleImport command
 *
 * This command should be used to import role definition from an XML file.
 *
 * @example This example will import XML contents from a specific file
 *          $ php moosh.php role-import -f source_file.xml
 *
 * @example This example will import XML contents by reading STDIN.
 *          $ php moosh.php role-import --stdin < source_file.xml
 *
 * @copyright  2012 onwards Tomasz Muras
 * @author     Andrej Vitez <contact@andrejvitez.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle33\Role;

use context_system;
use core_role_preset;
use DOMDocument;
use Moosh\Command\Moodle33\Role\RoleImport\core_role_define_role_table_advanced_extended;
use Moosh\MooshCommand;

class Roleimport extends MooshCommand {
    private const STATUS_ERROR = 1;

    public function __construct() {
        parent::__construct('import', 'role');
        $this->addOption('f|file:', 'Input file path. If empty then STDIN will be read.');
        $this->addOption('i|stdin:', 'Read data from STDIN');
        $this->addOption('s|skip-validate', 'Skip XSD schema validation of input XML contents.');
    }

    public function execute() {
        global $DB;
        $xmlData = '';

        if (!class_exists('DOMDocument') || !extension_loaded('dom') || !extension_loaded('libxml')) {
            $this->exitError('PHP DOMDocument and libxml support is mandatory. Unable to continue.');
        }

        if ($filepath = $this->expandedOptions['file']) {
            if (!is_readable($filepath) || false === ($xmlData = file_get_contents($filepath))) {
                $this->exitError(sprintf("Could not read input XML file '%s'\n", $filepath));
            }
        } else if ($this->expandedOptions['stdin']) {
            $xmlData = stream_get_contents(STDIN);
        } else {
            $this->exitError('You need to specify input file or use STDIN.');
        }

        if (!$xmlData) {
            $this->exitError('Invalid or empty XML contents. Please check your input.');
        }

        if (!$this->expandedOptions['skip-validate']) {
            $this->validateSchema($xmlData);
            $this->output('XML file validated successfully.', true);
        }

        if (!strlen($xmlData) || !$importData = core_role_preset::parse_preset($xmlData)) {
            $this->exitError('Unable to parse XML data');
        }

        $rolename = $importData['shortname'];
        if ($rolename) {
            $role = $DB->get_record('role', array('shortname' => $rolename));
        }

        $archetype = $importData['archetype'];
        if ($archetype) {
            $archetypes = get_role_archetypes();
            if (!isset($archetypes[$archetype])) {
                $this->exitError(sprintf('XML data defines an unknown archetype \'%s\'.', $archetype));
            }
        }
        
        $options = array(
            'shortname' => 1,
            'name' => 1,
            'description' => 1,
            'permissions' => 1,
            'archetype' => 1,
            'contextlevels' => 1,
            'allowassign' => 1,
            'allowoverride' => 1,
            'allowswitch' => 1,
            'allowview' => 1
        );

        $systemcontext = context_system::instance();
        $definitiontable = new core_role_define_role_table_advanced_extended($systemcontext, isset($role->id) ? $role->id : 0);

        if (!empty($role->id)) {
            $this->output('Original role exists, preloading all original role data.', true);
            $definitiontable->force_duplicate($role->id, $options);
        } else if ($archetype) {
            $this->output('Original role does not exist, but XML defines archetype which is applied to initialize default role values.',
                true);
            $definitiontable->force_archetype($archetype, $options);
        } else {
            $this->output('No original role or archetype is defined. Forcing all initial values.', true);
            $definitiontable->force_duplicate(0, $options);
        }

        $definitiontable->force_preset($xmlData, $options);
        $definitiontable->clear_self_references();
        $definitiontable->mark_changed_permissions($importData);

        if (!$definitiontable->is_submission_valid()) {
            $this->exitError('Unable to continue, submission has unknown errors.');
        }

        $definitiontable->save_changes();
        $tableroleid = $definitiontable->get_role_id();

        if (isset($role->id)) {
            $this->output(sprintf('Updated role \'%s\' (id: %d)', $rolename, $tableroleid), true);
        } else {
            $this->output(sprintf('Inserted new role \'%s\' (id: %d)', $rolename, $tableroleid), true);
        }
    }

    private function exitError($message, $statusCode = self::STATUS_ERROR) {
        $this->output($message);
        exit($statusCode);
    }

    private function output($message, $verbose = false) {
        if ($verbose && !$this->verbose) {
            return;
        }
        print $message . PHP_EOL;
    }

    public function validateSchema($xmlData) {
        global $CFG;

        $dom = new DOMDocument('1.0', 'utf-8');
        $xmlSchema = $CFG->dirroot . '/admin/roles/role_schema.xml';

        if (!file_exists($xmlSchema)) {
            $this->exitError(sprintf('Unable to load XSD schema file \'%s\'', $xmlSchema));
        }

        $dom->loadXML($xmlData, LIBXML_NOBLANKS);
        if (!$dom->schemaValidate($xmlSchema)) {
            $this->output('Failed to validate XML schema!' .
                ($this->verbose ? '' : ' Enable verbosity to show validation errors.'));

            if ($this->verbose) {
                $errors = libxml_get_errors();
                foreach ($errors as $error) {
                    $this->output(sprintf("\tvalidation error %s in '%s' (line: %d): '%s'", $error->code, $error->file,
                        $error->line, trim($error->message)));
                }
                libxml_clear_errors();
            }
        }
    }
}
