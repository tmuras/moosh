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
        require_once($this->mooshDir.'/vendor/mudrd8mz/moodle-tool_pluginskel/classes/local/util/exception.php');
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
        $modname = $this->arguments[0];
        $modpath = $this->topDir.'/mod/'.$modname;
        if (file_exists($modpath)) {
            cli_problem("Already exists: '$modpath'");
            cli_problem("Not creating new module ".$this->arguments[0]);
            exit(1);
        }

        echo "Generating module $modname based on tool_pluginskel\n";

        $recipe = array(
                'copyright' => '2018 Your Name <you@example.com>',
                'component' => "$modname",
                'release' => '0.1.0',
                'version' => date('Ymd00'),
                'requires' => '2017111300',
                'maturity' => 'MATURITY_ALPHA',
                'name' => "Module $modname",
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
                'lang_strings' =>
                        array (
                                array (
                                        'id' => "{$modname}name",
                                        'text' => $modname
                                ),
                                array (
                                        'id' => "{$modname}name_help",
                                        'text' => $modname
                                ),
                                array (
                                        'id' => "{$modname}settings",
                                        'text' => "{$modname}settings"
                                ),
                                array (
                                        'id' => "{$modname}fieldset",
                                        'text' => "{$modname}fieldset"
                                ),
                                array (
                                        'id' => "missingidandcmid",
                                        'text' => "Missing id and cmid"
                                ),
                                array (
                                        'id' => "modulename",
                                        'text' => $modname
                                ),
                                array (
                                        'id' => "modulename_help",
                                        'text' => $modname
                                ),
                                array (
                                        'id' => "modulenameplural",
                                        'text' => $modname
                                ),
                                array (
                                        'id' => "nonewmodules",
                                        'text' => "No new modules"
                                ),
                                array (
                                        'id' => "pluginadministration",
                                        'text' => "Plugin administration"
                                ),
                                array (
                                        'id' => "view",
                                        'text' => "View"
                                ),
                        ),
        );

        $logger = new \Monolog\Logger('tool_pluginskel');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', constant('\Monolog\Logger::NOTICE')));
        $manager = \tool_pluginskel\local\util\manager::instance($logger,$this->mooshDir . '/vendor/mudrd8mz/moodle-tool_pluginskel/skel');
        $manager->load_recipe($recipe);
        $manager->make();
        $manager->write_files($modpath);

        $installfile = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<XMLDB PATH=\"mod/$modname/db\" VERSION=\"20101203\" COMMENT=\"XMLDB file for Moodle mod/$modname\"
    xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
    xsi:noNamespaceSchemaLocation=\"../../../lib/xmldb/xmldb.xsd\"
>
  <TABLES>
    <TABLE NAME=\"$modname\" COMMENT=\"Default comment for $modname, please edit me\">
      <FIELDS>
        <FIELD NAME=\"id\" TYPE=\"int\" LENGTH=\"10\" NOTNULL=\"true\" UNSIGNED=\"true\" SEQUENCE=\"true\"/>
        <FIELD NAME=\"course\" TYPE=\"int\" LENGTH=\"10\" NOTNULL=\"true\" UNSIGNED=\"true\" SEQUENCE=\"false\" COMMENT=\"Course newmodule activity belongs to\"/>
        <FIELD NAME=\"name\" TYPE=\"char\" LENGTH=\"255\" NOTNULL=\"true\" SEQUENCE=\"false\" COMMENT=\"name field for moodle instances\"/>
        <FIELD NAME=\"intro\" TYPE=\"text\" NOTNULL=\"true\" SEQUENCE=\"false\" COMMENT=\"General introduction of the newmodule activity\"/>
        <FIELD NAME=\"introformat\" TYPE=\"int\" LENGTH=\"4\" NOTNULL=\"true\" UNSIGNED=\"true\" DEFAULT=\"0\" SEQUENCE=\"false\" COMMENT=\"Format of the intro field (MOODLE, HTML, MARKDOWN...)\"/>
        <FIELD NAME=\"timecreated\" TYPE=\"int\" LENGTH=\"10\" NOTNULL=\"true\" UNSIGNED=\"true\" SEQUENCE=\"false\"/>
        <FIELD NAME=\"timemodified\" TYPE=\"int\" LENGTH=\"10\" NOTNULL=\"true\" UNSIGNED=\"true\" DEFAULT=\"0\" SEQUENCE=\"false\"/>
        <FIELD NAME=\"grade\" TYPE=\"int\" LENGTH=\"10\" NOTNULL=\"true\" DEFAULT=\"100\" SEQUENCE=\"false\" COMMENT=\"The maximum grade. Can be negative to indicate the use of a scale.\"/>
      </FIELDS>
      <KEYS>
        <KEY NAME=\"primary\" TYPE=\"primary\" FIELDS=\"id\"/>
      </KEYS>
      <INDEXES>
        <INDEX NAME=\"course\" UNIQUE=\"false\" FIELDS=\"course\"/>
      </INDEXES>
    </TABLE>
  </TABLES>
</XMLDB>
";

        file_put_contents($modpath . '/db/install.xml', $installfile);
    }
}
