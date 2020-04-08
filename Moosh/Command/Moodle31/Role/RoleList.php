<?php


namespace Moosh\Command\Moodle31\Role;

use Moosh\MooshCommand;

class RoleList extends MooshCommand
{

    public function __construct()
    {
        parent::__construct('list', 'role');
    }

    public function execute() {

        global $CFG, $DB;

        $roles = $DB->get_records("role");

        $mask = "|%5.5s |%-30.30s |%-30.30s |\n";
        printf($mask, 'ID', 'Shortname', 'Name');

        foreach($roles as $role) {

            printf($mask, $role->id, $role->shortname, $role->name);

        }

    }

}