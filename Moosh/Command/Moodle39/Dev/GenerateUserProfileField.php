<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Dev;
use Moosh\MooshCommand;

class GenerateUserProfileField extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('userprofilefield', 'generate');

        $this->addArgument('userprofile_name');
    }

    public function execute()
    {
        $userprofilename = $this->arguments[0];
        if(!preg_match('/^[a-z0-9_]+$/  ', $userprofilename)) {
            cli_error('Agrument is not valid userprofile name');
        }
        $userprofilepath = $this->topDir . '/user/profile/field/' . $userprofilename;
        if (file_exists($userprofilepath)) {
            cli_error("Already exists: '$userprofilepath'");
        }

        run_external_command("cp -r '{$this->mooshDir}/vendor/moodlehq/moodle-user_profile_field' '$userprofilepath'", "Copying from module template failed");

        if (file_exists("$userprofilepath/.git")) {
            run_external_command("rm --interactive=never -r '$userprofilepath/.git'", "Removing .git failed");
        }

        // //replace newblock with $this->arguments[0]
        run_external_command("find '$userprofilepath' -type f -exec sed 's/myprofilefield/{$this->arguments[0]}/g' -i {} \;", "sed command failed");

        //rename lang/en/block_newblock.php
        run_external_command("mv '$userprofilepath/lang/en/profilefield_myprofilefield.php' '$userprofilepath/lang/en/profilefield_{$this->arguments[0]}.php'", "Renaming lang file failed");


    }
}
