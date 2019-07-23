<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Dev;
use Moosh\MooshCommand;

class GenerateModule extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('module', 'generate');

        $this->addArgument('module_name');
    }

    public function execute()
    {

        if(strpos($this->arguments[0],'_') !== false) {
            cli_error("Module name can not contain _");
        }

        //copy newmodule
        $modPath = $this->topDir . '/mod/' . $this->arguments[0];
        if (file_exists($modPath)) {
            cli_problem("Already exists: '$modPath'");
            cli_problem("Not creating new module " . $this->arguments[0]);
            exit(1);
        }
        run_external_command("cp -r '{$this->mooshDir}/vendor/moodlehq/moodle-mod_newmodule' '$modPath'", "Copying from module template failed");

        if (file_exists("$modPath/.git")) {
            run_external_command("rm --interactive=never -r '$modPath/.git'", "Removing .git failed");
        }

        //replace newmodule with $this->arguments[0]
        run_external_command("find '$modPath' -type f -exec sed 's/newmodule/{$this->arguments[0]}/g' -i {} \;", "sed command failed");

        //rename lang/en/newmodule.php
        run_external_command("mv '$modPath/lang/en/newmodule.php' '$modPath/lang/en/{$this->arguments[0]}.php'", "Renaming lang file failed");

        //rename backup files
        run_external_command("mv '$modPath/backup/moodle2/backup_newmodule_activity_task.class.php' '$modPath/backup/moodle2/backup_{$this->arguments[0]}_activity_task.class.php'", "Renaming backup activity task file failed");
        run_external_command("mv '$modPath/backup/moodle2/backup_newmodule_stepslib.php' '$modPath/backup/moodle2/backup_{$this->arguments[0]}_stepslib.php'", "Renaming backup stepslib file failed");

        //rename restore files
        run_external_command("mv '$modPath/backup/moodle2/restore_newmodule_activity_task.class.php' '$modPath/backup/moodle2/restore_{$this->arguments[0]}_activity_task.class.php'", "Renaming restore activity task file failed");
        run_external_command("mv '$modPath/backup/moodle2/restore_newmodule_stepslib.php' '$modPath/backup/moodle2/restore_{$this->arguments[0]}_stepslib.php'", "Renaming restore stepslib file failed");
    }
}
