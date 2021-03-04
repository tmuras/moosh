<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle35\Category;
use Moosh\MooshCommand;

class CategoryList extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('list', 'category');

        $this->addArgument('search');
        $this->addOption('o|output:', 'output format: tab, csv, min-width-15 (space padded)', 'min-width-15');

        $this->minArguments = 0;
        $this->maxArguments = 255;
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/course/lib.php';

        if(!count($this->arguments)) {
            $this->arguments = array('');
        }

        foreach ($this->arguments as $argument) {
            $this->expandOptionsManually(array($argument));
            $options = $this->expandedOptions;

            $sql = "SELECT id,name,idnumber,description,parent,visible FROM {course_categories} WHERE " . $DB->sql_like('name', ':catname', false);
            $categories = $DB->get_records_sql($sql, array('catname' => "%$argument%"));
        }

        $this->display_categories($categories);
    }

    private function display_categories($categories) {
        $options = $this->expandedOptions;

        $outputheader = $outputcontent = "";
        $doheader = 0;
        $header = array();
        $output = array();
        foreach ($categories as $category) {
            $line = array();
            foreach ($category as $field => $value ) {
                if ($doheader == 0) {
                    $header[] = $field;
                    // $outputheader .= str_pad ($field, 15);
                }
                if ($field == "parent" && $value > 0 ) {
                    $value = $this->get_parent($value);
                } elseif($field == "parent") {
                    $value = "Top";
                }
                $line[] = $value;
                // $outputcontent.= str_pad ($value, 15);
            }
            $output[] = $line;
            // $outputcontent .= "\n";
            $doheader++;
        }

        array_unshift($output, $header);
        // echo $outputheader . "\n";
        // echo $outputcontent;

        foreach ($output as $line) {
            if ($options['output'] == 'min-width-15') {

                foreach ($line as $k => $l) {
                    $line[$k] = str_pad ($l, 15);
                }
                echo implode('', $line) . "\n";

            } elseif ($options['output'] == 'csv') {

                foreach ($line as $k => $l) {
                    $line[$k] = "\"$l\"";
                }
                echo implode(',', $line) . "\n";

            } elseif ($options['output'] == 'tab') {
                echo implode("\t", $line) . "\n";
            }
        }
    }

    private function get_parent($id,$parentname = NULL) {
        global $DB;

        if ($parentcategory = $DB->get_record('course_categories',array("id"=>$id))) {
            if ($parentcategory->parent > 0 ) {
                $parentname .= $this->get_parent($parentcategory->parent,$parentname);
            } else {
                $parentname .= "Top";
            }
            $parentname .= "/" . $parentcategory->name;
        }
        return $parentname;

    }
}
