<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Dev;
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
        $ret = null;
        system("cp -r '{$this->mooshDir}/vendor/danielneis/moodle-block_newblock' '$blockPath'", $ret);
        if ($ret) {
            cli_error("Copying from module template failed");
        }

        if (file_exists("$blockPath/.git")) {
            $ret = null;
            system("rm --interactive=never -r '$blockPath/.git'", $ret);
            if ($ret) {
                cli_error("Removing .git failed");
            }
        }

        //replace newblock with $this->arguments[0]
        $ret = null;
        system("find '$blockPath' -type f -exec sed 's/newblock/{$this->arguments[0]}/g' -i {} \;", $ret);
        if ($ret) {
            cli_error("sed command failed");
        }

        //replace newblock with $this->arguments[0]
        $ret = null;
        system("find '$blockPath' -type f -exec sed 's/Newblock/{$this->arguments[0]}/g' -i {} \;", $ret);
        if ($ret) {
            cli_error("sed command failed");
        }


        //rename lang/en/block_newblock.php
        $ret = null;
        system("mv '$blockPath/lang/en/block_newblock.php' '$blockPath/lang/en/block_{$this->arguments[0]}.php'", $ret);
        if ($ret) {
            cli_error("Renaming lang file failed");
        }

        //rename block_newblock.php
        $ret = null;
        system("mv '$blockPath/block_newblock.php' '$blockPath/block_{$this->arguments[0]}.php'", $ret);
        if ($ret) {
            cli_error("Renaming block_newblock.php failed");
        }

    }
}
