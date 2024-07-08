<?php

/**
 * moosh - Moodle Shell
 * @copyright 2021 unistra {@link http://unistra.fr}
 * @author  2021 Céline Perves <cperves@unistra.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Task;
use Moosh\MooshCommand;

class TaskSchedule extends MooshCommand
{

    public function __construct() {
        parent::__construct('schedule', 'task');
        $this->addOption('M|minute:', 'minute');
        $this->addOption('H|hour:', 'hour');
        $this->addOption('d|day:', 'day');
        $this->addOption('m|month:', 'month');
        $this->addOption('w|dayofweek:', 'Day of week');
        $this->addOption('x|disabled:', 'Disabled');
        $this->addOption('r|resettodefaults:', 'Reset to defaults', 0);
        $this->addArgument('taskname');
        $this->minArguments = 1;
        $this->maxArguments = 1;

    }

    public function execute()
    {
        if(count($this->arguments) == 0){
            $tasks = \core\task\manager::get_all_scheduled_tasks();
            $tasknames = array();
            foreach($tasks as $currenttask){
                $tasknames[] = get_class($currenttask);
            }
            cli_writeln('Available task names are :');
            cli_writeln(implode(PHP_EOL, $tasknames));
            die;
        }
        $taskname = $this->arguments[0];
        $task = \core\task\manager::get_scheduled_task($taskname);
        if (!$task) {
            cli_error("task $taskname not exists");
        }
        if (!empty($this->expandedOptions['resettodefaults'])) {
            $defaulttask = \core\task\manager::get_default_scheduled_task($taskname);
            $task->set_minute($defaulttask->get_minute());
            $task->set_hour($defaulttask->get_hour());
            $task->set_month($defaulttask->get_month());
            $task->set_day_of_week($defaulttask->get_day_of_week());
            $task->set_day($defaulttask->get_day());
            $task->set_disabled($defaulttask->get_disabled());
            $task->set_customised(false);
        } else {
            if(!empty($this->expandedOptions['minute'])){
                $task->set_minute($this->expandedOptions['minute']);
            }
            if(!empty($this->expandedOptions['hour'])) {
                $task->set_hour($this->expandedOptions['hour']);
            }
            if(!empty($this->expandedOptions['month'])) {
                $task->set_month($this->expandedOptions['month']);
            }
            if(!empty($this->expandedOptions['dayofweek'])) {
                $task->set_day_of_week($this->expandedOptions['dayofweek']);
            }
            if(!empty($this->expandedOptions['day'])) {
                $task->set_day($this->expandedOptions['day']);
            }
            if(!empty($this->expandedOptions['disabled'])) {
                $task->set_disabled($this->expandedOptions['disabled']);
            }
            $task->set_customised(true);
        }
        try {
            \core\task\manager::configure_scheduled_task($task);
            cli_writeln("Task configured");
        } catch (Exception $e) {
            cli_writeln("error while configuring task $taskname");
            cli_error($e->getMessage());
        }
    }
    protected function getArgumentsHelp() {
        $help = parent::getArgumentsHelp();
        $help .= "\n\n";
        $help .= "Schedule Moodle task";

        return $help;
    }
}