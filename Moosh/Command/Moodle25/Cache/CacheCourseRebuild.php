<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle25\Cache;
use Moosh\MooshCommand;

class CacheCourseRebuild extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('course-rebuild', 'cache');

        $this->addOption('a|all', 'rebuild all courses cache');

        $this->addArgument('courseid');

        $this->minArguments = 0;
    }

    public function execute()
    {
        global $CFG;

        require_once $CFG->dirroot . '/lib/modinfolib.php';
        $options = $this->expandedOptions;

        if ($this->arguments[0]) {
            rebuild_course_cache($this->arguments[0]);
            echo "Succesfully rebuilt cache for course " . $this->arguments[0] . "\n";
        }

        if ($options['all']) {
            rebuild_course_cache();
            exit("Succesfully rebuilt all courses cache\n");
        }
    }
}

