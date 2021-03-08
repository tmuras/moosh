<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Dev;
use Moosh\MooshCommand;

class GenerateBlock extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('block', 'generate');

        $this->addArgument('module_name');
    }

    public function execute()
    {
        //copy newmodule
        $blockPath = $this->topDir . '/blocks/' . $this->arguments[0];
        if (file_exists($blockPath)) {
            cli_problem("Already exists: '$blockPath'");
            cli_problem("Not creating new block " . $this->arguments[0]);
            exit(1);
        }
        run_external_command("cp -r '{$this->mooshDir}/vendor/danielneis/moodle-block_newblock' '$blockPath'", "Copying from module template failed");

        if (file_exists("$blockPath/.git")) {
            run_external_command("rm --interactive=never -r '$blockPath/.git'", "Removing .git failed");
        }

        //replace newblock with $this->arguments[0]
        run_external_command("find '$blockPath' -type f -exec sed 's/newblock/{$this->arguments[0]}/g' -i {} \;", "sed command failed");

        //rename lang/en/block_newblock.php
        run_external_command("mv '$blockPath/lang/en/block_newblock.php' '$blockPath/lang/en/block_{$this->arguments[0]}.php'", "Renaming lang file failed");

        //rename block_newblock.php
        run_external_command("mv '$blockPath/block_newblock.php' '$blockPath/block_{$this->arguments[0]}.php'", "Renaming block_newblock.php failed");
    }
}
