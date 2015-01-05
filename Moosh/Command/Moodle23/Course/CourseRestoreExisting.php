<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Course;
use Moosh\MooshCommand;
use restore_controller;
use restore_dbops;
use backup;

class CourseRestoreExisting extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('restoreexisting', 'course');

        $this->addArgument('backup_file');
        $this->addArgument('course_id');
    }

    public function execute()
    {
        cli_error('This command has been deprecated. Use course-restore -e instead.');
    }
}
