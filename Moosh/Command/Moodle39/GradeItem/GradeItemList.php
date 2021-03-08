<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\GradeItem;
use Moosh\MooshCommand;

class GradeItemList extends MooshCommand {
    public function __construct() {
        parent::__construct('list', 'gradeitem');

        $this->addOption('i|id', 'display id column only');
        $this->addOption('h|hidden:', 'show all/yes/no if hidden', 'all');
        $this->addOption('l|locked:', 'show all/yes/no if locked', 'all');
        $this->addOption('e|empty:', 'show only scoreless grade items: all/yes/no if empty', 'all');
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
        $sql = "SELECT * ";
        $sql .= "FROM {grade_items} i ";
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

        $gradeitems = $DB->get_records_sql($sql, $params);

        $this->display($gradeitems);

    }

    private function has_grade($item_id) {
        global $DB;
        if ($records = $DB->get_records('grade_grades', array("itemid" => $item_id))) {
            foreach ($records as $record) {
                if (isset( $record->rawgrade ) ) {
                    return true;
                }
            }
        }
        else { return false; }
    }

    private function get_category_path($id, $parentname = NULL) {
        global $DB;

        if ($parentcategory = $DB->get_record('grade_categories', array("id" => $id))) {
            if ($parentcategory->parent > 0) {
                $parentname .= $this->get_category_path($parentcategory->parent, $parentname);
            } else {
                $parentname .= "Top";
            }
            $parentname .= "/" . $parentcategory->fullname;
        }
        return $parentname;

    }

    protected function display($gradeitems, $json = false, $humanreadable = true) {

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
        foreach ($gradeitems as $item) {
            $line = array();
            if ($options['hidden'] == 'yes' && $item->hidden == 0) {
                continue;
            }
            if ($options['hidden'] == 'no' && $item->hidden != 0) {
                continue;
            }
            if ($options['locked'] == 'yes' && $item->locked == 0) {
                continue;
            }
            if ($options['locked'] == 'no' && $item->locked != 0) {
                continue;
            }
            $id = $item->id;
            if ($options['empty'] == 'yes' && $this->has_grade($id) == true) {
                continue;
            }
            if ($options['empty'] == 'no' && $this->has_grade($id) == false) {
                continue;
            }
            if ($options['id']) {
                echo $id . "\n";
                continue;
            }
            foreach ($item as $field => $value) {
                if ($fields && !isset($fields[$field])) {
                    continue;
                }
                if ($doheader == 0) {
                    $header[] = $field;
                    //$outputheader .= str_pad($field, 20);
                }
                if ($field == "categoryid" && $value > 0) {
                    $value = $this->get_category_path($value);
                } elseif ($field == "categoryid") {
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
