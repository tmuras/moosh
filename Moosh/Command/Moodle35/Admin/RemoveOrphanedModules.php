<?php
/**
 * moosh admin-remove-orphaned-modules [--removeall
 *                   modulesmap1=<modulesmaptable1> [modulesmap2=<modulesmaptable2> ...]
 *
 * @example sudo -u www-data  moosh admin-remove-orphaned-modules  -r all module_name_1=module_table_name_1 module_name_2=module_table_name_2
 * @example sudo -u www-data  moosh admin-remove-orphaned-modules  -remove=90,105,1999 module_name_1=module_table_name_1 module_name_2=module_table_name_2
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

        $this->addOption('r|remove', 'remove all found orphaned modules', 0);

        $this->addArgument('modulesmap');
        $this->minArguments = 0;
        $this->maxArguments = 100;
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
        $mapping = [];
        foreach ($this->arguments as $argument) {
            $expanded = explode('=', $argument);
            if (count($expanded) == 2) {
                $mapping[$expanded[0]] = $expanded[1];
            } else if ($remove === true) {
                if ($argument === 'all') {
                    $removeall = true;
                } else {
                    if (strpos(',', $argument) === false && (int)$argument != $argument) {
                        print_error("Wrong 'remove' option format. Please, pass option value like: course_module_id_1,course_module_id_2,course_module_id_3 or 'all'");
                    }
                    $remove = explode(',', $argument);
                }
            } else {
                print_error("Wrong arguments format. Please, pass arguments like: module_name=module_instances_table_name_without_prefix");
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
                WHERE name = \'{module_name}\'
            )';
        $adhocquery = 'SELECT *, FROM_UNIXTIME(nextruntime) as nextruntimestr FROM {task_adhoc} WHERE classname = \'\\\core_course\\\task\\\course_delete_modules\' AND customdata LIKE \'%"course":"{courseid}","module":"{moduleid}","instance":"{instanceid}"%\'';

        $tables = $DB->get_tables();

        $modules = $DB->get_records('modules');
        //check all orphaned modules records
        foreach ($modules as $module) {
            if (!in_array($module->name, $tables) && !isset($mapping[$module->name])) {
                echo 'Table does not exists: ' . $module->name . "\n";
            } else {
                $moduleselectsql = str_replace('{module_name}', $module->name, $selectquery);
                if (isset($mapping[$module->name])) {
                    $moduleselectsql = str_replace('{module_table_name}', $CFG->prefix . $mapping[$module->name], $moduleselectsql);
                } else {
                    $moduleselectsql = str_replace('{module_table_name}', $CFG->prefix . $module->name, $moduleselectsql);
                }

                $orphanedrows = $DB->get_records_sql($moduleselectsql);
                if ($orphanedrows) {
                    echo 'Module: ' . $module->name . "\n";
                    foreach ($orphanedrows as $orphanedrow) {
                        $instanceadhocquery = str_replace('{courseid}', $orphanedrow->course, $adhocquery);
                        $instanceadhocquery = str_replace('{moduleid}', $orphanedrow->module, $instanceadhocquery);
                        $instanceadhocquery = str_replace('{instanceid}', $orphanedrow->instance, $instanceadhocquery);
                        $adhoc = $DB->get_record_sql($instanceadhocquery);

                        $adhocdetails = ' --- NO adhoc cron task found';
                        if ($adhoc) {
                            $adhocdetails = ' --- Next cron run: ' . $adhoc->nextruntimestr;
                            $lastperformstatus = ', was NOT performed yet';
                            if ($adhoc->faildelay > 0) {
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
}
