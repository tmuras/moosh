<?php
/**
 * moosh - Moodle Shell
 * Reset a role with a XML definition file.
 *
 * @author     David Balch.
 * @copyright  2014 TALL, University of Oxford {@link http://www.tall.ox.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle27\Role;
use Moosh\MooshCommand;

class RoleReset extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('reset', 'role');

        $this->addArgument('roleid');
        $this->addArgument('definitionfile');
    }

    public function execute()
    {
        global $CFG, $DB;
        $roleid = $this->arguments[0];
        $filearg = $this->arguments[1];

        if (substr($filearg, 0, 1) == '/') {
            // Absolute file.
            $filename = $filearg;
        } else {
            // Relative to current directory.
            $filename = $this->cwd . DIRECTORY_SEPARATOR . $filearg;
        }
        $fh = fopen($filename, 'r');
        $roledefinition = fread($fh, filesize($filename));

        if ($roledefinition) {
            $systemcontext = \context_system::instance();
            $options = array(
                'shortname'     => 1,
                'name'          => 1,
                'description'   => 1,
                'permissions'   => 1,
                'archetype'     => 1,
                'contextlevels' => 1,
                'allowassign'   => 1,
                'allowoverride' => 1,
                'allowswitch'   => 1,
                'permissions'   => 1);
            $definitiontable = new \core_role_define_role_table_advanced($systemcontext, $roleid);

            // Add all permissions from definition file to $_POST, otherwise, they won't be applied.
            $info = \core_role_preset::parse_preset($roledefinition);
            $_POST = $info['permissions'];
            $definitiontable->read_submitted_permissions();

            $definitiontable->force_preset($roledefinition, $options);
            $definitiontable->save_changes();
        }

    }
}
