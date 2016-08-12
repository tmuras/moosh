<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Course;
use Moosh\MooshCommand;

class CourseCreate extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('create', 'course');

        $this->addOption('c|category:', 'category id');
        $this->addOption('f|fullname:', 'full name');
        $this->addOption('d|description:', 'description');
        $this->addOption('F|format:', 'format (e.g. one of site, weeks, topics, etc.)');
        $this->addOption('i|idnumber:', 'id number');
        $this->addOption('v|visible:', 'visible (y or n, by default created visible)');

        $this->addArgument('shortname');

        $this->maxArguments = 255;
    }

    public function execute()
    {
        global $CFG;

        require_once $CFG->dirroot . '/course/lib.php';

        foreach ($this->arguments as $argument) {
            $this->expandOptionsManually(array($argument));
            $options = $this->expandedOptions;
            $course = new \stdClass();
            $course->fullname = $options['fullname'];
            $course->shortname = $argument;
            $course->description = $options['description'];
            $format = $options['format'];
            if(!$format){
            	$format = get_config('moodlecourse', 'format');
            }
            $course->format = $format;
            $course->idnumber = $options['idnumber'];
            $visible = strtolower($options['visible']);
            if($visible == 'n' || $visible == 'no' ){
            	$visible = 0;
            }else{
            	$visible = 1;
            }
            $course->visible = $visible;
            $course->category = $options['category'];
            $course->summary = '';
            $course->summaryformat = FORMAT_HTML;
            $course->startdate = time();

            //either use API create_course
            $newcourse = create_course($course);

            echo $newcourse->id . "\n";
        }
    }
}
