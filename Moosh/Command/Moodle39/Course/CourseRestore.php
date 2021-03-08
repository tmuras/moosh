<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Course;

use Moosh\MooshCommand;
use restore_controller;
use restore_dbops;
use backup;

class CourseRestore extends MooshCommand {
    public function __construct() {
        parent::__construct('restore', 'course');

        $this->addArgument('backup_file');
        $this->addArgument('category_id');

        $this->addOption('d|directory', 'restore from extracted directory (1st param) under tempdir/backup');
        $this->addOption('e|existing', 'restore into existing course, id provided instead of category_id');
        $this->addOption('o|overwrite', 'restore into existing course, overwrite current content, id provided instead of category_id');
        $this->addOption('i|ignore-warnings', 'continue with restore if there are pre-check warnings');
    }

    public function execute() {
        global $CFG, $DB, $USER;

        require_once($CFG->dirroot . "/backup/util/includes/backup_includes.php");
        require_once($CFG->dirroot . "/backup/util/includes/restore_includes.php");

        $arguments = $this->arguments;
        $options = $this->expandedOptions;

        if (empty($CFG->tempdir)) {
            $CFG->tempdir = $CFG->dataroot . DIRECTORY_SEPARATOR . 'temp';
        }

        if ($options['overwrite']) {
            $options['existing'] = true;
        }

            // Check if category is OK.
        if (!$options['existing']) {
            $category = $DB->get_record('course_categories', array('id' => $this->arguments[1]), '*', MUST_EXIST);
        } else {
            $course = $DB->get_record('course', array('id' => $this->arguments[1]), '*', MUST_EXIST);
            $category = $DB->get_record('course_categories', array('id' => $course->category), '*', MUST_EXIST);
        }

        if (!$options['directory']) {
            if ($arguments[0][0] != '/') {
                $arguments[0] = $this->cwd . DIRECTORY_SEPARATOR . $arguments[0];
            }

            if (!file_exists($arguments[0])) {
                cli_error("Backup file '" . $arguments[0] . "' does not exist.");
            }

            if (!is_readable($arguments[0])) {
                cli_error("Backup file '" . $arguments[0] . "' is not readable.");
            }

        } else {
            $path = $CFG->tempdir . DIRECTORY_SEPARATOR . "backup" . DIRECTORY_SEPARATOR . $arguments[0];
            if (!file_exists($path) || !is_dir($path) || !is_readable($path)) {
                cli_error("Directory '$path' does not exist, not a directory or not readable.");
            }
        }


        if (!$options['directory']) {
            //unzip into $CFG->tempdir / "backup" / "auto_restore_" . $split[1];
            $backupdir = "moosh_restore_" . uniqid();

            if (isset($CFG->backuptempdir)){
                $path = $CFG->backuptempdir . DIRECTORY_SEPARATOR . $backupdir;
            }
            else{
                $path = $CFG->tempdir . DIRECTORY_SEPARATOR . "backup" . DIRECTORY_SEPARATOR . $backupdir;
            }

            if ($this->verbose) {
                echo "Extracting Moode backup file to: '" . $path . "'\n";
            }

            /** @var $fp file_packer */
            $fp = get_file_packer('application/vnd.moodle.backup');
            $fp->extract_to_pathname($arguments[0], $path);
        } else {
            $backupdir = $arguments[0];
        }

        //extract original full & short names
        $xmlfile = $path . DIRECTORY_SEPARATOR . "course" . DIRECTORY_SEPARATOR . "course.xml";

        // Different XML file in Moodle 1.9 backup
        if (!file_exists($xmlfile)) {
            $xmlfile = $path . DIRECTORY_SEPARATOR . "moodle.xml";
        }

        $xml = simplexml_load_file($xmlfile);
        $fullname = $xml->xpath('/course/fullname');
        if (!$fullname) {
            $fullname = $xml->xpath('/MOODLE_BACKUP/COURSE/HEADER/FULLNAME');
        }
        $shortname = $xml->xpath('/course/shortname');
        if (!$shortname) {
            $shortname = $xml->xpath('/MOODLE_BACKUP/COURSE/HEADER/SHORTNAME');
        }

        $fullname = (string)($fullname[0]);
        $shortname = (string)($shortname[0]);

        if (!$shortname) {
            cli_error('No shortname in the backup file.');
        }

        if (!$fullname) {
            $fullname = $shortname;
        }

        // Get unique shortname if creating new course.
        if (!$options['existing'] && $DB->get_record('course', array('category' => $category->id, 'shortname' => $shortname))) {
            $matches = NULL;
            preg_match('/(.*)_(\d+)$/', $shortname, $matches);
            if ($matches) {
                $base = $matches[1];
                $number = $matches[2];
            } else {
                $base = $shortname;
                $number = 1;
            }
            $shortname = $base . '_' . $number;
            while ($DB->get_record('course', array('category' => $category->id, 'shortname' => $shortname))) {
                $number++;
                $shortname = $base . '_' . $number;
            }
        }

        if ($options['existing']) {
            $courseid = $arguments[1];
            if ($options['overwrite']) {
                echo "Overwriting current content of existing course -> Course ID: $courseid\n";
                $rc = new restore_controller($backupdir, $courseid, backup::INTERACTIVE_NO,
                    backup::MODE_GENERAL, $USER->id, 0);
            } else {
                echo "Adding backup to current content of existing course -> Course ID: $courseid\n";
                $rc = new restore_controller($backupdir, $courseid, backup::INTERACTIVE_NO,
                    backup::MODE_GENERAL, $USER->id, backup::TARGET_CURRENT_ADDING);
            }
        } else {
            echo "Creating new course to restore backup\n";
            $courseid = restore_dbops::create_new_course($fullname, $shortname, $category->id);
            $rc = new restore_controller($backupdir, $courseid, backup::INTERACTIVE_NO,
                backup::MODE_GENERAL, $USER->id, backup::TARGET_NEW_COURSE);
        }

        echo "Restoring (new course id,shortname,destination category): $courseid,$shortname," . $category->id . "\n";

        if ($rc->get_status() == backup::STATUS_REQUIRE_CONV) {
            $rc->convert();
        }
        $plan = $rc->get_plan();
        if (!$rc->execute_precheck()){
            $check = $rc->get_precheck_results();
            cli_problem("Restore pre-check failed!");
            var_dump($check);
            if (!$options['ignore-warnings'] || array_key_exists('errors',$check)) {
                die();
            }
        }

        if ($options['existing'] && $options['overwrite']) {
            // If existing course shall be overwritten, delete current content
            $deletingoptions = array();
            $deletingoptions['keep_roles_and_enrolments'] = 0;
            $deletingoptions['keep_groups_and_groupings'] = 0;
            restore_dbops::delete_course_content($courseid, $deletingoptions);
        }

        $rc->execute_plan();
        $rc->destroy();

        echo "New course ID for '$shortname': $courseid in category {$category->id}\n";
    }
}
