<?php
/**
 *
 */
class CourseCreate extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('create', 'course');

        $this->addOption('c|category:');
        $this->addOption('f|fullname:');
        $this->addOption('d|description:');
        $this->addOption('F|format:');
        $this->addOption('v|visible:');

        $this->addRequiredArgument('shortname');
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/course/lib.php';

        $options = $this->expandedOptions;
        $arguments = $this->arguments;

        $course = new stdClass();
        $course->fullname = $options['fullname'];
        $course->shortname = $arguments[0];
        $course->description = $options['description'];
        $course->format = $options['format'];
        $course->visible = $options['visible'];
        $course->category = $options['category'];
        //either use API create_course
        $newcourse = create_course($course);

        //or direct insert into DB

        echo $newcourse->id . "\n";
    }
}
