<?php
/**
 * Rebuild context paths - it works similar as context_cleanup_task with \context_helper::build_all_paths(true);
 * Command: (php admin/tool/task/cli/schedule_task.php --execute='\core\task\context_cleanup_task')
 * https://docs.moodle.org/311/en/How_to_rebuild_context_paths
 * moosh context-rebuild
 *
 * rebuild context paths
 * @example moosh context-rebuild
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @introduced 2021-07-23
 * @author     Jakub Kleban <jakub.kleban2000@gmail.com>
 */

namespace Moosh\Command\Moodle39\Context;
use Moosh\MooshCommand;

class ContextRebuild extends MooshCommand {

    public function __construct() {
        parent::__construct('rebuild', 'context');
    }

    public function execute() {
        global $CFG;
        require_once($CFG->dirroot.'/lib/accesslib.php');

        // Increase memory limit.
        raise_memory_limit(MEMORY_EXTRA);

        \context_helper::cleanup_instances();
        echo " Cleaned up context instances\n";
        \context_helper::build_all_paths(true);
        echo " Builded all paths\n";
    }
}