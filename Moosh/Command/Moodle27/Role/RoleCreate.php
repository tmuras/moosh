<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle27\Role;
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

        require_once($CFG->libdir . DIRECTORY_SEPARATOR . "testing". DIRECTORY_SEPARATOR . "generator" . DIRECTORY_SEPARATOR . "data_generator.php");
        $generator = new \testing_data_generator();

        $options = $this->expandedOptions;
        $arguments = $this->arguments;

        //don't create if already exists
        $role = $DB->get_record('role', array('shortname' => $arguments[0]));
        if ($role) {
            echo "Role '" . $arguments[0] . "' already exists!\n";
            exit(0);
        }

        $options['shortname'] = $arguments[0];
        $newroleid = $generator->create_role($options);
        echo "$newroleid\n";
    }
}
