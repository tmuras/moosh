<?php

/**
 * moosh - Moodle Shell
 * @copyright 2021 unistra {@link http://unistra.fr}
 * @author 2021 Céline Perves <cperves@unistra.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Category;
use Moosh\MooshCommand;

class CategoryMoveCoursesFromCategoryToAnother extends MooshCommand {

    public function __construct() {
        parent::__construct('move-courses-from-category-to-another', 'category');
        $this->addArgument('sourcecategory');
        $this->addArgument('destinationcategory');
        $this->minArguments = 2;
    }

    public function execute()
    {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/course/classes/category.php');
        require_once($CFG->dirroot.'/course/lib.php');
        // Check that category ids exists.
        try {
            $sourcecategory = \core_course_category::get($this->arguments[0]);
        } catch (moodle_exception $me){
            cli_error('source category does not exists');
        }
        try {
            $destinationcategory = \core_course_category::get($this->arguments[1]);
        } catch(moodle_exception $me){
            cli_error('destination category does not exists');
        }
        $courses = $DB->get_records('course', array('category'=> $sourcecategory->id));
        if(count($courses)==0){
            cli_writeln('no courses in source category');
        }
        foreach( $courses as $coursedata){
            // change category
            $coursedata->category = $destinationcategory->id;
            $msgobject = new \stdClass();
            $msgobject->courseid = $coursedata->id;
            $msgobject->course = $coursedata->shortname;
            $msgobject->sourcecategory = $sourcecategory->name;
            $msgobject->sourcecategoryid = $sourcecategory->id;
            $msgobject->destinationcategory = $destinationcategory->name;
            $msgobject->destinationcategoryid = $destinationcategory->id;
            update_course($coursedata);
            cli_writeln("course $msgobject->course ($msgobject->courseid) moved from category $msgobject->sourcecategory (id=$msgobject->sourcecategoryid) to category $msgobject->destinationcategory (id=$msgobject->destinationcategoryid)");
        }
    }
    protected function getArgumentsHelp() {
        $help = parent::getArgumentsHelp();
        $help .= "\n\n";
        $help .= "This command enable to move all courses included into a source cateogry into a destination category";
        return $help;
    }
}