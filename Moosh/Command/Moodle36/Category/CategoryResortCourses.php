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
    public function __construct() {
        parent::__construct('resortcourses', 'category');

        $this->addArgument('category_id');
        $this->addArgument('sort');

        $this->addOption('r|recursive', 'recursively sort any subcategories');
        $this->addOption('n|nocatsort', 'do not sort categories, only courses');

        $this->maxArguments = 2;
    }


    protected function getArgumentsHelp() {
        $ret = "\n\nARGUMENTS:";
        $ret .= "\n\t";
        $ret .= implode(' ', $this->argumentNames);
        $ret .= "\n\n\tsort can be: fullname, shortname or idnumber";

        return $ret;
    }


    public function execute() {
        global $DB;

        $options = $this->expandedOptions;
        $nocatsort = $options['nocatsort'];

        list($categoryid, $sort) = $this->arguments;
        if (!$cattosort = $DB->get_record('course_categories', array('id' => $categoryid))) {
            cli_error("No category with id '$categoryid' found");
        } else {
            if (!$options['recursive']) {
                $this->resortcourses_category($cattosort, $sort);
            } else {
                $this->resortcategory_recursive($cattosort, $sort, $nocatsort);
            }
        }
    }

    protected function resortcategory_recursive($category, $sort, $nocatsort) {

        $categorieslist = core_course_category::make_categories_list('moodle/category:manage');
        $categoryids = array_keys($categorieslist);
        $categories = core_course_category::get_many($categoryids);
        unset($categorieslist);

        foreach ($categories as $cat) {

            // Don't sort categories if -n/--nocatsort given.
            if (!$nocatsort) {
                // Don't clean up here, we'll do it once we're all done.
                \core_course\management\helper::action_category_resort_subcategories($cat, 'name', false);
            }

            // Don't clean up here, we'll do it once we're all done.
            \core_course\management\helper::action_category_resort_courses($cat, $sort, false);
        }

        // Cleanup now that we're done.
        core_course_category::resort_categories_cleanup(true);
    }

    protected function resortcourses_category($category, $sort) {
        $cat = core_course_category::get($category->id);
        return $cat->resort_courses($sort, true);
    }
}
