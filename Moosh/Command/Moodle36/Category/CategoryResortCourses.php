<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace Moosh\Command\Moodle36\Category;

use Moosh\MooshCommand;
use core_course_category;

class CategoryResortCourses extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('resortcourses', 'category');

        $this->addArgument('category_id');
        $this->addArgument('sort');

        $this->maxArguments = 2;
    }

    public function execute()
    {
        global $DB;

        list($categoryid, $sort) = $this->arguments;
        if (!$cattosort = $DB->get_record('course_categories', array('id'=>$categoryid))) {
            cli_error("No category with id '$categoryid' found");
        }
        else {
            $this->resortcourses_category($cattosort, $sort);
        }
    }

    protected function resortcourses_category($category, $sort)
    {        
        $cat = core_course_category::get($category->id);
        $cat->resort_categories_cleanup(true);
        return $cat->resort_courses($sort);
    }

}
