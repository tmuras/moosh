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

class TaskList52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('component', null, InputOption::VALUE_REQUIRED, 'Filter by component')
            ->addOption('disabled', null, InputOption::VALUE_NONE, 'Show only disabled tasks')
            ->addOption('enabled', null, InputOption::VALUE_NONE, 'Show only enabled tasks')
            ->addOption('overdue', null, InputOption::VALUE_NONE, 'Show only overdue tasks')
            ->addOption('running', null, InputOption::VALUE_NONE, 'Show only currently running tasks')
            ->addOption('failed', null, InputOption::VALUE_NONE, 'Show only tasks with fail delay')
            ->addOption('classname-only', null, InputOption::VALUE_NONE, 'Show classnames only');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $idOnly = $input->getOption('classname-only');

        $filterComponent = $input->getOption('component');
        $filterDisabled = $input->getOption('disabled');
        $filterEnabled = $input->getOption('enabled');
        $filterOverdue = $input->getOption('overdue');
        $filterRunning = $input->getOption('running');
        $filterFailed = $input->getOption('failed');

        $verbose->step('Loading scheduled tasks');
        $tasks = \core\task\manager::get_all_scheduled_tasks();
        $now = time();

        $filtered = [];
        foreach ($tasks as $task) {
            if ($filterComponent !== null && $task->get_component() !== $filterComponent) {
                continue;
            }
            if ($filterDisabled && !$task->get_disabled()) {
                continue;
            }
            if ($filterEnabled && $task->get_disabled()) {
                continue;
            }
            if ($filterOverdue && ($task->get_next_run_time() === null || $task->get_next_run_time() > $now)) {
                continue;
            }
            if ($filterRunning && $task->get_timestarted() === null) {
                continue;
            }
            if ($filterFailed && $task->get_fail_delay() == 0) {
                continue;
            }
            $filtered[] = $task;
        }

        if (empty($filtered)) {
            $output->writeln('No tasks found matching criteria.');
            return Command::SUCCESS;
        }

        if ($idOnly) {
            foreach ($filtered as $task) {
                $output->writeln(get_class($task));
            }
            return Command::SUCCESS;
        }

        $headers = ['classname', 'component', 'schedule', 'disabled', 'last_run', 'next_run', 'fail_delay', 'running'];
        $rows = [];
        foreach ($filtered as $task) {
            $schedule = $task->get_minute() . ' ' . $task->get_hour() . ' ' . $task->get_day() . ' ' .
                $task->get_month() . ' ' . $task->get_day_of_week();
            $lastRun = $task->get_last_run_time() ? date('Y-m-d H:i', $task->get_last_run_time()) : 'never';
            $nextRun = $task->get_next_run_time() ? date('Y-m-d H:i', $task->get_next_run_time()) : 'n/a';
            $running = $task->get_timestarted() ? 'yes (pid=' . ($task->get_pid() ?? '?') . ')' : '';

            // Shorten classname for display.
            $classname = get_class($task);

            $rows[] = [$classname, $task->get_component(), $schedule, $task->get_disabled() ? 'yes' : '', $lastRun, $nextRun, $task->get_fail_delay() ?: '', $running];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
