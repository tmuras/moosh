<?php

/**
 * moosh - Moodle Shell
 *
 * List all enrolment instances for a given course.
 *
 * @copyright  2025 University of Siegen
 * @author     2025 Timo Hardebusch
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Course;

use Moosh\MooshCommand;

class CourseEnrolList extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('enrol-list', 'course');

        $this->addArgument('courseid');

        $this->maxArguments = 255;
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/course/lib.php';

        foreach ($this->arguments as $courseid) {
            if ($courseid == 0) {
                cli_error('You must enter a valid courseid.');
            }

            // Get the course record.
            $course = $DB->get_record('course', ['id' => $courseid]);
            if (!$course) {
                cli_error("Course $courseid not found.");
            }

            cli_writeln("Enrolment methods for course id {$courseid}:");

            // Retrieve enrolment instances for the course.
            $instances = enrol_get_instances($course->id, false);

            // If no enrolment methods are found, print a message.
            if (empty($instances)) {
                cli_writeln("  No enrolment methods found.");
                continue;
            }

            // Loop through each enrolment instance and display its details.
            foreach ($instances as $instance) {
                $statusText = ($instance->status == ENROL_INSTANCE_ENABLED) ? "enabled" : "disabled";
                cli_writeln("  $instance->id : $instance->name - $instance->enrol - $statusText");
            }
        }
    }

    protected function getArgumentsHelp()
    {
        $help = parent::getArgumentsHelp();
        $help .= "\n\n";
        $help .= "List enrolment methods for a given course.\n";
        $help .= "Each enrolment method is displayed with its instance id, name, type and status (enabled/disabled).";
        return $help;
    }
}
