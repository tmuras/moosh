<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Task;

use Moosh\MooshCommand;

class LockCheck extends MooshCommand {
    public function __construct() {
        parent::__construct('lock-check', 'task');

        //$this->addArgument('name');

        $this->addOption('a|all', 'show all locks checked');
        //$this->addOption('o|option:', 'option with value and default', 'default');

    }

    public function execute() {
        global $CFG, $DB;

        // Some variables you may want to use
        //  $this->cwd - the directory where moosh command was executed
        //  $this->mooshDir - moosh installation directory
        //  $this->expandedOptions - commandline provided options, merged with defaults
        //  $this->topDir - top Moodle directory
        //  $this->arguments[0] - first argument passed
        //  $this->pluginInfo - array with information about the current plugin (based on cwd), keys:'type','name','dir'
        //  $this->verbose - if set to true, then "moosh -v" was run - add more verbose / debug information

        $options = $this->expandedOptions;

        $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');
        $cronlockfactory = get_class($cronlockfactory);
        echo "Cron lock factory: $cronlockfactory\n";
        
        //  Example: dbname:mdl__cron_
        $prefix = $CFG->dbname . ':' . $CFG->prefix . '_cron_';

        // Get the list of all tasks
        $tasks = \core\task\manager::get_all_scheduled_tasks();
        foreach ($tasks as $task) {
            $resourcekey = sha1($prefix . '\\' . get_class($task));

            $result = $DB->get_record_sql('SELECT IS_USED_LOCK(:resourcekey) AS process', 
                    ['resourcekey' => $resourcekey]);

            if ($result->process) {
                echo "[$resourcekey] " .  '\\' . get_class($task) . " locked by MySQL process {$result->process}\n";
            } else if ($options['all'])  {
                echo "[$resourcekey] " .  '\\' . get_class($task) . " not locked\n";
            }
        }
    }
}
