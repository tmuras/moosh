<?php
/**
 * moosh - Moodle Shell - generate local plugin
 *
 * @copyright  2015 Daniel Neis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Dev;
use Moosh\MooshCommand;

class GenerateEnrol extends MooshCommand {

    public function __construct() {

        parent::__construct('enrol', 'generate');

        $this->addArgument('enrol_name');
    }

    public function execute() {
        //copy newmodule
        $modPath = $this->topDir . '/enrol/' . $this->arguments[0];
        if (file_exists($modPath)) {
            cli_problem("Already exists: '$modPath'");
            cli_problem("Not creating new local " . $this->arguments[0]);
            exit(1);
        }
        run_external_command("cp -r '{$this->mooshDir}/vendor/danielneis/moodle-enrol_newenrol' '$modPath'", "Copying from local template failed");

        if (file_exists("$modPath/.git")) {
            run_external_command("rm --interactive=never -r '$modPath/.git'", "Removing .git failed");
        }

        //replace newmodule with $this->arguments[0]
        run_external_command("find '$modPath' -type f -exec sed 's/newenrol/{$this->arguments[0]}/g' -i {} \;", "sed command failed");

        //rename lang/en/newlocal.php
        run_external_command("mv '$modPath/lang/en/enrol_newenrol.php' '$modPath/lang/en/enrol_{$this->arguments[0]}.php'", "Renaming lang file failed");
    }
}
