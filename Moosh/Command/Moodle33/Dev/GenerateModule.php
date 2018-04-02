<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle33\Dev;

use Moosh\MooshCommand;

class GenerateModule extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('module', 'generate');

        $this->addArgument('module_name');
    }

    public function execute()
    {
        require_once($this->mooshDir.'/vendor/mudrd8mz/moodle-tool_pluginskel/vendor/autoload.php');
        require_once($this->mooshDir.'/vendor/mudrd8mz/moodle-tool_pluginskel/locallib.php');
        require_once($this->mooshDir.'/vendor/mudrd8mz/moodle-tool_pluginskel/classes/local/util/manager.php');
        require_once($this->mooshDir.'/vendor/mudrd8mz/moodle-tool_pluginskel/classes/local/util/mustache.php');
        require_once($this->mooshDir.'/vendor/mudrd8mz/moodle-tool_pluginskel/classes/local/skel/base.php');
        require_once($this->mooshDir.'/vendor/mudrd8mz/moodle-tool_pluginskel/classes/local/skel/php_web_file.php');
        require_once($this->mooshDir.'/vendor/mudrd8mz/moodle-tool_pluginskel/classes/local/skel/php_internal_file.php');
        require_once($this->mooshDir.'/vendor/mudrd8mz/moodle-tool_pluginskel/classes/local/skel/lib_php_file.php');
        require_once($this->mooshDir.'/vendor/mudrd8mz/moodle-tool_pluginskel/classes/local/skel/view_php_file.php');
        require_once($this->mooshDir.'/vendor/mudrd8mz/moodle-tool_pluginskel/classes/local/skel/txt_file.php');
        require_once($this->mooshDir.'/vendor/mudrd8mz/moodle-tool_pluginskel/classes/local/skel/php_cli_file.php');
        require_once($this->mooshDir.'/vendor/mudrd8mz/moodle-tool_pluginskel/classes/local/skel/version_php_file.php');
        require_once($this->mooshDir.'/vendor/mudrd8mz/moodle-tool_pluginskel/classes/local/skel/lang_file.php');

        if (strpos($this->arguments[0], '_') !== false) {
            cli_error("Module name can not contain _");
        }

        //copy newmodule
        $modpath = $this->topDir.'/mod/'.$this->arguments[0];
        $modname = "mod_".$this->arguments[0];
        if (file_exists($modpath)) {
            cli_problem("Already exists: '$modpath'");
            cli_problem("Not creating new module ".$this->arguments[0]);
            exit(1);
        }

        echo "Generating module $modname based on tool_pluginskel\n";

        $recipe = array(
                'copyright' => '2018 Your Name <you@example.com>',
                'component' => 'mod_xxx',
                'release' => '0.1.0',
                'version' => 2018040200,
                'requires' => '2017111300',
                'maturity' => 'MATURITY_ALPHA',
                'name' => 'Module XXX',
                'mod_features' =>
                        array(
                                'gradebook' => true,
                                'file_area' => true,
                                'navigation' => true,
                        ),
                'features' =>
                        array(
                                'install' => true,
                                'uninstall' => true,
                                'settings' => true,
                                'readme' => true,
                                'license' => true,
                                'upgrade' => true,
                                'upgradelib' => true,
                        ),
                'cli_scripts' =>
                        array(
                                0 =>
                                        array(
                                                'filename' => 'sample_cli',
                                        ),
                        ),
        );

        $logger = new \Monolog\Logger('tool_pluginskel');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', constant('\Monolog\Logger::NOTICE')));
        $manager = \tool_pluginskel\local\util\manager::instance($logger);
        $manager->load_recipe($recipe);
        $manager->make();
        $manager->write_files('/tmp/a');
    }
}
