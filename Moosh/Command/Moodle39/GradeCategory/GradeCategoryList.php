<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\GradeCategory;
use Moosh\MooshCommand;

class GradeCategoryList extends MooshCommand {
    public function __construct() {
        parent::__construct('list', 'gradecategory');

        $this->addOption('i|id', 'display id column only');
        $this->addOption('h|hidden:', 'show all/yes/no if hidden', 'all');
        $this->addOption('e|empty:', 'show only empty grade categories: all/yes/no if empty', 'all');
        $this->addOption('f|fields:', 'show only those fields in the output (comma separated)');
        $this->addOption('o|output:', 'output format: tab, csv', 'csv');

        $this->addArgument('search');

        $this->minArguments = 0;
        $this->maxArguments = 255;
    }

    public function execute() {
        global $CFG, $DB;

        foreach ($this->arguments as $argument) {
            $this->expandOptionsManually(array($argument));
        }

        $this->expandOptions();

        $options = $this->expandedOptions;

        $params = NULL;
        $sql = "SELECT c.id,c.courseid,";
        $sql .= "c.parent,c.fullname, c.aggregation, c.keephigh, c.droplow,";
        $sql .= "c.aggregateonlygraded, c.aggregateoutcomes, c.timecreated,";
        $sql .= "c.timemodified,c.hidden FROM {grade_categories} c ";
        $sql .= "WHERE '1'='1' ";

        // Glue arguments together, so end user does not need to provide single argument.
        if (isset($this->arguments[0]) && $this->arguments[0]) {
            $customwhere = implode(' ', $this->arguments);
            $sql .= " AND ($customwhere)";
        }

        if($this->verbose) {
           cli_problem("SQL query run: $sql");
           cli_problem("Params:");
           cli_problem(var_export($params, true));
        }

        $gradecats = $DB->get_records_sql($sql, $params);

        $this->display($gradecats);

    }

    private function has_item($category_id) {
        global $DB;

        if ($item = $DB->get_records('grade_items', array("categoryid" => $category_id))) {
            return true;
        }
        elseif ($children = $DB->get_records('grade_categories', array("parent" => $category_id))) {
            foreach ($children as $child) {
                return $this->has_item($child->id);
            }
        }
        else { return false; }
    }

    private function get_parent($id, $parentname = NULL) {
        global $DB;

        if ($parentcategory = $DB->get_record('grade_categories', array("id" => $id))) {
            if ($parentcategory->parent > 0) {
                $parentname .= $this->get_parent($parentcategory->parent, $parentname);
            } else {
                $parentname .= "Top";
            }
            $parentname .= "/" . $parentcategory->fullname;
        }
        return $parentname;

    }

    protected function display($gradecats, $json = false, $humanreadable = true) {

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
        foreach ($gradecats as $category) {
            $line = array();
            if ($options['hidden'] == 'yes' && $category->hidden == 0) {
                continue;
            }
            if ($options['hidden'] == 'no' && $category->hidden != 0) {
                continue;
            }
            $id = $category->id;
            if ($options['empty'] == 'yes' && $this->has_item($id) == true) {
                continue;
            }
            if ($options['empty'] == 'no' && $this->has_item($id) == false) {
                continue;
            }
            if ($options['id']) {
                echo $category->id . "\n";
                continue;
            }
            foreach ($category as $field => $value) {
                if ($fields && !isset($fields[$field])) {
                    continue;
                }
                if ($doheader == 0) {
                    $header[] = $field;
                    //$outputheader .= str_pad($field, 20);
                }
                if ($field == "parent" && $value > 0) {
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
