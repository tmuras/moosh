<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace Moosh\Command\Moodle39\Category;

use Moosh\MooshCommand;
use core_course_category;

class CategoryResortCourses extends MooshCommand {
    public function __construct() {
        parent::__construct('resortcourses', 'category');

        $this->addArgument('category_id');
        $this->addArgument('sort');

        $this->addOption('r|recursive', 'recursively sort any subcategories');
        $this->addOption('n|nocatsort', 'do not sort categories, only courses');

        $this->minArguments = 2;
        $this->maxArguments = 4;
    }

    protected function getArgumentsHelp() {
        $ret = "\n\nARGUMENTS:";
        $ret .= "\n\t";
        $ret .= implode(' ', $this->argumentNames);
        $ret .= "\n\n\tTo resort the top category, specify 0 for the category_id";
        $ret .= "\n\n\tSort can be: fullname, shortname or idnumber";

        return $ret;
    }

    public function execute() {
        global $DB;

        list($categoryid, $sort) = $this->arguments;

        $options = $this->expandedOptions;

        $cattosort = core_course_category::get($categoryid);

        if (!$cattosort && $categoryid != 0) {
            cli_error("No category with id '$categoryid' found");
        } else {
            if ($options['recursive']) {
                $this->resort_recursive($cattosort, $sort, $options['nocatsort']);
            } else {
                $this->resort($cattosort, $sort, $options['nocatsort']);
            }

            core_course_category::resort_categories_cleanup(true);
        }
    }

    protected function resort($category, $sort, $nocatsort) {
        if (!$nocatsort) {
            $category->resort_subcategories($sort, false);
        }
        $category->resort_courses($sort, false);
    }

    protected function resort_recursive($category, $sort, $nocatsort) {
        $this->resort($category, $sort, $nocatsort);
        foreach ($category->get_children() as $cat) {
            $this->resort_recursive($cat, $sort, $nocatsort);
        }
    }
}