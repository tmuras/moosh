<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Course;
use Moosh\MooshCommand;

class CourseConfigSet extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('config-set', 'course');

        $this->addArgument('mode');
        $this->addArgument('id');
        $this->addArgument('setting');
        $this->addArgument('value');
    }

    public function execute()
    {
        $setting = trim($this->arguments[2]);
        $value = trim($this->arguments[3]);


        switch ($this->arguments[0]) {
            case 'course':
                if(!self::setCourseSetting($this->arguments[1]/* courseid */,$setting,$value)){
                	// the setting was not applied, exit with a non-zero exit code
                	cli_error('');
                }
                break;
            case 'category':
                //get all courses in category (recursive)
                $courselist = get_courses($this->arguments[1]/* categoryid */,'','c.id');
                $succeeded = 0;
                $failed = 0;
                foreach ($courselist as $course) {
                    if(self::setCourseSetting($course->id,$setting,$value)){
                        $succeeded++;
                    }else{
                        $failed++;
                    }
                }
                if($failed == 0){
                    echo "OK - successfully modified $succeeded courses\n";
                }else{
                    echo "WARNING - failed to mofify $failed courses (successfully modified $succeeded)\n";
                }
                break;
        }

    }

    private function setCourseSetting($courseid,$setting,$value) {
        
        global $DB;
        
        if ($DB->set_field('course',$setting,$value,array('id'=>$courseid))) {
            echo "OK - Set $setting='$value' (courseid={$courseid})\n";
            return true;
        } else {
            echo "ERROR - failed to set $setting='$value' (courseid={$courseid})\n";
            return false;
        }

    }

    protected function getArgumentsHelp()
    {
        return "\n\nARGUMENTS:\n\tcourse courseid setting value\n\tOr...\n\tcategory categoryid[all] setting value";
    }

}
