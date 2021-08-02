<?php
/**
 * Rebuild context paths - it works the same way as command with \context_helper::build_all_paths(true);
 * (php admin/tool/task/cli/schedule_task.php --execute='\core\task\context_cleanup_task' --showdebugging)
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
        global $CFG, $DB;
        require_once($CFG->dirroot.'/lib/accesslib.php');

        set_debugging(DEBUG_DEVELOPER, true);

        // Increase memory limit.
        raise_memory_limit(MEMORY_EXTRA);

        $predbqueries = $DB->perf_get_queries();
        $pretime = microtime(true);

        $fullname = 'Cleanup contexts (core\task\context_cleanup_task)';
        echo "Execute scheduled task: $fullname\n";

        try {
            self::fake_execute_with_true(); // Here was $task->execute();
            if (isset($predbqueries)) {
                echo "... used " . ($DB->perf_get_queries() - $predbqueries) . " dbqueries\n";
                echo "... used " . (microtime(1) - $pretime) . " seconds\n";
            }
            echo "Scheduled task complete: $fullname\n";
            exit(0);
        } catch (Exception $e) {
            if ($DB->is_transaction_started()) {
                $DB->force_transaction_rollback();
            }
            echo "... used " . ($DB->perf_get_queries() - $predbqueries) . " dbqueries\n";
            echo "... used " . (microtime(true) - $pretime) . " seconds\n";
            echo 'Scheduled task failed: ' . $fullname . ',' . $e->getMessage() . PHP_EOL;
            if ($CFG->debugdeveloper) {
                if (!empty($e->debuginfo)) {
                    echo "Debug info:\n $e->debuginfo";
                }
                echo "Backtrace:\n";
                echo format_backtrace($e->getTrace(), true);
            }
            exit(1);
        }
    }

    private function fake_execute_with_true(){
        \context_helper::cleanup_instances();
        echo " Cleaned up context instances\n";
        \context_helper::build_all_paths(true);
    }
}