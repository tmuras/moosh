<?php


namespace Moosh\Command\Moodle31\User;
use Moosh\MooshCommand;

class UserUnassignSystemRole extends MooshCommand
{

    public function __construct()
    {
        parent::__construct('unassign-system-role', 'user');

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
            cli_error(sprintf("User not found with id %s", $userid));

        $role = $DB->get_record('role', ['shortname' => $roleshortname]);
        if(!$role)
            cli_error(sprintf("Role not found with shortname %s", $roleshortname));

        try {
            $return = role_unassign($role->id, $user->id, 1);
        } catch (\moodle_exception $e) {
            cli_error($e->getMessage());
        }

        echo "OK!\n";


    }


}