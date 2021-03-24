<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Dev;
use Moosh\MooshCommand;

class GenerateQtype extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('qtype', 'generate');

        $this->addArgument('module_name');
    }

    public function execute()
    {
        //copy newmodule
        $modPath = $this->topDir . '/question/type/' . $this->arguments[0];
        if (file_exists($modPath)) {
            cli_problem("Already exists: '$modPath'");
            cli_problem("Not creating new question type " . $this->arguments[0]);
            exit(1);
        }
        run_external_command("cp -r '{$this->mooshDir}/vendor/jamiepratt/moodle-qtype_TEMPLATE' '$modPath'", "Copying from qtype template failed");

        if (file_exists("$modPath/.git")) {
            run_external_command("rm --interactive=never -r '$modPath/.git'", "Removing .git failed");
        }

        //replace newmodule with $this->arguments[0]
        run_external_command("find '$modPath' -type f -exec sed 's/YOURQTYPENAME/{$this->arguments[0]}/g' -i {} \;", "sed command failed");

        //rename lang/en/qtype_YOURQTYPENAME.php
        run_external_command("mv '$modPath/lang/en/qtype_YOURQTYPENAME.php' '$modPath/lang/en/qtype_{$this->arguments[0]}.php'", "Renaming lang file failed");

        //rename edit_YOURQTYPENAME_form.php
        run_external_command("mv '$modPath/edit_YOURQTYPENAME_form.php' '$modPath/edit_{$this->arguments[0]}_form.php'", "Renaming lang file failed");
    }
}
