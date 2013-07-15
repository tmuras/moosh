<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Course;
use ___PHPSTORM_HELPERS\object;
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
        global $DB;

        $setting = trim($this->arguments[2]);
        $value = trim($this->arguments[3]);


        switch ($this->arguments[0]) {
            case 'course':
                self::setCourseSetting($this->arguments[1]/* courseid */,$setting,$value);
                break;
            case 'category':
                //get all courses in category (recursive)
                $courselist = get_courses($this->arguments[1]/* categoryid */,'','c.id');
                foreach ($courselist as $course) {
                    echo "debug: courseid=$course->id , $setting=$value\n";
                    self::setCourseSetting($course->id,$setting,$value);
                }
                break;
        }

    }

    private function setCourseSetting($courseid,$setting,$value) {
        if ($DB->set_field('course',$setting,$value,array('id'=>$courseid))) {
            echo "debug: Success (courseid={$courseid})\n";
            return true;
        } else {
            echo "debug: Fail (courseid={$courseid})\n";
            return false;
        }

    }

    protected function getArgumentsHelp()
    {
        return "\n\nARGUMENTS:\n\tcourse courseid setting value\n\tOr...\n\tcategory categoryid[all] setting value";
    }

}
