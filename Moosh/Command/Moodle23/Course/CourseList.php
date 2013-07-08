<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Course;
use Moosh\MooshCommand;

class CourseList extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('list', 'course');

        $this->addArgument('search');
        $this->addOption('i|idnumber', 'show idnumber');

        $this->maxArguments = 255;
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/course/lib.php';

        foreach ($this->arguments as $argument) {
            $this->expandOptionsManually(array($argument));
            $options = $this->expandedOptions;

            $search = mysql_real_escape_string($argument);
            $sql = "SELECT id,category,";
            if($options['idnumber']){
                $sql .= "idnumber,";
            }
            $sql .= "shortname,fullname
                      FROM {course}
                      WHERE (" . $DB->sql_like('shortname', ':shortname', false) . " OR " . $DB->sql_like('fullname', ':fullname', false) . ")";
            $courses = $DB->get_records_sql($sql, array('shortname' => "%$search%", 'fullname' => "%$search%"));

            $outputheader = $outputcontent = "";
            $doheader = 0;
            foreach ($courses as $course) {
                foreach ($course as $field => $value ) {
                    if ($doheader == 0) {
                        $outputheader .= str_pad ($field, 15);
                    }
                    if ($field == "category" && $value > 0 ) {
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

