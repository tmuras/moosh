<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace Moosh\Command\Moodle25\Activity;
use Moosh\MooshCommand;

class ActivityDelete extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('delete', 'activity');

        $this->addArgument('moduleid');
    }

    public function execute() 
    {
        global $CFG;
        require_once $CFG->dirroot . '/course/lib.php';

        $moduleid = intval($this->arguments[0]);

        if ($moduleid <= 0) {
            exit("Argument 'moduleid' must be bigger than 0.");
        } 

        course_delete_module($moduleid);
        echo "Deleted activity $moduleid\n";
    }
}

