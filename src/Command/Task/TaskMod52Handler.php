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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TaskMod52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('classname', InputArgument::REQUIRED, 'Task classname (e.g. \\core\\task\\send_new_user_passwords_task)')
            ->addOption('minute', null, InputOption::VALUE_REQUIRED, 'Set cron minute')
            ->addOption('hour', null, InputOption::VALUE_REQUIRED, 'Set cron hour')
            ->addOption('day', null, InputOption::VALUE_REQUIRED, 'Set cron day of month')
            ->addOption('month', null, InputOption::VALUE_REQUIRED, 'Set cron month')
            ->addOption('dayofweek', null, InputOption::VALUE_REQUIRED, 'Set cron day of week')
            ->addOption('enabled', null, InputOption::VALUE_REQUIRED, 'Enable (1) or disable (0)')
            ->addOption('reset', null, InputOption::VALUE_NONE, 'Reset to default schedule')
            ->addOption('clear-fail', null, InputOption::VALUE_NONE, 'Clear fail delay');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $classname = $input->getArgument('classname');
        $newMinute = $input->getOption('minute');
        $newHour = $input->getOption('hour');
        $newDay = $input->getOption('day');
        $newMonth = $input->getOption('month');
        $newDow = $input->getOption('dayofweek');
        $newEnabled = $input->getOption('enabled');
        $doReset = $input->getOption('reset');
        $doClearFail = $input->getOption('clear-fail');

        // Normalize classname.
        if ($classname[0] !== '\\') {
            $classname = '\\' . $classname;
        }

        $task = \core\task\manager::get_scheduled_task($classname);
        if (!$task) {
            $output->writeln("<error>Scheduled task '$classname' not found.</error>");
            return Command::FAILURE;
        }

        $hasChanges = $doReset || $doClearFail || $newMinute !== null || $newHour !== null
            || $newDay !== null || $newMonth !== null || $newDow !== null || $newEnabled !== null;

        if (!$hasChanges) {
            $output->writeln('<error>No modifications specified. Use --minute, --hour, --day, --month, --dayofweek, --enabled, --reset, or --clear-fail.</error>');
            return Command::FAILURE;
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would modify task '{$task->get_name()}' (use --run to execute):</info>");
            if ($doReset) { $output->writeln("  reset to defaults"); }
            if ($doClearFail) { $output->writeln("  clear fail delay (current: {$task->get_fail_delay()}s)"); }
            if ($newMinute !== null) { $output->writeln("  minute: {$task->get_minute()} → $newMinute"); }
            if ($newHour !== null) { $output->writeln("  hour: {$task->get_hour()} → $newHour"); }
            if ($newDay !== null) { $output->writeln("  day: {$task->get_day()} → $newDay"); }
            if ($newMonth !== null) { $output->writeln("  month: {$task->get_month()} → $newMonth"); }
            if ($newDow !== null) { $output->writeln("  dayofweek: {$task->get_day_of_week()} → $newDow"); }
            if ($newEnabled !== null) { $output->writeln("  enabled: " . ((int) $newEnabled ? 'yes' : 'no')); }
            return Command::SUCCESS;
        }

        $verbose->step("Modifying task '{$task->get_name()}'");

        if ($doReset) {
            \core\task\manager::reset_scheduled_tasks_for_component($task->get_component());
            $output->writeln("Reset tasks for component '{$task->get_component()}' to defaults.");
            // Reload after reset.
            $task = \core\task\manager::get_scheduled_task($classname);
        }

        if ($doClearFail) {
            \core\task\manager::clear_fail_delay($task);
            $verbose->info('Cleared fail delay');
            $task = \core\task\manager::get_scheduled_task($classname);
        }

        if ($newMinute !== null) { $task->set_minute($newMinute); }
        if ($newHour !== null) { $task->set_hour($newHour); }
        if ($newDay !== null) { $task->set_day($newDay); }
        if ($newMonth !== null) { $task->set_month($newMonth); }
        if ($newDow !== null) { $task->set_day_of_week($newDow); }
        if ($newEnabled !== null) { $task->set_disabled(!(int) $newEnabled); }

        if ($newMinute !== null || $newHour !== null || $newDay !== null || $newMonth !== null || $newDow !== null || $newEnabled !== null) {
            \core\task\manager::configure_scheduled_task($task);
            $verbose->info('Saved task configuration');
        }

        // Reload and display.
        $task = \core\task\manager::get_scheduled_task($classname);
        $schedule = $task->get_minute() . ' ' . $task->get_hour() . ' ' . $task->get_day() . ' ' .
            $task->get_month() . ' ' . $task->get_day_of_week();

        $headers = ['classname', 'name', 'schedule', 'disabled', 'fail_delay', 'next_run'];
        $rows = [[
            get_class($task),
            $task->get_name(),
            $schedule,
            $task->get_disabled() ? 'yes' : 'no',
            $task->get_fail_delay(),
            $task->get_next_run_time() ? date('Y-m-d H:i', $task->get_next_run_time()) : 'n/a',
        ]];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
