<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Dev;
use Moosh\MooshCommand;

class RestoreSettings extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('settings', 'restore');

        //$this->addArgument();

        //$this->addOption('i|include-text:', 'make sure this piece of text is included in the random content', NULL);

    }


    public function execute()
    {
        //some variables you may want to use
        //$this->cwd - the directory where moosh command was executed
        //$this->mooshDir - moosh installation directory
        //$this->expandedOptions - commandline provided options, merged with defaults
        //$this->topDir - top Moodle directory
        global $CFG, $DB, $USER;

        require_once($CFG->dirroot . "/backup/util/includes/backup_includes.php");
        require_once($CFG->dirroot . "/backup/util/includes/restore_includes.php");

        // Find moosh001 or create empty course.
        $course = $DB->get_record('course', ['shortname' => 'moosh001']);
        if ($course) {
            $courseid = $course->id;
        } else {
            $courseid = \restore_dbops::create_new_course('Moosh restore empty course', 'moosh001', 1);
        }

        // Back it up.
        $CFG->keeptempdirectoriesonbackup = true;

        $bc = new \backup_controller(\backup::TYPE_1COURSE, $courseid, \backup::FORMAT_MOODLE,
                \backup::INTERACTIVE_NO, \backup::MODE_GENERAL, $USER->id);
        $bc->execute_plan();
        $result = $bc->get_results();
        $backupid = $bc->get_backupid();

        // Read with restore controller.
        $rc = new \restore_controller($backupid, 1, \backup::INTERACTIVE_NO,
                \backup::MODE_GENERAL, $USER->id, 0);
        $plan = $rc->get_plan();

        // Dump settings.
        $settings = $plan->get_settings();
        foreach ($settings as $k => $setting) {
            /** @var \restore_generic_setting $setting */
            echo "*** $k ***\n";
            if ($setting->has_help()) {
                echo $setting->get_help();
            }

            echo "UI name: " . $setting->get_ui_name() . "\n";
            echo "UI type: " . $this->ui_type_string($setting->get_ui_type()) . " (" . $setting->get_ui_type() . ")\n";
            echo "Name: " . $setting->get_name() . "\n";
            echo "Level: " . $this->level_string($setting->get_level()) . " (" . $setting->get_level() . ")\n";
            echo "\n";
        }
    }

    private function ui_type_string($uitype) {

        switch ($uitype) {
            case \base_setting::UI_NONE:
                return 'UI_NONE';

            case \base_setting::UI_HTML_CHECKBOX:
                return 'UI_HTML_CHECKBOX';

            case \base_setting::UI_HTML_RADIOBUTTON:
                return 'UI_HTML_RADIOBUTTON';

            case \base_setting::UI_HTML_DROPDOWN:
                return 'UI_HTML_DROPDOWN';

            case \base_setting::UI_HTML_TEXTFIELD:
                return 'UI_HTML_TEXTFIELD';

        }

    }

    private function level_string($level) {

        switch ($level) {
            case \backup_setting::ROOT_LEVEL:
                return 'ROOT_LEVEL';

            case \backup_setting::COURSE_LEVEL:
                return 'COURSE_LEVEL';

            case \backup_setting::SECTION_LEVEL:
                return 'SECTION_LEVEL';

            case \backup_setting::ACTIVITY_LEVEL:
                return 'ACTIVITY_LEVEL';

        }

    }


}


