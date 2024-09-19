<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2024 fireartist
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Course;

use Moosh\MooshCommand;
use core_course_category;

class CourseReorder extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('reorder', 'course');

        $this->addOption('up', 'Move course 1 position up within its category');
        $this->addOption('down', 'Move course 1 position down within its category');
        $this->addOption('top', 'Move course to the top of the course list within its category');
        $this->addOption('bottom', 'Move course to the bottom of the course list within its category');

        $this->addArgument('id');
        $this->minArguments = 1;
        $this->maxArguments = 1;
    }

    public function execute()
    {
        global $DB;

        $options = $this->expandedOptions;
        $counts = array_count_values($options);

        if ((!array_key_exists(1, $counts)) || $counts[1] != 1) {
            cli_error("Must provide 1 of the following: --up, --down, --top, --bottom");
        }

        list($courseid) = $this->arguments;
        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

        $courses = $DB->get_records(
            'course',
            [
                'category' => $course->category,
            ],
            'sortorder ASC',
            'id, sortorder'
        );
        # reset index to be consecutive from zero
        $courses = array_values($courses);

        if ($options['up']) {
            $this->move_up($courses, $courseid);
        }
        elseif ($options['top']) {
            $this->move_top($courses, $courseid);
        }
        elseif ($options['down']) {
            $this->move_down($courses, $courseid);
        }
        elseif ($options['bottom']) {
            $this->move_bottom($courses, $courseid);
        }

        # Create event
        $category = core_course_category::get($course->category);

        $event = \core\event\course_category_updated::create([
            'objectid' => $category->id,
            'context' => $category->get_context()
        ]);
        $event->trigger();

        # Clear cache
        core_course_category::resort_categories_cleanup(true);

        return true;
    }

    protected function move_up($courses, $id) {
        global $DB;

        if ($courses[0]->id == $id) {
            # already at top
            return;
        }

        # Set target to previous sortorder.
        # Set 1-before-target to target's sortorder.

        $prev = False;

        foreach ($courses as $index => &$item) {
            # Test for target before the +1 test, so we never go out of bounds.
            if ($item->id == $id) {
                $DB->set_field('course', 'sortorder', $prev, ['id' => $item->id]);
                # We're only swapping 2 items, so can stop now.
                break;
            }
            elseif ($courses[$index+1]->id == $id) {
                $prev = $item->sortorder;
                $DB->set_field('course', 'sortorder', $courses[$index+1]->sortorder, ['id' => $item->id]);
            }
        }
    }

    protected function move_top($courses, $id) {
        global $DB;

        if ($courses[0]->id == $id) {
            # already at top
            return;
        }

        # Set target to first sortorder.
        # Set all items before target to next item's sortorder.

        $first = $courses[0]->sortorder;

        foreach ($courses as $index => &$item) {
            if ($item->id == $id) {
                $DB->set_field('course', 'sortorder', $first, ['id' => $item->id]);
                # We don't edit any items *after* the target, so can stop now.
                break;
            }
            else {
                $DB->set_field('course', 'sortorder', $courses[$index+1]->sortorder, ['id' => $item->id]);
            }
        }
    }

    protected function move_down($courses, $id) {
        global $DB;

        if ($courses[count($courses)-1]->id == $id) {
            # already at bottom
            return;
        }

        # Set target to next it's sortorder.
        # Set 1-after-target to target's sortorder.

        $prev = False;

        foreach ($courses as $index => &$item) {
            if ($item->id == $id) {
                $prev = $item->sortorder;
                $DB->set_field('course', 'sortorder', $courses[$index+1]->sortorder, ['id' => $item->id]);
            }
            # Check it's not the first item in the list so we don't go out of bounds.
            # The first item in the list is never going to the the item *after* the target.
            elseif ($index != 0 && $courses[$index-1]->id == $id) {
                $DB->set_field('course', 'sortorder', $prev, ['id' => $item->id]);
                # We're only swapping 2 items, so can stop now.
                break;
            }
        }
    }

    protected function move_bottom($courses, $id) {
        global $DB;

        if ($courses[count($courses)-1]->id == $id) {
            # already at bottom
            return;
        }

        # Ignore items until the target courseid (prev not set).
        # Set target to last sortorder.
        # Set subsequent items to the previous sortorder.

        $prev = False;
        $last = $courses[count($courses)-1]->sortorder;

        foreach ($courses as $index => &$item) {
            if ($item->id == $id) {
                $prev = $item->sortorder;
                $DB->set_field('course', 'sortorder', $last, ['id' => $item->id]);
            }
            elseif ($prev) {
                $sortorder = $item->sortorder;
                $DB->set_field('course', 'sortorder', $prev, ['id' => $item->id]);
                $prev = $sortorder;
            }
        }
    }
}
