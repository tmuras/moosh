<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2017 onwards, Marty Gilbert
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle30\Category;
use Moosh\MooshCommand;

class CategoryDelete extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('delete', 'category');
        $this->addArgument('categoryid');
    }

    public function execute()
    {
		global $CFG, $DB;

		require_once($CFG->libdir.'/coursecatlib.php');
		
		$catid = $this->arguments[0];

		//Make sure the category exists
		if ($DB->record_exists('course_categories', array('id' => $catid))) {

			$category = \coursecat::get($catid);

			//do a recursive delete of all courses and subcats
			$courses = $category->delete_full(false);

			//print out all of the deleted courses, if 'verbose' is set
			if ($this->verbose) {
				foreach ($courses as $course){
					echo "Deleted course: $course->shortname\n";
				}
			}

			echo 'Deleted '.sizeof($courses).' courses'."\n";
		} else {
			echo 'Category id '.$catid.' does not exist.'."\n";
		}
    }
}
