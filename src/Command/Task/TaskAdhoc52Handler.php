<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Task;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TaskAdhoc52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('failed', null, InputOption::VALUE_NONE, 'Show/process only failed tasks')
            ->addOption('classname', null, InputOption::VALUE_REQUIRED, 'Filter by classname')
            ->addOption('execute', null, InputOption::VALUE_NONE, 'Execute pending adhoc tasks')
            ->addOption('clean', null, InputOption::VALUE_NONE, 'Delete old failed adhoc tasks')
            ->addOption('count', null, InputOption::VALUE_NONE, 'Show summary counts only');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $showFailed = $input->getOption('failed');
        $filterClass = $input->getOption('classname');
        $doExecute = $input->getOption('execute');
        $doClean = $input->getOption('clean');
        $showCount = $input->getOption('count');

        // Clean old failed tasks.
        if ($doClean) {
            if (!$runMode) {
                $failedCount = $DB->count_records_select('task_adhoc', 'faildelay > 0');
                $output->writeln("<info>Dry run — would clean failed adhoc tasks ($failedCount with fail delay) (use --run to execute).</info>");
                return Command::SUCCESS;
            }
            $verbose->step('Cleaning failed adhoc tasks');
            \core\task\manager::clean_failed_adhoc_tasks();
            $output->writeln('Cleaned old failed adhoc tasks.');
            return Command::SUCCESS;
        }

        // Execute adhoc tasks.
        if ($doExecute) {
            if (!$runMode) {
                $pendingCount = $DB->count_records_select('task_adhoc', 'nextruntime <= ?', [time()]);
                $output->writeln("<info>Dry run — would execute $pendingCount pending adhoc task(s) (use --run to execute).</info>");
                return Command::SUCCESS;
            }
            $verbose->step('Executing adhoc tasks');
            $executed = 0;
            while ($task = \core\task\manager::get_next_adhoc_task(time(), true, $filterClass)) {
                $output->writeln("Running: " . get_class($task) . " (ID={$task->get_id()})");
                try {
                    $task->execute();
                    \core\task\manager::adhoc_task_complete($task);
                    $executed++;
                } catch (\Throwable $e) {
                    $output->writeln("<comment>  Failed: {$e->getMessage()}</comment>");
                    \core\task\manager::adhoc_task_failed($task);
                }
            }
            $output->writeln("Executed $executed adhoc task(s).");
            return Command::SUCCESS;
        }

        // Show summary counts.
        if ($showCount) {
            $verbose->step('Getting adhoc task summary');
            $total = $DB->count_records('task_adhoc');
            $pending = $DB->count_records_select('task_adhoc', 'nextruntime <= ? AND timestarted IS NULL', [time()]);
            $running = $DB->count_records_select('task_adhoc', 'timestarted IS NOT NULL');
            $failed = $DB->count_records_select('task_adhoc', 'faildelay > 0');

            $headers = ['Metric', 'Value'];
            $rows = [
                ['Total adhoc tasks', $total],
                ['Pending (due)', $pending],
                ['Running', $running],
                ['Failed (with delay)', $failed],
            ];

            $formatter = new ResultFormatter($output, $format);
            $formatter->display($headers, $rows);
            return Command::SUCCESS;
        }

        // Default: list tasks.
        $verbose->step('Listing adhoc tasks');

        $conditions = [];
        $params = [];

        if ($showFailed) {
            $conditions[] = 'faildelay > 0';
        }
        if ($filterClass !== null) {
            $conditions[] = 'classname = ?';
            $params[] = $filterClass;
        }

        $where = empty($conditions) ? '' : implode(' AND ', $conditions);
        $tasks = $DB->get_records_select('task_adhoc', $where ?: null, $params ?: null, 'nextruntime ASC', '*', 0, 100);

        if (empty($tasks)) {
            $output->writeln('No adhoc tasks found.');
            return Command::SUCCESS;
        }

        $headers = ['id', 'classname', 'component', 'nextruntime', 'faildelay', 'running'];
        $rows = [];
        foreach ($tasks as $t) {
            $rows[] = [
                $t->id,
                $t->classname,
                $t->component,
                date('Y-m-d H:i', $t->nextruntime),
                $t->faildelay ?: '',
                $t->timestarted ? 'yes' : '',
            ];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
