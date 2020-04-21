<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle31\User;
use Moosh\MooshCommand;

class UserAssignSystemRole extends MooshCommand
{

    public function __construct()
    {
        parent::__construct('assign-system-role', 'user');

        $this->addArgument('username');
        $this->addArgument('roleshortname');
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once("$CFG->libdir/accesslib.php");

        list($userid, $roleshortname) = $this->arguments;
        $user = $DB->get_record('user', ['username' => $userid]);
        if(!$user)
            cli_error(sprintf("User not found with username %s", $userid));

        $role = $DB->get_record('role', ['shortname' => $roleshortname]);
        if(!$role)
            cli_error(sprintf("Role not found with shortname %s", $roleshortname));

        $this->isSystemRole($role);

        try {
            $return = role_assign($role->id, $user->id, 1);
        } catch (\moodle_exception $e) {
            cli_error($e->getMessage());
        }

        echo "OK!\n";
    }

    function isSystemRole($role) {
        global $DB;

        if(!$DB->count_records("role_context_levels", ["roleid" => $role->id, "contextlevel" => 10])) {
            cli_error("Role is not a system role!");
        }

    }


}