<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Dev;

use Moosh\MooshCommand;

class GenerateModule extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('module', 'generate');

        $this->addArgument('module_name');
	$this->addOption('c|copyright:', 'Your Name <your@email.address', "Your Name <you@example.com>");
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

	$copyright = date('Y') . ' ' . $this->expandedOptions['copyright'];
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
                'copyright' => $copyright,
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
		'capabilities' =>
			array (
				array (
					'name' => "addinstance",
					'title' => "$modname add instance",
					'riskbitmask' => 'RISK_XSS',
					'captype' => 'write',
					'contextlevel' => 'CONTEXT_COURSE',
					'archetypes' =>
					array (
						'role' => 'editingteacher',
						'permission' => 'CAP_ALLOW'
					),
				        'clonepermissionsfrom' => 'moodle/course:manageactivities'
				),
			)
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

	mkdir($modpath . '/classes/event', 0755, true);
	$eventfile = "<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Defines the view event.
 *
 * @package    mod_$modname
 * @copyright  $copyright
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_$modname\\event;
defined('MOODLE_INTERNAL') || die();
/**
 * The mod_newmodule instance viewed event class
 *
 * If the view mode needs to be stored as well, you may need to
 * override methods get_url() and get_legacy_log_data(), too.
 *
 * @package    mod_$modname
 * @copyright  $copyright
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_viewed extends \\core\\event\\course_module_viewed {
    /**
     * Initialize the event
     */
    protected function init() {
        \$this->data['objecttable'] = '$modname';
        parent::init();
    }
}";
	file_put_contents($modpath . '/classes/event/course_module_viewed.php', $eventfile);
    }
}
