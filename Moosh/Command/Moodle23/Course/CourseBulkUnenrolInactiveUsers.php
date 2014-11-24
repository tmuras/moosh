<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2014 Manolescu Dorel - based on Matteo Mosangini CourseUnerol
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Course;

use Moosh\MooshCommand;
use context_course;
use course_enrolment_manager;

class CourseBulkUnenrolInactiveUsers extends MooshCommand {

    public function __construct() {
        parent::__construct('bulk_unenrol_inactive_users', 'course');

        $this->addOption('r|role:', 'role name like- manager, editingteacher, student...');

        $this->addArgument('courseid');
    }

    public function execute() {
        cli_error('This command has been deprecated. Use user-list & course-unenrol instead.');
    }
}
