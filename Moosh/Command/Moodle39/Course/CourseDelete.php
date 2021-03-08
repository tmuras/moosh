<?php

/**
 * Delete course by id.
 *
 * moosh course-delete [<id1> <id2> ...]
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Course;

use Moosh\MooshCommand;

class CourseDelete extends MooshCommand
{

    public function __construct()
    {
        parent::__construct('delete', 'course');

        $this->addArgument('id');
        $this->maxArguments = 255;
    }

    public function execute()
    {
        global $DB;

        foreach ($this->arguments as $argument) {
            try {
                $course = $DB->get_record('course', array('id' => $argument));
            } catch (Exception $e) {
                print get_class($e) . " thrown within the exception handler. Message: " . $e->getMessage() . " on line " . $e->getLine();
            }

            if ($course instanceof \stdClass) {
                try {
                    print "About to delete course id={$course->id}; shortname={$course->shortname}; category id={$course->category}";
                    delete_course($course);
                } catch (Exception $e) {
                    print get_class($e) . " thrown for courseid={$course->id} within the exception handler. Message: " . $e->getMessage() . " on line " . $e->getLine();
                }
            } else {
                print "Course not found\n";
            }
        }
        fix_course_sortorder();
    }

}
