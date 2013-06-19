<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Dev;
use Moosh\MooshCommand;

class GenerateGradeReport extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('gradereport', 'generate');

        $this->addArgument('report_name');
    }

    public function execute()
    {
        //copy newmodule
        $modPath = $this->topDir . '/grade/report/' . $this->arguments[0];
        if (file_exists($modPath)) {
            cli_problem("Already exists: '$modPath'");
            cli_problem("Not creating new grade report " . $this->arguments[0]);
            exit(1);
        }
        $ret = null;
        system("cp -r '{$this->mooshDir}/vendor/danielneis/moodle-gradereport_newgradereport' '$modPath'", $ret);
        if ($ret) {
            cli_error("Copying from grade report template failed");
        }

        if (file_exists("$modPath/.git")) {
            $ret = null;
            system("rm --interactive=never -r '$modPath/.git'", $ret);
            if ($ret) {
                cli_error("Removing .git failed");
            }
        }

        //replace newmodule with $this->arguments[0]
        $ret = null;
        system("find '$modPath' -type f -exec sed 's/newgradereport/{$this->arguments[0]}/g' -i {} \;", $ret);
        if ($ret) {
            cli_error("sed command failed");
        }

        //rename lang/en/newmodule.php
        $ret = null;
        system("mv '$modPath/lang/en/gradereport_newgradereport.php' '$modPath/lang/en/gradereport_{$this->arguments[0]}.php'", $ret);
        if ($ret) {
            cli_error("Renaming lang file failed");
        }
    }
}
