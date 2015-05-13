<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle28\Dev;
use Moosh\MooshCommand;

class GenerateMessageOutput extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('messageoutput', 'generate');

        $this->addArgument('export_name');
    }

    public function execute()
    {
        //copy newmodule
        $modPath = $this->topDir . '/message/output/' . $this->arguments[0];
        if (file_exists($modPath)) {
            cli_problem("Already exists: '$modPath'");
            cli_problem("Not creating new message output" . $this->arguments[0]);
            exit(1);
        }
        run_external_command("cp -r '{$this->mooshDir}/vendor/danielneis/moodle-message_newprocessor' '$modPath'", "Copying from message output template failed");

        if (file_exists("$modPath/.git")) {
            run_external_command("rm --interactive=never -r '$modPath/.git'", "Removing .git failed");
        }

        //replace newmodule with $this->arguments[0]
        run_external_command("find '$modPath' -type f -exec sed 's/newprocessor/{$this->arguments[0]}/g' -i {} \;", "sed command failed");

        //rename message_output_newprocessor.php
        run_external_command("mv '$modPath/message_output_newprocessor.php' '$modPath/message_output_{$this->arguments[0]}.php'", "Renaming lang file failed");

        //rename lang/en/newmodule.php
        run_external_command("mv '$modPath/lang/en/message_newprocessor.php' '$modPath/lang/en/message_{$this->arguments[0]}.php'", "Renaming lang file failed");
    }
}
