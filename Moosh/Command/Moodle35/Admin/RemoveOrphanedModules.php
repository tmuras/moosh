<?php
/**
 * moosh admin-remove-orphaned-modules [--removeall]
 *
 * @example sudo -u www-data  moosh admin-remove-orphaned-modules  -r all
 * @example sudo -u www-data  moosh admin-remove-orphaned-modules  -remove=90,105,1999
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle35\Admin;

use Moosh\MooshCommand;
use core_plugin_manager;

class RemoveOrphanedModules extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('remove-orphaned-modules', 'admin');

        $this->addOption('r|remove', 'remove found orphaned modules: all or given ids separated by comma', 0);

        $this->minArguments = 0;
        $this->maxArguments = 1;
    }

    public function execute() {
        global $CFG, $DB;

        $options = $this->expandedOptions;

        $remove = false;
        $removeall = false;
        if ($options['remove']) {
            $remove = (bool)$options['remove'];
        }
        //input data parsing
        if ($remove === true) {
            if ($this->arguments[0] === 'all') {
                $removeall = true;
            } else {
                if (strpos(',', $this->arguments[0]) === false && (int)$this->arguments[0] != $this->arguments[0]) {
                    print_error("Wrong 'remove' option format. Please, pass option value like: course_module_id_1,course_module_id_2,course_module_id_3 or 'all'");
                }
                $remove = explode(',', $this->arguments[0]);
            }
        }

        //init SQL queries;
        $selectquery = 'SELECT * FROM {course_modules} WHERE instance NOT IN
            (
                SELECT id
                FROM `{module_table_name}`
            ) AND module IN
            (
                SELECT id
                FROM mdl_modules
                WHERE name = :module_name
            )';
        $adhocquery = 'SELECT *, FROM_UNIXTIME(nextruntime) as nextruntimestr FROM {task_adhoc} WHERE classname = \'\\\core_course\\\task\\\course_delete_modules\' AND customdata LIKE \'%"module":"{moduleid}"%\'';

        $tables = $DB->get_tables();

        $modules = $DB->get_records('modules');
        //check all orphaned modules records
        foreach ($modules as $module) {
            if (!in_array($module->name, $tables)) {
                echo 'Table does not exists: ' . $module->name . "\n";
                continue;
            }
            $moduleselectsql = str_replace('{module_table_name}', $CFG->prefix . $module->name, $selectquery);

            $orphanedrows = $DB->get_records_sql($moduleselectsql, array('module_name' => $module->name));
            if ($orphanedrows) {
                $moduleadhocquery = str_replace('{moduleid}', (int)$module->id, $adhocquery);
                $adhocs = $DB->get_records_sql($moduleadhocquery); //, array('moduleid' => (int)$module->id));
                $adhocstmp = array();
                foreach ($adhocs as $adhoc) {
                    preg_match('/\"course\":\"(\d*)\",.*\"instance\":\"(\d*)\"/', $adhoc->customdata, $matches);
                    $adhocstmp[$matches[1] . '_' . $matches[2]] = $adhoc;
                }
                $adhocs = $adhocstmp;
                $adhocstmp = null;
                echo 'Module: ' . $module->name . "\n";
                foreach ($orphanedrows as $orphanedrow) {
                    $adhocdetails = ' --- NO related adhoc cron task found';
                    $adhockey = $orphanedrow->course . '_' . $orphanedrow->instance;
                    if (isset($adhocs[$adhockey])) {
                        $adhocdetails = ' --- Next cron run: ' . $adhocs[$adhockey]->nextruntimestr;
                        $lastperformstatus = ', was NOT performed yet';
                        if ($adhocs[$adhockey]->faildelay > 0) {
                            $lastperformstatus = ', last perform status: FAILED';
                        }
                        $adhocdetails .= $lastperformstatus;
                    }
                    echo ' - id=' . $orphanedrow->id . ', course=' . $orphanedrow->course . ', instance=' . $orphanedrow->instance . $adhocdetails . "\n";

                    // remove records if required
                    if ((!$removeall && $remove && in_array($orphanedrow->id, $remove)) || $removeall) {
                        if ($DB->delete_records('course_modules', array('id' => $orphanedrow->id))) {
                            echo '   - Removed.' . "\n";
                        } else {
                            echo ' - course module removing ERROR' . "\n";
                        }
                    }
                }
            }
        }
    }
}
