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

class CategorySortorderToFirst extends MooshCommand {
    public function __construct() {
        parent::__construct('sortordertofirst', 'category');

        $this->addArgument('category_id');
        $this->addArgument('target');

        $this->minArguments = 2;
        $this->maxArguments = 2;
    }

    protected function getArgumentsHelp() {
        $ret = "\n\nARGUMENTS:";
        $ret .= "\n\t";
        $ret .= implode(' ', $this->argumentNames);
        $ret .= "\n\n\ttarget can be: first or last";

        return $ret;
    }

    public function execute() {
        global $DB;

        list($categoryid, $target) = $this->arguments;

        fix_course_sortorder();

        if (!$category = core_course_category::get($categoryid)) {
            cli_error("No category with id '$categoryid' found");
        } else if (!in_array($target, ['first', 'last'])) {
            cli_error("Targer has to be first or last, you gave $target");
        } else {
            $previous = $category->sortorder;

            $params = ['parent' => $category->parent, 'sortorder' => $previous];
            if ($target == 'first') {
                $select = 'parent = :parent AND sortorder < :sortorder';
                $sort = 'sortorder DESC';
            } else {
                $select = 'parent = :parent AND id > :sortorder';
                $sort = 'sortorder ASC';
            }
            $categories = $DB->get_records_select('course_categories', $select, $params, $sort, 'id, sortorder');

            foreach ($categories as $cat) {
                $DB->set_field('course_categories', 'sortorder', $previous, ['id' => $cat->id]);
                $previous = $cat->sortorder;
            }
            $DB->set_field('course_categories', 'sortorder', $previous, ['id' => $categoryid]);

            $event = \core\event\course_category_updated::create([
                'objectid' => $categoryid,
                'context' => $category->get_context()
            ]);
            $event->trigger();

            core_course_category::resort_categories_cleanup(true);

            return true;

        }
        return false;
    }
}