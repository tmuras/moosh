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

        $execute = '\core\task\context_cleanup_task';
        if (!$task = \core\task\manager::get_scheduled_task($execute)) {
            mtrace("Task '$execute' not found");
            exit(1);
        }

        if (moodle_needs_upgrading()) {
            mtrace("Moodle upgrade pending, cannot execute tasks.");
            exit(1);
        }

        // Increase memory limit.
        raise_memory_limit(MEMORY_EXTRA);

        // Emulate normal session - we use admin account by default.
        cron_setup_user();

        $predbqueries = $DB->perf_get_queries();
        $pretime = microtime(true);

        \core\task\logmanager::start_logging($task);
        $fullname = $task->get_name() . ' (' . get_class($task) . ')';
        mtrace('Execute scheduled task: ' . $fullname);
        // NOTE: it would be tricky to move this code to \core\task\manager class,
        //       because we want to do detailed error reporting.
        $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');
        if (!$cronlock = $cronlockfactory->get_lock('core_cron', 10)) {
            mtrace('Cannot obtain cron lock');
            exit(129);
        }
        if (!$lock = $cronlockfactory->get_lock('\\' . get_class($task), 10)) {
            $cronlock->release();
            mtrace('Cannot obtain task lock');
            exit(130);
        }

        $task->set_lock($lock);
        if (!$task->is_blocking()) {
            $cronlock->release();
        } else {
            $task->set_cron_lock($cronlock);
        }

        try {
            get_mailer('buffer');
            self::fake_execute_with_true(); // Here was $task->execute();
            if (isset($predbqueries)) {
                mtrace("... used " . ($DB->perf_get_queries() - $predbqueries) . " dbqueries");
                mtrace("... used " . (microtime(1) - $pretime) . " seconds");
            }
            mtrace('Scheduled task complete: ' . $fullname);
            \core\task\manager::scheduled_task_complete($task);
            get_mailer('close');
            exit(0);
        } catch (Exception $e) {
            if ($DB->is_transaction_started()) {
                $DB->force_transaction_rollback();
            }
            mtrace("... used " . ($DB->perf_get_queries() - $predbqueries) . " dbqueries");
            mtrace("... used " . (microtime(true) - $pretime) . " seconds");
            mtrace('Scheduled task failed: ' . $fullname . ',' . $e->getMessage());
            if ($CFG->debugdeveloper) {
                if (!empty($e->debuginfo)) {
                    mtrace("Debug info:");
                    mtrace($e->debuginfo);
                }
                mtrace("Backtrace:");
                mtrace(format_backtrace($e->getTrace(), true));
            }
            \core\task\manager::scheduled_task_failed($task);
            get_mailer('close');
            exit(1);
        }
    }

    private function fake_execute_with_true(){
        \context_helper::cleanup_instances();
        mtrace(' Cleaned up context instances');
        \context_helper::build_all_paths(true);
    }
}