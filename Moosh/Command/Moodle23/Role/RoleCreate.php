<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Role;
use Moosh\MooshCommand;

class RoleCreate extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('create', 'role');

        $this->addOption('n|name:');
        $this->addOption('d|description:');
        $this->addOption('a|archetype:');
        $this->addArgument('shortname');
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once($CFG->libdir . DIRECTORY_SEPARATOR . "accesslib.php");

        $options = $this->expandedOptions;
        $arguments = $this->arguments;

        //don't create if already exists
        $role = $DB->get_record('role', array('shortname' => $arguments[0]));
        if ($role) {
            echo "Role '" . $arguments[0] . "' already exists!\n";
            exit(0);
        }

        $newroleid = create_role($options['name'], $arguments[0], $options['description'], $options['archetype']);
        echo "$newroleid\n";
    }
}
