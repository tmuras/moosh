<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Course;
use ___PHPSTORM_HELPERS\object;
use Moosh\MooshCommand;

class CourseReset extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('reset', 'course');

        $this->addArgument('id');
    }

    public function execute()
    {
        global $CFG, $DB, $COURSE;

        $course = $DB->get_record('course', array('id'=>$this->arguments[0]));
        $COURSE = $course;

        $defaults = $this->loadDefaults();
        $defaults->id = $course->id;
        reset_course_userdata($defaults);
    }

    protected function loadDefaults() {
        global $DB, $CFG, $COURSE;

        $COURSE = new \stdClass();
        $COURSE->id = $this->arguments[0];
        $defaults = array ('reset_events'=>1, 'reset_logs'=>1, 'reset_roles_local'=>1, 'reset_gradebook_grades'=>1, 'reset_notes'=>1);
        if ($allmods = $DB->get_records('modules') ) {
            foreach ($allmods as $mod) {
                $modname = $mod->name;
                $modfile = $this->topDir."/mod/$modname/lib.php";
                $mod_reset_course_form_defaults = $modname.'_reset_course_form_defaults';
                if (file_exists($modfile)) {
                    @include_once($modfile);
                    if (function_exists($mod_reset_course_form_defaults)) {
                        if ($moddefs = $mod_reset_course_form_defaults($COURSE)) {
                            $defaults = $defaults + $moddefs;
                        }
                    }
                }
            }
        }
        return (object)$defaults;
    }
}
