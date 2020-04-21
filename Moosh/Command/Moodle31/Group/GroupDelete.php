<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle31\Group;
use Moosh\MooshCommand;

class GroupDelete extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('delete', 'group');

        // lib/weblib.php defines FORMAT_MARKDOWN
        $this->addArgument('id');

        $this->minArguments = 1;

    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/group/lib.php';

        $group = new \stdClass();
        $group->id = $this->arguments[0];

        if (groups_delete_group($group->id)) {
            echo "$group->id\n";
        }
    }
}
