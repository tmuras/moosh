<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Course;
use Moosh\MooshCommand;
use backup_controller;
use backup;

class CourseBackup extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('backup', 'course');

        $this->addOption('f|filename:', 'path to filename to save the course backup');
        $this->addOption('p|path:', 'path to save the course backup');
        $this->addOption('s|section:', 'include only this section in backup');
        $this->addOption('F|fullbackup', 'do full backup instead of general');
        $this->addOption('template', 'do template backup instead of general');
        $this->addOption('e|exclude:', 'comma-separated list of module names to exclude');
        $this->addOption('excludecmid:=number', 'comma-separated list of module CMIDs to exclude');

        $this->addArgument('id');
        
    }

    public function execute()
    {
        global $CFG, $DB, $USER;

        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');        

        //check if course id exists
        $course = $DB->get_record('course', array('id' => $this->arguments[0]), '*', MUST_EXIST);
        $shortname = str_replace(' ', '_', $course->shortname);

        $options = $this->expandedOptions;

        if (strlen($options['section'])) {
            $section = $DB->get_record('course_sections', array('course' => $this->arguments[0], 'section' => $options['section']), '*', MUST_EXIST);
        }

        $cwd=$this->cwd;
        if (trim($options['path'])!="") {
            $cwd=$options['path'];
        }

        if (!$options['filename']) {
            if (strlen($options['section'])) {
                // clean up the section name, remove invalid characters, etc. so we can use it in the filename
                $sectionfilename = str_replace(' ','-',$section->name);
                $sectionfilename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $sectionfilename); $sectionfilename = mb_ereg_replace("([\.]{2,})", '', $sectionfilename);
                $options['filename'] = $cwd . '/backup_' . $this->arguments[0] . "_". str_replace('/','_',$shortname) . '_' . $section->section . '_' . $sectionfilename . '_' . date('Y.m.d') . '.mbz';
            } else {
                $options['filename'] = $cwd . '/backup_' . $this->arguments[0] . "_". str_replace('/','_',$shortname) . '_' . date('Y.m.d') . '.mbz';
            }
        } elseif ($options['filename'][0] != '/') {
            $options['filename'] = $cwd .'/' .$options['filename'];
        }

        //check if destination file does not exist and can be created
        if (file_exists($options['filename'])) {
            cli_error("File '{$options['filename']}' already exists, I will not over-write it.");
        }

        if (strlen($options['section'])) {
            $bc = new backup_controller(\backup::TYPE_1SECTION, $section->id, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_YES, backup::MODE_GENERAL, $USER->id);
        } else {
            $bc = new backup_controller(\backup::TYPE_1COURSE, $this->arguments[0], backup::FORMAT_MOODLE,
            backup::INTERACTIVE_YES, backup::MODE_GENERAL, $USER->id);
        }

        if ($options['exclude']) {
            $excludemodules = explode(',', $options['exclude']);
            $tasks = $bc->get_plan()->get_tasks();
            foreach ($tasks as &$task) {
                if (is_subclass_of($task, 'backup_activity_task')) {
                    $modulename = $task->get_modulename();
                    if (in_array($modulename, $excludemodules)) {
                        $setting = $task->get_setting('included');
                        $setting->set_value('0');
                    }
                }
            }
        }

        if ($options['excludecmid']) {
            $excludecmids = explode(',', $options['excludecmid']);
            $tasks = $bc->get_plan()->get_tasks();
            foreach ($tasks as &$task) {
                if (is_subclass_of($task, 'backup_activity_task')) {
                    $cmid = $task->get_moduleid();
                    if (in_array($cmid, $excludecmids)) {
                        $setting = $task->get_setting('included');
                        $setting->set_value('0');
                    }
                }
            }
        }

        if ($options['fullbackup']) {
            $tasks = $bc->get_plan()->get_tasks();
            foreach ($tasks as &$task) {
                if ($task instanceof \backup_root_task) {
                    $setting = $task->get_setting('logs');
                    $setting->set_value('1');
                    $setting = $task->get_setting('grade_histories');
                    $setting->set_value('1');
                } 
            } 
        }

        if ($options['template']) {
            $tasks = $bc->get_plan()->get_tasks();
            foreach ($tasks as &$task) {
                if ($task instanceof \backup_root_task) {
                    $setting = $task->get_setting('users');
                    $setting->set_value('0');
                    $setting = $task->get_setting('anonymize');
                    $setting->set_value('1');
                    $setting = $task->get_setting('role_assignments');
                    $setting->set_value('0');
                    $setting = $task->get_setting('filters');
                    $setting->set_value('0');
                    $setting = $task->get_setting('comments');
                    $setting->set_value('0');
                    $setting = $task->get_setting('logs');
                    $setting->set_value('0');
                    $setting = $task->get_setting('grade_histories');
                    $setting->set_value('0');
                } 
            } 
        }
        
        $bc->set_status(backup::STATUS_AWAITING);
        $bc->execute_plan();
        $result = $bc->get_results();

        if(isset($result['backup_destination']) && $result['backup_destination']) {
            $file = $result['backup_destination'];
            /** @var $file stored_file */

            if(!$file->copy_content_to($options['filename'])) {
                cli_error("Problems copying final backup to '". $options['filename'] . "'");
            } else {
                printf("%s\n", $options['filename']);
            }
        } else {
	    echo $bc->get_backupid();
        }
    }
}
