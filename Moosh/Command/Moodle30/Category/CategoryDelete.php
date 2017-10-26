<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2017 onwards, Marty Gilbert
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle30\Category;
use Moosh\MooshCommand;

class CategoryDelete extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('delete', 'category');

        $this->addArgument('catid');

        //$this->addOption('t|test', 'option with no value');
        //$this->addOption('o|option:', 'option with value and default', 'default');

    }

    public function execute()
    {
        // Some variables you may want to use
        //  $this->cwd - the directory where moosh command was executed
        //  $this->mooshDir - moosh installation directory
        //  $this->expandedOptions - commandline provided options, merged with defaults
        //  $this->topDir - top Moodle directory
        //  $this->arguments[0] - first argument passed
        //  $this->pluginInfo - array with information about the current plugin (based on cwd), keys:'type','name','dir'
        //  $this->verbose - if set to true, then "moosh -v" was run - add more verbose / debug information
		
		global $CFG;

        require_once $CFG->dirroot . '/course/lib.php';
		require_once($CFG->libdir.'/coursecatlib.php');
		
        $options = $this->expandedOptions;

		$catid = $this->arguments[0];
		$category = \coursecat::get($catid);

		$courses = $category->delete_full(false);

		foreach ($courses as $course){
			echo "Deleted course $course->shortname\n";
		}

        /* if verbose mode was requested, show some more information/debug messages
        if($this->verbose) {
            echo "Say what you're doing now";
        }
        */
    }
}
