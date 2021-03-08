<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Role;
use Moosh\MooshCommand;

class RoleUpdateContextLevel extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('update-contextlevel', 'role');

        $this->addOption('i|id', 'use numerical id instead of short name');
        $this->addOption('cl10on|system-on', 'Can be used on System context ');
        $this->addOption('cl10off|system-off', 'Can be used on System context ');
        $this->addOption('cl30on|user-on', 'Can be used on User context ');
        $this->addOption('cl30off|user-off', 'Can be used on User context ');
        $this->addOption('cl40on|category-on', 'Can be used on Category context ');
        $this->addOption('cl40off|category-off', 'Can be used on Category context ');
        $this->addOption('cl50on|course-on', 'Can be used on Course context ');
        $this->addOption('cl50off|course-off', 'Can be used on Course context ');
        $this->addOption('cl70on|activity-on', 'Can be used on Activity context ');
        $this->addOption('cl70off|activity-off', 'Can be used on Activity context ');
        $this->addOption('cl80on|block-on', 'Can be used on Block context ');
        $this->addOption('cl80off|block-off', 'Can be used on Block context ');
        $this->addArgument('role');
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

        // Enable relevant context level for the selected role
        $contextlevels = array();
        if ($options['system-on']) {$contextlevels[] = '10';}
        if ($options['user-on']) {$contextlevels[] = '30';}
        if ($options['category-on']) {$contextlevels[] = '40';}
        if ($options['course-on']) {$contextlevels[] = '50';}
        if ($options['activity-on']) {$contextlevels[] = '70';}
        if ($options['block-on']) {$contextlevels[] = '80';}
        foreach ($contextlevels as $level) {
            if (!$DB->get_record('role_context_levels', array('roleid' => $role->id, 'contextlevel'=>$level))) {
                $role_context_level = new \stdClass();
                $role_context_level->roleid = $role->id;
                $role_context_level->contextlevel = $level;
                if ($ok = $DB->insert_record('role_context_levels',$role_context_level)) {
                    echo "Context level {$level} for roleid {$role->id} ({$role->shortname}) updated (on) successfuly\n";
                };
            }
        }

        // Disable relevant context level for the selected role
        $contextlevels_remove = array();
        if ($options['system-off']) {$contextlevels_remove[] = '10';}
        if ($options['user-off']) {$contextlevels_remove[] = '30';}
        if ($options['category-off']) {$contextlevels_remove[] = '40';}
        if ($options['course-off']) {$contextlevels_remove[] = '50';}
        if ($options['activity-off']) {$contextlevels_remove[] = '70';}
        if ($options['block-off']) {$contextlevels_remove[] = '80';}
        foreach ($contextlevels_remove as $level) {
            if ($DB->delete_records('role_context_levels',array('roleid' => $role->id, 'contextlevel'=>$level))) {
                echo "Context level {$level} for roleid {$role->id} ({$role->shortname}) updated (off) successfuly\n";
            };
        }

        echo "\n";
    }
}
