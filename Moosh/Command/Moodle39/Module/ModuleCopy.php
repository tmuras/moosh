<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @author     Marty Gilbert
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Module;
use Moosh\MooshCommand;

use restore_controller;
use backup;
use backup_controller;
use context_module;

class ModuleCopy extends MooshCommand {

    public function __construct() {
        parent::__construct('copy', 'module');

        $this->addArgument('moduleid');
        $this->addArgument('courseid');

        $this->addOption('n|name:', 'new name of the copied module');
        $this->addOption('s|section:', 'destination section');
    }

    protected function getArgumentsHelp() {
        $ret = "\n\nARGUMENTS:";
        $ret .= "\n\t";
        $ret .= implode(' ', $this->argumentNames);
        $ret .= "\n\n\tmoduleid: id of the module to copy";
        $ret .= "\n\n\tcourseid: id of the destination course";

        return $ret;
    }

    public function execute() {

        $options = $this->expandedOptions;

        list($moduleid, $courseid) = $this->arguments;

        $newname = '';
        $section = 0;

        if ($options['name']) {
            $newname = $options['name'];
        }

        if ($options['section']) {
            $section = $options['section'];
        }

        $cm = get_coursemodule_from_id('', $moduleid, 0, true, MUST_EXIST);

        $this->copy_module($cm, $courseid, $section, $newname);
    }

    /**
     * Adapted from MOODLE_38_STABLE duplicate_module in course/lib.php
     *
     * @param object $cm course module object to be duplicated.
     * @param int $tocourse destination course id.
     * @param int $section destination section. If invalid, section 0.
     * @since Moodle 2.8
     *
     * @throws Exception
     * @throws coding_exception
     * @throws moodle_exception
     * @throws restore_controller_exception
     *
     * @return cm_info|null cminfo object if we sucessfully duplicated the mod and found the new cm.
     */
    protected function copy_module($cm, $tocourse, $tosection, $newname='') {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->libdir  . '/filelib.php');

        $a          = new \stdClass();
        $a->modtype = get_string('modulename', $cm->modname);
        $a->modname = format_string($cm->name);

        if (!plugin_supports('mod', $cm->modname, FEATURE_BACKUP_MOODLE2)) {
            throw new moodle_exception('duplicatenosupport', 'error', '', $a);
        }

        // Backup the activity.
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $cm->id, backup::FORMAT_MOODLE,
                backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id);

        $backupid       = $bc->get_backupid();
        $backupbasepath = $bc->get_plan()->get_basepath();

        $bc->execute_plan();

        $bc->destroy();

        // Restore the backup immediately.

        $rc = new restore_controller($backupid, $tocourse,
                backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id, backup::TARGET_CURRENT_ADDING);

        // Make sure that the restore_general_groups setting is always enabled when duplicating an activity.
        $plan = $rc->get_plan();
        $groupsetting = $plan->get_setting('groups');
        if (empty($groupsetting->get_value())) {
            $groupsetting->set_value(true);
        }

        $cmcontext = context_module::instance($cm->id);
        if (!$rc->execute_precheck()) {
            $precheckresults = $rc->get_precheck_results();
            if (is_array($precheckresults) && !empty($precheckresults['errors'])) {
                if (empty($CFG->keeptempdirectoriesonbackup)) {
                    fulldelete($backupbasepath);
                }
            }
        }

        $rc->execute_plan();

        // Now a bit hacky part follows - we try to get the cmid of the newly
        // restored copy of the module.
        $newcmid = null;
        $tasks = $rc->get_plan()->get_tasks();
        foreach ($tasks as $task) {
            if (is_subclass_of($task, 'restore_activity_task')) {
                if ($task->get_old_contextid() == $cmcontext->id) {
                    $newcmid = $task->get_moduleid();
                    break;
                }
            }
        }

        $rc->destroy();

        if (empty($CFG->keeptempdirectoriesonbackup)) {
            fulldelete($backupbasepath);
        }

        if ($newcmid) {
            // Proceed with activity renaming before everything else. We don't use APIs here to avoid
            // triggering a lot of create/update duplicated events.
            $newcm = get_coursemodule_from_id($cm->modname, $newcmid, $tocourse);

            if (empty($newname)) {
                // Add ' (copy)' to duplicates. Note we don't cleanup or validate lengths here. It comes
                // from original name that was valid, so the copy should be too.
                $newname = get_string('duplicatedmodule', 'moodle', $newcm->name);
            }
            $DB->set_field($cm->modname, 'name', $newname, ['id' => $newcm->instance]);

            // If the $tocourse is different from the original, reset access restrictions.
            if ($tocourse != $cm->course) {
                $DB->set_field('course_modules', 'availability', null, array('id' => $newcmid));
            }

            $section = $DB->get_record('course_sections', array('course' => $tocourse, 'section' => $tosection));

            // Move it to the desired section, or section 0 if invalid section given.
            if (!$section) {
                // Incorrect session given. Default to 0.
                $section = $DB->get_record('course_sections', array('course' => $tocourse, 'section' => 0));
            }

            moveto_module($newcm, $section);

            // Update calendar events with the duplicated module.
            // The following line is to be removed in MDL-58906.
            course_module_update_calendar_events($newcm->modname, null, $newcm);

            // Trigger course module created event. We can trigger the event only if we know the newcmid.
            $newcm = get_fast_modinfo($tocourse)->get_cm($newcmid);
            $event = \core\event\course_module_created::create_from_cm($newcm);
            $event->trigger();
        }

        return isset($newcm) ? $newcm : null;
    }
}
