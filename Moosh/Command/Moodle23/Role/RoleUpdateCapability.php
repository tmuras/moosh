<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Role;
use Moosh\MooshCommand;

class RoleUpdateCapability extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('update-capability', 'role');

        $this->addOption('i|id', 'use numerical id instead of short name');
        $this->addArgument('role'); // id or shortname
        $this->addArgument('capability'); // Example: block/calendar_month:myaddinstance
        $this->addArgument('value'); // CAP_INHERIT=0, CAP_ALLOW=1, CAP_PREVENT=-1, CAP_PROHIBIT=-1000
        $this->addArgument('contextid'); // Should be "1" for core system wide roles
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once($CFG->libdir . DIRECTORY_SEPARATOR . "accesslib.php");

        $options = $this->expandedOptions;
        $arguments = $this->arguments;

        //execute method
        if ($options['id']) {
            $role = $DB->get_record('role', array('id' => $arguments[0]));
            if (!$role) {
                echo "Role with id '" . $arguments[0] . "' does not exist\n";
                exit(0);
            }
        } else {
            $role = $DB->get_record('role', array('shortname' => $arguments[0]));
            if (!$role) {
                echo "Role '" . $arguments[0] . "' does not exist.\n";
                exit(0);
            }
        }

        $capability = CAP_ALLOW;
        switch ($arguments[2]) {
            case 'inherit': $capability = CAP_INHERIT; break;
            case 'allow': $capability = CAP_ALLOW; break;
            case 'prevent': $capability = CAP_PREVENT; break;
            case 'prohibit': $capability = CAP_PROHIBIT; break;
        }
        if (assign_capability($arguments[1],$capability,$role->id,$arguments[3],true)) {
            echo "Capability '{$arguments[1]}' was set to {$capability} for roleid {$role->id} ({$role->shortname}) successfuly\n";
        }
        echo "\n";
    }
}
