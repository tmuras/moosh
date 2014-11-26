<?php
/**
 * Enable erol method(s) in a course.
 * moosh course-enrolleduser
 *      enrol-role courseid
 *
 * @copyright  2013 onwards Mirko Otto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Course;
use Moosh\MooshCommand;
use context_course;

class CourseEnrolledUser extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('enrolleduser', 'course');

        $this->addArgument('role_shortname');
        $this->addArgument('courseid');
        //$this->minArguments = 0;
        $this->maxArguments = 255;
    }

    public function execute()
    {
        cli_error('This command has been deprecated. Use user-list instead.');
    }
}
