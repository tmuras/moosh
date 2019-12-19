<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Category;
use Moosh\MooshCommand;

class CategoryMove extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('move', 'category');

        $this->addArgument('category_id');
        $this->addArgument('destination_category_id');

        $this->maxArguments = 2;
    }

    public function execute()
    {
        global $DB;
        list($categoryid, $destcategoryid) = $this->arguments;
        if (!$cattomove = $DB->get_record('course_categories', array('id'=>$categoryid))) {
            cli_error("No category with id '$categoryid' found");
        }
        if ($cattomove->parent != $destcategoryid) {
            if ($destcategoryid == 0) {
                $newparent = new \stdClass;
                $newparent->id = 0;
                $newparent->visible = 1;
            } else if (!$newparent = $DB->get_record('course_categories', array('id'=>$destcategoryid))) {
                cli_error("No destination category with id '$destcategoryid' found");
            }
            $this->move_category($cattomove, $newparent);
        }
    }

    protected function move_category($category, $destcategory)
    {
        global $CFG;
        require_once $CFG->dirroot . '/course/lib.php';
        move_category($category, $destcategory);
    }

    protected function getArgumentsHelp()
    {
        return parent::getArgumentsHelp()
            . "\n\t*  To make a category a top-level category, specify 0 for the destination_category_id.";
    }
}
