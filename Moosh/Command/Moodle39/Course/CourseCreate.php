<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Course;
use http\Exception\InvalidArgumentException;
use Moosh\MooshCommand;

/**
 * Creates Moodle course(es).
 * {@code moosh hp5-core-contenttypes-export [-n, --name] [-f, --fullname] [-d, --description] [-F, --format] [-n, --numsections] [-i, --idnumber] [-v, --visible] [-r, --reuse] <shortname>}
 *
 * @example 1: Creates new course "my-test-course" with default moodle values.
 * moosh course-create my-test-course
 *
 * @example 2: Creates 10 courses ("my-test-course1", "my-test-course2" etc.) with default field
 * values using bash/zim expansion.
 * moosh course-create my-test-course{1..10}
 *
 * @example 3: Creates test course with format "site" and description "Awesome course". Another field set default.
 * moosh course-create -f site -d "Awesome course" my-test-course
 *
 * @example 4: Creates 100 courses, skips if any of them exist.
 * moosh course-create -r my-test-course{1..100}
 *
 * @package Moosh\Command\Moodle39\Course
 * @author Jakub Kleban <jakub.kleban2000@gmail.com>
 * @author Michal Chruscielski <michalch775@gmail.com>
 */
class CourseCreate extends MooshCommand {
    public function __construct() {
        parent::__construct('create', 'course');

        $this->addOption('c|category:', 'category id');
        $this->addOption('f|fullname:', 'full name');
        $this->addOption('d|description:', 'description');
        $this->addOption('F|format:', 'format (e.g. one of site, weeks, topics, etc.)');
        $this->addOption('n|numsections:', 'number of sections (i.e. of weeks, topics, etc.)');
        $this->addOption('i|idnumber:', 'id number.');
        $this->addOption('v|visible:', 'visible (y or n, by default creates visible)');
        $this->addOption('r|reuse', 'do not create new course if it a matching one already exists', false);

        $this->addArgument('shortname');

        $this->maxArguments = 255;
    }

    public function execute() {
        global $CFG;
        require_once $CFG->dirroot . '/course/lib.php';

        $commandOptionsKeys = array('category', 'fullname', 'description', 'format', 'numsections', 'idnumber');

        // get course config as stdClass
        $courseConfig = get_config("moodlecourse");

        if($this->verbose) {
            $count = count($this->arguments);
            mtrace("Attempting creation of $count courses.");
        }

        // runs for every argument (course)
        foreach ($this->arguments as $shortName) {
            // we want to extend options for every course separately in order to specify our shortName
            $this->expandOptionsManually(array($shortName));
            $options = $this->expandedOptions;
            $course = new \stdClass();

            if($this->verbose) {
                mtrace("Creating course $shortName");
            }

            // Moodle loads default options from config and sets them, so we do.
            foreach($courseConfig as $key => $value){
                // Skipping if key would be overwritten
                if(isset($options[$key]) && $options[$key] !== "") {
                    continue;
                }

                $course->$key = $value;

                if ($this->verbose) {
                    mtrace("Setting default $key: $value.");
                }
            }

            // Shortname is always defined as argument
            $course->shortname = $shortName;

            // Setting user chosen options
            foreach ($commandOptionsKeys as $optionKey) {
                // empty options are empty strings (not nulls) if they are not given
                if ($options[$optionKey] === "") {
                    continue;
                }

                $course->$optionKey = $options[$optionKey];

                if($this->verbose) {
                    mtrace("Set $optionKey to value: $options[$optionKey]");
                }
            }

            // setting course start date time to now
            $startDateTime=time();
            $course->startdate = $startDateTime;

            if($this->verbose) {
                mtrace("Set startdate: $startDateTime (now)");
            }

            if(isset($courseConfig->courseduration)) {
                $course->enddate = $startDateTime + $courseConfig->courseduration;

                if($this->verbose) {
                    $endDate = $course->enddate;
                    mtrace("Set enddte: $endDate");
                }
            }

            try {
                $courseVisible = $this->formatCourseVisible($options['visible']);
            } catch (\InvalidArgumentException $e) {
                cli_error("-visible option must be equal 'n' or 'y'");
                // cli_error breaks execution, but exit() suppresses IDE warnings.
                exit();
            }

            // overwriting only when value given
            if($courseVisible !== null) {
                $course->visible = $courseVisible;

                if($this->verbose) {
                    mtrace("Set visible: $courseVisible");
                }
            }

            // required, setting empty/default
            $course->summary = '';
            $course->summaryformat = FORMAT_HTML;
            $course->enablecompletion = true;

            if ($options['reuse'] && $existing = $this->findCourse($course)) {
                $new_course = $existing;

                $name = $new_course->shortname;
                $id = $new_course->id;
                print("Course $name with id: $id exists. Skipping.\n");
            } else {
                $new_course = create_course($course);

                $name = $new_course->shortname;
                $id = $new_course->id;
                print("Added course $name with id: $id\n");
            }
        }
    }

    /**
     * Finds first course matching given argument
     * @param \stdClass $course course which we're looking for
     * @return \stdClass|null found course or null
     */
    public function findCourse($course) {
        global $DB;

        // Shortname must be unique so it's our only parameter
        $params = array('shortname' => $course->shortname);

        $courses = $DB->get_records('course', $params);

        // want to be exception safe
        if (count($courses) >= 1) {
            return array_pop($courses);
        } else {
            return null;
        }
    }

    /**
     * returns course visible value or null if empty argument given. if `$visible` is not equal
     * to "0", "1", "y", "n", "yes" or "no" throw InvalidArgumentException.
     * @param string $visible argument value
     * @return int|null 0, 1 is visible or null if can't determine
     */
    public function formatCourseVisible($visible) {
        $visible = strtolower($visible);

        if($visible === 'n' || $visible === 'no' || $visible === "0" ){
            return 0;
        } else if($visible === 'y' || $visible === 'yes' || $visible === "1"){
           return 1;
        } else if(strlen($visible) > 0) {
            // invalid value given
            throw new InvalidArgumentException("Visible must be equal n or y.");
        } else {
            // no argument given, we returns null
            return null;
        }
    }
}
