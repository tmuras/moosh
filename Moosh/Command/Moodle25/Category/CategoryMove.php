<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace Moosh\Command\Moodle25\Category;
use coursecat;

class CategoryMove extends \Moosh\Command\Moodle23\Category\CategoryMove
{
    protected function move_category($category, $destcategory)
    {
        global $CFG;
        require_once $CFG->libdir . '/coursecatlib.php';
        return coursecat::get($category->id)->change_parent($destcategory->id);
    }
}
