<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Course;

use Moosh\MooshCommand;

class CourseReset extends MooshCommand {
    public function __construct() {
        parent::__construct('reset', 'course');

        $this->addArgument('id');
    }

    public function execute() {
        global $DB;

        if (!$this->course = $DB->get_record('course', array('id' => $this->arguments[0]))) {
            print_error("invalidcourseid");
        }

        require_login($this->course);

        $defaults = $this->loadDefaults();
        $defaults->id = $this->course->id;
        $status = reset_course_userdata($defaults);

        print_r($status);
    }

    protected function loadDefaults() {
        global $DB;

        if (!$course = $DB->get_record('course', array('id' => $this->arguments[0]))) {
            print_error("invalidcourseid");
        }

        require_login($course);
        $defaults = array('reset_events' => 1, 'reset_logs' => 1, 'reset_roles_local' => 1, 'reset_gradebook_grades' => 1, 'reset_notes' => 1);
        if ($allmods = $DB->get_records('modules')) {
            foreach ($allmods as $mod) {
                $modname = $mod->name;
                $modfile = $this->topDir . "/mod/$modname/lib.php";
                $mod_reset_course_form_defaults = $modname . '_reset_course_form_defaults';
                if (file_exists($modfile)) {
                    include_once($modfile);
                    if (function_exists($mod_reset_course_form_defaults)) {
                        if ($moddefs = $mod_reset_course_form_defaults($this->course)) {
                            $defaults = $defaults + $moddefs;
                        }
                    }
                }
            }
        }
        return (object)$defaults;
    }
}
