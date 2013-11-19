<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Dev;
use Moosh\MooshCommand;

class GenerateWS extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('ws', 'generate');

        $this->addArgument('web_service_name');
    }

    public function execute()
    {
        //copy newmodule
        $modPath = $this->topDir . '/local/' . $this->arguments[0];
        if (file_exists($modPath)) {
            cli_problem("Already exists: '$modPath'");
            cli_problem("Not creating new module " . $this->arguments[0]);
            exit(1);
        }
        $ret = null;
        system("cp -r '{$this->mooshDir}/vendor/moodlehq/moodle-local_wstemplate' '$modPath'", $ret);
        if ($ret) {
            cli_error("Copying from module template failed");
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
        system("find '$modPath' -type f -exec sed 's/wstemplate/{$this->arguments[0]}/g' -i {} \;", $ret);
        if ($ret) {
            cli_error("sed command failed");
        }

        //rename lang/en/newmodule.php
        $ret = null;
        system("mv '$modPath/lang/en/local_wstemplate.php' '$modPath/lang/en/{$this->arguments[0]}.php'", $ret);
        if ($ret) {
            cli_error("Renaming lang file failed");
        }
    }
}
