<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright 2012 onwards Tomasz Muras
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace Moosh\Command\Moodle39\Category;
use Moosh\MooshCommand;

class CategoryExport extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('export', 'category');

        $this->addArgument('category_id');

    }

    public function execute()
    {
        global $CFG;

        require_once $CFG->dirroot . '/course/lib.php';
        require_once $CFG->libdir . '/coursecatlib.php';

        $categoryid = intval($this->arguments[0]);

        $categories = $this->get_category_tree($categoryid);
        echo "<categories>\n";
        $this->categories2xml(array($categories));
        echo "</categories>\n";
    }

    private function get_category_tree($id) {
        global $DB;

        $category = $DB->get_record('course_categories', array('id' => $id));
        if ($id && !$category) {
            cli_error("Wrong category '$id'");
        } elseif (!$id) {
            $category = NULL;
        }

        $parentcategory = \coursecat::get($id);
        if ($parentcategory->has_children()) {
            $parentschildren = $parentcategory->get_children();
            foreach($parentschildren as $singlecategory) {
                if ($singlecategory->has_children()) {
                    $childcategories = $this->get_category_tree($singlecategory->id);
                    $category->categories[] = $childcategories;
                } else {
                // coursecat variables are protected, need to get data from db
                    $singlecategory = $DB->get_record('course_categories', array('id' => $singlecategory->id));
                    $category->categories[] = $singlecategory;
                }
            }
        }

        return $category;
    }

    private function categories2xml($categories)
    {
        foreach ($categories as $category) {
            if (!is_object($category)) {
                echo "not an object\n";
                var_dump($category);
                debug_print_backtrace();
                die();
            }

            if(isset($category->id)) {
                echo "<category oldid='{$category->id}' ";
            }
            if (isset($category->idnumber) && !empty($category->idnumber)) {
                echo "idnumber='{$category->idnumber}' ";
            }

            if(isset($category->id)) {
                $name = str_replace(
                    array("&", "<", ">", '"', "'"),
                    array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;"),
                    $category->name
                );
                echo "name='$name'>";
            }

            if (property_exists($category, 'categories')) {
                foreach($category->categories as $categories2) {
                    $this->categories2xml(array($categories2));
                }
            }
            if(isset($category->id)) {
                echo "</category>\n";
            }
        }
    }
}
