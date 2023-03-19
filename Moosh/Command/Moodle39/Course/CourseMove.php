<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Course;

use Moosh\MooshCommand;

class CourseMove extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('move', 'course');

        $this->addArgument('course_ids');
        $this->addArgument('destination_category_id');

        $this->maxArguments = 2;
    }

    public function execute()
    {
        global $DB;

        $courseIdsArg = $this->arguments[0];
        $categoryId = $this->arguments[1];

        if (!$DB->record_exists('course_categories', array('id' => $categoryId))) {
            cli_error("Category with id $categoryId not found");
        }

        $category = \core_course_category::get($categoryId);

        $courseIds = array_map('intval', explode(',', $courseIdsArg));
        foreach ($courseIds as $courseId) {
            if (!$DB->record_exists('course', array('id' => $courseId))) {
                cli_error("Course with id $courseId not found");
            }
        }

        \core_course\management\helper::move_courses_into_category($category, $courseIds);
    }

    protected function getArgumentsHelp()
    {
        return parent::getArgumentsHelp()
            . "\n\t*  You can specify a comma separated list of course ids as the first argument.";
    }
}
