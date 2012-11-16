<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class CourseCreate extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('create', 'course');

        $this->addOption('c|category:', 'category');
        $this->addOption('f|fullname:', 'full name');
        $this->addOption('d|description:', 'description');
        $this->addOption('F|format:', 'format');
        $this->addOption('v|visible:', 'visible');

        $this->addRequiredArgument('shortname');

        $this->maxArguments = 255;
    }

    public function execute()
    {
        global $CFG;

        require_once $CFG->dirroot . '/course/lib.php';

        foreach ($this->arguments as $argument) {
            $this->expandOptionsManually(array($argument));
            $options = $this->expandedOptions;
            $course = new stdClass();
            $course->fullname = $options['fullname'];
            $course->shortname = $argument;
            $course->description = $options['description'];
            $course->format = $options['format'];
            $course->visible = $options['visible'];
            $course->category = $options['category'];
            //either use API create_course
            $newcourse = create_course($course);

            echo $newcourse->id . "\n";
        }
    }
}
