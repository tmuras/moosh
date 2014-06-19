<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright 2012 onwards Tomasz Muras
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace Moosh\Command\Moodle19\Category;
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

        $categoryid = intval($this->arguments[0]);
        $category = get_record('course_categories', 'id',$categoryid);
        if ($categoryid && !$category) {
            cli_error("Wrong category '$categoryid'");
        } elseif (!$categoryid) {
            $category = NULL;
        }

        $categories = get_child_categories($categoryid);

        //add top lever category as well
        if ($category) {
            $category->categories = $categories;
            $categories = array($category);
        }

        echo "<categories>\n";
        $this->categories2xml($categories);
        echo "</categories>\n";


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
            $category->categories = get_child_categories($category->id);

            echo "<category oldid='{$category->id}' ";

            $name = str_replace(
                array("&",     "<",    ">",    '"',      "'"),
                array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;"),
                $category->name
            );

            echo "name='$name'>";

            foreach ($category->categories as $categories2) {
                $this->categories2xml(array($categories2));
            }
            echo "</category>\n";
        }
    }

}