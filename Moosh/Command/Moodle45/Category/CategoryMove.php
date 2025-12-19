<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle45\Category;
use Moosh\MooshCommand;
use core_course_category;

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
	
        list($categoryid, $destcategoryid) = $this->arguments;
	try {
	    $cattomove = core_course_category::get($categoryid);
	} catch (\moodle_exception $e){
            cli_error("No category with id '$categoryid' found");
        }
        if ($cattomove->parent != $destcategoryid) {
	    try {
	        $newparent = core_course_category::get($destcategoryid);
	    } catch (\moodle_exception){
                cli_error("No category with id '$destcategoryid' found");
            }
            $cattomove->change_parent($destcategoryid);
        }
    }

    protected function getArgumentsHelp()
    {
        return parent::getArgumentsHelp()
            . "\n\t*  To make a category a top-level category, specify 0 for the destination_category_id.";
    }
}
