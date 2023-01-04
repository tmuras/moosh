<?php
/**
 * moosh - Moodle Shell - RoleExport command
 *
 * This command should be used to export role definition to an XML file.
 *
 * @example This example will write XML contents to a specific file
 *          $ php moosh.php role-export -f target_file.xml ROLENAME
 *
 * @example This example will output XML contents to stdout
 *          $ php moosh.php role-export ROLENAME
 *
 * @example This example will output pretty printed XML contents to stdout
 *          $ php moosh.php role-export --pretty ROLENAME
 *
 * @copyright  2012 onwards Tomasz Muras
 * @author     Andrej Vitez <contact@andrejvitez.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle33\Role;

use core_role_preset;
use DOMDocument;
use Moosh\MooshCommand;

class RoleExport extends MooshCommand {
    public function __construct() {
        parent::__construct('export', 'role');

        $this->addOption('f|file:', 'Output file path. If empty then output will be printed to stdout.');
        $this->addOption('r|pretty', 'Output formatted XML with whitespaces.');
        $this->addArgument('shortname');
    }

    public function execute() {
        global $CFG, $DB;

        $rolename = $this->arguments[0];
        if (!$rolename) {
            printf("Short rolename argument is mandatory");
            exit(1);
        }
        $role = $DB->get_record('role', array('shortname' => $rolename));
        if (!$role) {
            echo "Role '" . $rolename . "' does not exists!\n";
            exit(1);
        }

        $filepath = $this->expandedOptions['file'];
        if (!$filepath) {
            printf("Invalid output path value '%s'\n", $filepath);
            exit(1);
        }

        require_once($CFG->libdir . '/filelib.php');
        $xml = core_role_preset::get_export_xml($role->id);

        if ($this->expandedOptions['pretty']) {
            $dom = new DOMDocument('1.0');
            $dom->preserveWhiteSpace = true;
            $dom->formatOutput = true;
            $dom->loadXML($xml);
            $xml = $dom->saveXML();
        }

        if ($filepath) {
            if (false === file_put_contents($filepath, $xml)) {
                printf("Could not save XML contents to file '%s'\n", $filepath);
                exit(1);
            }

            exit(0);
        }

        echo $xml . PHP_EOL;
    }
}
