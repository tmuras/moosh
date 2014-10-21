<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Course;
use Moosh\MooshCommand;
use backup_controller;
use backup;

class CourseBackup extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('backup', 'course');

        $this->addOption('f|filename:', 'path to filename to save the course backup');
        $this->addOption('F|fullbackup', 'do full backup instead of general');

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
        if (!$options['filename']) {
            $options['filename'] = $this->cwd . '/backup_' . $shortname . '_' . date('Y.m.d') . '.mbz';
        } elseif ($options['filename'][0] != '/') {
            $options['filename'] = $this->cwd .'/' .$options['filename'];
        }

        //check if destination file does not exist and can be created
        if (file_exists($options['filename'])) {
            cli_error("File '{$options['filename']}' already exists, I will not over-write it.");
        }

        $bc = new backup_controller(\backup::TYPE_1COURSE, $this->arguments[0], backup::FORMAT_MOODLE,
            backup::INTERACTIVE_YES, backup::MODE_GENERAL, $USER->id);

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

        $bc->set_status(backup::STATUS_AWAITING);
        $bc->execute_plan();
        $result = $bc->get_results();
        $file = $result['backup_destination'];
        /** @var $file stored_file */

        $file->copy_content_to($options['filename']);
        printf("%s\n", $options['filename']);
    }
}
