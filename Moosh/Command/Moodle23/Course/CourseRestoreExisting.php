<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Course;
use Moosh\MooshCommand;
use restore_controller;
use restore_dbops;
use backup;

class CourseRestoreExisting extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('restoreexisting', 'course');

        $this->addArgument('backup_file');
        $this->addArgument('course_id');
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once($CFG->dirroot."/backup/util/includes/backup_includes.php");
        require_once($CFG->dirroot."/backup/util/includes/restore_includes.php");

        //check if course is OK
        $course = $DB->get_record('course', array('id' => $this->arguments[1]), '*', MUST_EXIST);

        // deal with relative path
        $arguments = $this->arguments;
        if ($arguments[0][0] != '/') {
            $arguments[0] = $this->cwd . DIRECTORY_SEPARATOR . $arguments[0];
        }

        if (!file_exists($arguments[0])) {
            cli_error("Backup file '" . $arguments[0] . "' does not exist.");
        }

        if (!is_readable($arguments[0])) {
            cli_error("Backup file '" . $arguments[0] . "' is not readable.");
        }

        //unzip into $CFG->dataroot / "temp" / "backup" / "auto_restore_" . $split[1];
        $backupdir = "moosh_restore_" . uniqid();
        $path = $CFG->dataroot . DIRECTORY_SEPARATOR . "temp" . DIRECTORY_SEPARATOR . "backup" . DIRECTORY_SEPARATOR . $backupdir;
        if($this->verbose) {
            echo "Extracting Moode backup file to: '" . $path . "'\n";
        }

        /** @var $fp file_packer */
        $fp = get_file_packer('application/vnd.moodle.backup');
        $fp->extract_to_pathname($arguments[0], $path);

        $courseid = $course->id;
        $rc = new restore_controller($backupdir, $courseid, backup::INTERACTIVE_NO,
            backup::MODE_GENERAL, $this->user->id, backup::TARGET_CURRENT_ADDING);
        $plan = $rc->get_plan();
        $tasks = $plan->get_tasks();
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        echo "Restore Complete\n";
    }
}
