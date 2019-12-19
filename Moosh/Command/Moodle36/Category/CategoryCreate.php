<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace Moosh\Command\Moodle36\Category;

class CategoryCreate extends \Moosh\Command\Moodle25\Category\CategoryCreate
{
    protected function create_category($category)
    {
        return \core_course_category::create($category);
    }
}

