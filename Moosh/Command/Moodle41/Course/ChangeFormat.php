<?php

/**
 * Course Change format moosh
 * moosh - Moodle Shell
 * @copyright 2025 unistra {@link http://unistra.fr}
 * @author 2025 Céline Perves <cperves@unistra.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle41\Course;
use Moosh\MooshCommand;

class ChangeFormat extends MooshCommand {
    public function __construct() {
        parent::__construct("change-format", "course");
        $this->addArgument('courseid');
        $this->addArgument('format');
    }
    public function execute(){
        global $DB, $CFG;
        require_once($CFG->dirroot.'/course/lib.php');
        require_once($CFG->libdir.'/classes/plugininfo/format.php');

        $courseid = trim($this->arguments[0]);
        $format = trim($this->arguments[1]);
        // Check that course format exists
        $formatplugins = \core\plugininfo\format::get_enabled_plugins();
        if (!isset($formatplugins[$format])) {
            cli_error("Format $format not found");
        }
        $oldcourse = $DB->get_record('course', array( 'id' => $courseid));
        if (!$oldcourse) {
            cli_error("course $courseid not found");
        }
        $course = $oldcourse;
        $course->format = $format;
        update_course($course); // editoroptions can be set to null since no summary editor options were changed
        cli_writeln("course format for $courseid sucessfully changed to $format");
    }

    protected function getArgumentsHelp() {
        $help = parent::getArgumentsHelp();
        $help .= "\n\n";
        $help .= "This command enable to change a course format";
        return $help;
    }
}