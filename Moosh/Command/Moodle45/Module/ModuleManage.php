<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle45\Module;
use Moosh\MooshCommand;

class ModuleManage extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('manage', 'module');

        $this->addArgument('action');
        $this->addArgument('blockname');
        $this->addOption('f|force', 'force delete of module from disk');

    }

    public function execute()
    {
        global $DB, $CFG;

        require_once($CFG->dirroot.'/lib/classes/plugin_manager.php');

        $action = $this->arguments[0];
        $modulename = $this->arguments[1]; // name of the module (in English)

        // Does module exists?
        if (!empty($modulename)) {
            if (!$module = $DB->get_record('modules', array('name'=>$modulename))) {
                print_error('moduledoesnotexist', 'error');
            }
        }

        switch ($action) {
            case 'show':
                $DB->set_field('modules', 'visible', '1', array('id'=>$module->id));      // Show module.
                $DB->set_field('course_modules', 'visible', '1', ['visibleold' => 1, 'module' => $module->id]);
                
                increment_revision_number('course', 'cacherev',
                        "id IN (SELECT DISTINCT course
                                        FROM {course_modules}
                                        WHERE visible = 1 AND module = ?)",
                        [$module->id]);
                
                \core_plugin_manager::reset_caches();
                break;

            case 'hide':
                $DB->set_field('modules', 'visible', '0', array('id'=>$module->id));      // Hide module.
                
                $sql = "UPDATE {course_modules}
                           SET visibleold = visible, visible = 0
                         WHERE module = ?";
                $DB->execute($sql, [$module->id]);
                
                increment_revision_number('course', 'cacherev',
                        "id IN (SELECT DISTINCT course
                                        FROM {course_modules}
                                        WHERE visibleold = 1 AND module = ?)",
                        [$module->id]);

                \core_plugin_manager::reset_caches();
                break;

            case 'delete':
                // Delete module from DB. Should we also delete it from disk?
                if ($this->expandedOptions['force']) {
                    // Delete module from disk too!
                }
                break;
        }

    }

    protected function getArgumentsHelp()
    {
        return "\n\nARGUMENTS:".
                "\n\taction[show|hide|delete] modulename";
                //"\n\n\t-f|--force delete module from disk too";
    }
}
