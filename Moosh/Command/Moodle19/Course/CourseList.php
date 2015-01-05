<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle19\Course;

use Moosh\MooshCommand;

class CourseList extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('list', 'course');

        $this->addArgument('categoryid');

    }

    public function execute()
    {
        $categoryid = $this->arguments[0];
        $courses = $this->get_courses_by_cat($categoryid);
        foreach ($courses as $course) {
            echo "{$course->id},{$course->category},{$course->shortname},{$course->fullname}\n";
        }

    }


    private function get_courses_by_cat($catid)
    {
        global $CFG;

        $courses = array();
        //Get all categories id
        if ($cat = get_record('course_categories', 'id', $catid)) {
            $catlist = "$catid,";
            $sql = "SELECT cc.id FROM {$CFG->prefix}course_categories cc WHERE cc.path like '{$cat->path}/%'";
            if ($catids = get_records_sql($sql)) {
                foreach ($catids as $value) {
                    $catlist .= $value->id . ',';
                }
            }
            if (!empty($catlist)) {
                $catlist = rtrim($catlist, ',');
                $sql = "SELECT * FROM {$CFG->prefix}course c WHERE c.category IN ($catlist)";
                $courses = get_records_sql($sql);
            }
        }
        return $courses;
    }

}

