<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Dev;
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
        $ret = null;
        system("cp -r '{$this->mooshDir}/vendor/jamiepratt/moodle-qtype_TEMPLATE' '$modPath'",$ret);
        if($ret) {
            cli_error("Copying from qtype template failed");
        }

        $ret = null;
        system("rm --interactive=never -r '$modPath/.git'",$ret);
        if($ret) {
            cli_error("Removing .git failed");
        }

        //replace newmodule with $this->arguments[0]
        $ret = null;
        system("find '$modPath' -type f -exec sed 's/YOURQTYPENAME/{$this->arguments[0]}/g' -i {} \;",$ret);
        if($ret) {
            cli_error("sed command failed");
        }

        //rename lang/en/qtype_YOURQTYPENAME.php
        $ret = null;
        system("mv '$modPath/lang/en/qtype_YOURQTYPENAME.php' '$modPath/lang/en/qtype_{$this->arguments[0]}.php'",$ret);
        if($ret) {
            cli_error("Renaming lang file failed");
        }

        //rename edit_YOURQTYPENAME_form.php
        $ret = null;
        system("mv '$modPath/edit_YOURQTYPENAME_form.php' '$modPath/edit_{$this->arguments[0]}_form.php'",$ret);
        if($ret) {
            cli_error("Renaming lang file failed");
        }
    }
}
