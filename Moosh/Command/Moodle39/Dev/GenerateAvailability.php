<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Dev;
use Moosh\MooshCommand;

class GenerateAvailability extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('availability', 'generate');

        $this->addArgument('export_name');
    }

    public function execute()
    {
        //copy newmodule
        $modPath = $this->topDir . '/availability/condition/' . $this->arguments[0];
        if (file_exists($modPath)) {
            cli_problem("Already exists: '$modPath'");
            cli_problem("Not creating new availability condition" . $this->arguments[0]);
            exit(1);
        }
        run_external_command("cp -r '{$this->mooshDir}/vendor/danielneis/moodle-availability_newavailability' '$modPath'", "Copying from availability condition template failed");

        if (file_exists("$modPath/.git")) {
            run_external_command("rm --interactive=never -r '$modPath/.git'", "Removing .git failed");
        }

        //replace newmodule with $this->arguments[0]
        run_external_command("find '$modPath' -type f -exec sed 's/newavailability/{$this->arguments[0]}/g' -i {} \;", "sed command failed");

        //rename lang/en/newmodule.php
        run_external_command("mv '$modPath/lang/en/availability_newavailability.php' '$modPath/lang/en/availability_{$this->arguments[0]}.php'", "Renaming lang file failed");

        //rename yui/build/moodle-availability_newavailability-form/moodle-availability_newavailability-form.js
        run_external_command("mv '$modPath/yui/build/moodle-availability_newavailability-form/moodle-availability_newavailability-form.js' '{$modPath}/yui/build/moodle-availability_newavailability-form/moodle-availability_{$this->arguments[0]}-form.js'", "Renaming yui build js");

        //rename yui/build/moodle-availability_newavailability-form/moodle-availability_newavailability-form-debug.js
        run_external_command("mv '$modPath/yui/build/moodle-availability_newavailability-form/moodle-availability_newavailability-form-debug.js' '{$modPath}/yui/build/moodle-availability_newavailability-form/moodle-availability_{$this->arguments[0]}-form-debug.js'", "Renaming yui build debug js");

        //rename yui/build/moodle-availability_newavailability-form/moodle-availability_newavailability-form-min.js
        run_external_command("mv '$modPath/yui/build/moodle-availability_newavailability-form/moodle-availability_newavailability-form-min.js' '{$modPath}/yui/build/moodle-availability_newavailability-form/moodle-availability_{$this->arguments[0]}-form-min.js'", "Renaming yui build min js");

        //rename dir yui/build/moodle-availability_newavailability-form
        run_external_command("mv '$modPath/yui/build/moodle-availability_newavailability-form' '{$modPath}/yui/build/moodle-availability_{$this->arguments[0]}-form'", "Renaming yui build dir");
    }
}
