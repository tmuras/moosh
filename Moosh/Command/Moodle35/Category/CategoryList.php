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

            $sql = "SELECT id,name,idnumber,description,parent FROM {course_categories} WHERE " . $DB->sql_like('name', ':catname', false);
            $categories = $DB->get_records_sql($sql, array('catname' => "%$argument%"));

            $outputheader = $outputcontent = "";
            $doheader = 0;
            foreach ($categories as $category) {
                foreach ($category as $field => $value ) {
                    if ($doheader == 0) {
                        $outputheader .= str_pad ($field, 15);
                    }
                    if ($field == "parent" && $value > 0 ) {
                        $value = $this->get_parent($value);
                    } elseif($field == "parent") {
                        $value = "Top";
                    }

                    $outputcontent.= str_pad ($value, 15);
                }
                $outputcontent .= "\n";
                $doheader++;
            }
            echo $outputheader . "\n";
            echo $outputcontent;

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

