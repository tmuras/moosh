<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle26\Course;

use Moosh\MooshCommand;

class CourseList extends MooshCommand {
    public function __construct() {
        parent::__construct('list', 'course');

        $this->addArgument('search');
        $this->addOption('n|idnumber', 'show idnumber');
        $this->addOption('i|id', 'display id only column');
        $this->addOption('c|categorysearch:', 'courses from given category id only');
        $this->addOption('v|visible:', 'show all/yes/no visible', 'all');
        $this->addOption('e|empty:', 'show only empty courses: all/yes/no', 'all');
        $this->addOption('f|fields:', 'show only those fields in the output (comma separated)');
        $this->addOption('o|output:', 'output format: tab, csv', 'csv');

        $this->minArguments = 0;
        $this->maxArguments = 255;
    }

    public function execute() {
        global $CFG, $DB;


        require_once $CFG->dirroot . '/course/lib.php';
        require_once $CFG->dirroot . '/lib/coursecatlib.php';

        foreach ($this->arguments as $argument) {
            $this->expandOptionsManually(array($argument));
        }

        $this->expandOptions();

        $options = $this->expandedOptions;

        $params = NULL;
        $sql = "SELECT c.id,c.category,";
        if ($options['idnumber']) {
            $sql .= "c.idnumber,";
        }
        if ($options['empty'] == 'yes' || $options['empty'] == 'no') {
            $sql .= "COUNT(c.id) AS modules,";
        }
        $sql .= "c.shortname,c.fullname,c.visible FROM {course} c ";

        if ($options['empty'] == 'yes' || $options['empty'] == 'no') {
            $sql .= " LEFT JOIN {course_modules} m ON c.id=m.course ";
        }

        $sql .= "WHERE '1'='1' ";
        if ($options['categorysearch'] ) {
            $category = \coursecat::get($options['categorysearch']);

            $categories = $this->get_categories($category);

            list($where, $params) = $DB->get_in_or_equal(array_keys($categories));

            $sql .= "AND c.category $where";
        }

        // Glue arguments together, so end user does not need to provide single argument.
        if (isset($this->arguments[0]) && $this->arguments[0]) {
            $customwhere = implode(' ', $this->arguments);
            $sql .= " AND ($customwhere)";
        }
        if ($options['empty'] == 'yes') {
            $sql .= " GROUP BY c.id HAVING modules < 2";
        }
        if ($options['empty'] == 'no') {
            $sql .= " GROUP BY c.id HAVING modules > 1";
        }

        if($this->verbose) {
           cli_problem("SQL query run: $sql");
           cli_problem("Params:");
           cli_problem(var_export($params, true));
        }

        $courses = $DB->get_records_sql($sql, $params);

        // Filter out any that have any section information (summary)
        if ($options['empty'] == 'yes') {
            $sql = "SELECT COUNT(*) AS C FROM {course_sections} WHERE course = ? AND summary <> ''";
            foreach ($courses as $k => $course) {
                $sections = $DB->get_record_sql($sql, array($course->id));
                if ($sections->c > 0) {
                    unset($courses[$k]);
                }
            }
        }

        // @TODO: If empty == no, then add those that have no modules but some modification to sections

        $this->display_courses($courses);


    }

    private function get_parent($id, $parentname = NULL) {
        global $DB;

        if ($parentcategory = $DB->get_record('course_categories', array("id" => $id))) {
            if ($parentcategory->parent > 0) {
                $parentname .= $this->get_parent($parentcategory->parent, $parentname);
            } else {
                $parentname .= "Top";
            }
            $parentname .= "/" . $parentcategory->name;
        }
        return $parentname;

    }


    protected function get_categories(\coursecat $category) {
        static $categories = array();

        $categories[$category->id] = $category->name;

        foreach ($category->get_children() as $child) {
            $this->get_categories($child);
        }

        return $categories;
    }

    private function display_courses($courses) {
        $options = $this->expandedOptions;
        $fields = NULL;
        if ($options['fields']) {
            $fields = str_getcsv($options["fields"]);
            $fields = array_combine($fields, $fields);
        }

        $outputheader = $outputcontent = "";
        $doheader = 0;
        $header = array();
        $output = array();
        foreach ($courses as $course) {
            $line = array();
            if ($options['visible'] == 'yes' && $course->visible == 0) {
                continue;
            }
            if ($options['visible'] == 'no' && $course->visible == 1) {
                continue;
            }
            if ($options['id']) {
                echo $course->id . "\n";
                continue;
            }
            foreach ($course as $field => $value) {
                if ($fields && !isset($fields[$field])) {
                    continue;
                }
                if ($doheader == 0) {
                    $header[] = $field;
                    //$outputheader .= str_pad($field, 20);
                }
                if ($field == "category" && $value > 0) {
                    $value = $this->get_parent($value);
                } elseif ($field == "parent") {
                    $value = "Top";
                }
                $line[] = $value;
                //$outputcontent .= str_pad($value, 20);
            }
            $output[] = $line;
            //$outputcontent .= "\n";
            $doheader++;
        }
        if (!$options['id']) {
            array_unshift($output, $header);
            //$outputheader .= "\n";
            //echo $outputheader;
        }
        //echo $outputcontent;
        foreach ($output as $line) {
            if ($options['output'] == 'csv') {

                foreach ($line as $k => $l) {
                    $line[$k] = "\"$l\"";
                }
                echo implode(',', $line) . "\n";

            } elseif ($options['output'] == 'tab') {
                echo implode("\t", $line) . "\n";
            }
        }
    }
}
