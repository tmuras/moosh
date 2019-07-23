<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace Moosh\Command\Moodle25\Category;

class CategoryCreate extends \Moosh\Command\Moodle23\Category\CategoryCreate
{
    protected function create_category($category)
    {
        global $CFG;
        require_once $CFG->libdir . '/coursecatlib.php';
        return \coursecat::create($category);
    }
}

