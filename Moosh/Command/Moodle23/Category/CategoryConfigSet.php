<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Category;
use Moosh\MooshCommand;

class CategoryConfigSet extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('config-set', 'category');

        $this->addArgument('id');
        $this->addArgument('setting');
        $this->addArgument('value');
    }

    public function execute()
    {
        global $DB;
        
        $catid = $this->arguments[0];
        $setting = trim($this->arguments[1]);
        $value = trim($this->arguments[2]);

        
        if ($DB->set_field('course_categories', $setting, $value, array('id'=>$catid))) {
            echo "OK - Set $setting='$value' (categoryid={$catid})\n";
        } else {
            echo "ERROR - failed to set $setting='$value' (categoryid={$catid})\n";
            exit(1);
        }   
    }

    protected function getArgumentsHelp()
    {
        return "\n\nARGUMENTS:\n\tcategoryid setting value\n";
    }

}
