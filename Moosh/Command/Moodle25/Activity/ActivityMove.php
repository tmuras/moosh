<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace Moosh\Command\Moodle25\Activity;
use Moosh\MooshCommand;

class ActivityMove extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('move', 'activity');

        $this->addOption('s|sectionnumber:=number', 'sectionnumber', null);

        $this->addArgument('moduleid');
        $this->addArgument('beforemodid');

        $this->minArguments = 1;
        $this->maxArguments = 2;
    }

    public function execute() 
    {
        global $CFG, $DB;
        require_once $CFG->dirroot . '/course/lib.php';

        $options = $this->expandedOptions;

        $moduleid = intval($this->arguments[0]);

        if ($moduleid <= 0) {
            cli_error("Argument 'moduleid' must be bigger than 0.");
        } 
        $module = get_coursemodule_from_id('', $moduleid);
        if ( empty($module) ) {
            cli_error("There is no such activity to delete.");
        }

        $courseid = $module->course;
        $course = $DB->get_record('course', array('id' => $courseid));

        if (!empty($options['sectionnumber'])) {
            $sectionnumber = $options['sectionnumber'];
            $destinesection = $DB->get_record('course_sections', array(
                'section' => $sectionnumber,
                'course' => $courseid));
        }
        else {
            $sectionid = $module->section;
            $destinesection = $DB->get_record('course_sections', array('id' => $sectionid));
        }

        if ( !empty($this->arguments[1]) ) {
            $beforemodid = intval($this->arguments[1]);
            moveto_module($module, $destinesection, $beforemodid);
        }
        else {
            moveto_module($module, $destinesection);

        }
        echo "Moved activity $moduleid\n";
    }
}

