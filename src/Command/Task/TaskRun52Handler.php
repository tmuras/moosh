<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Task;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TaskRun52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('classname', InputArgument::REQUIRED, 'Task classname to execute');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $verbose = new VerboseLogger($output);

        $classname = $input->getArgument('classname');
        if ($classname[0] !== '\\') {
            $classname = '\\' . $classname;
        }

        $task = \core\task\manager::get_scheduled_task($classname);
        if (!$task) {
            $output->writeln("<error>Scheduled task '$classname' not found.</error>");
            return Command::FAILURE;
        }

        $output->writeln("Executing task: {$task->get_name()}");
        $verbose->step('Running task');

        $startTime = microtime(true);

        try {
            $task->execute();
        } catch (\Throwable $e) {
            $output->writeln("<error>Task failed: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }

        $elapsed = round(microtime(true) - $startTime, 2);
        $output->writeln("Task completed in {$elapsed}s.");

        return Command::SUCCESS;
    }
}
