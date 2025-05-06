<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle41\Info;

use Moosh\Command\Moodle39\Info\dml_exception;
use Moosh\MooshCommand;
use core_plugin_manager;
use question_bank;
use core_component;
use core_collator;

class PluginsUsage extends MooshCommand {
    protected $needed;

    public function __construct() {
        parent::__construct('usage', 'plugins');
        $this->addOption('c|contribonly:', 'returns only non standard plugins when ==1 and all plugins when ==0');
        $this->addArgument('plugin_name');
    }

    public function execute() {
        global $CFG, $DB;

        $options = $this->expandedOptions;
        $plugincheck = $this->arguments[0];
        $checkcontribonly = false;
        if (isset($options['contribonly']) && (int) $options['contribonly'] > 0) {
            $checkcontribonly = true;
        }
        $result = null;

        // course format
        //format - key
        $standard = core_plugin_manager::standard_plugins_list('format');
        require_once ($CFG->dirroot .  '/course/lib.php');
        $courseformats = get_sorted_course_formats(true);
        foreach ($courseformats as $courseformat) {
            $formcourseformats[$courseformat] = get_string('pluginname', "format_$courseformat");
        }
        $courseformatusages = $DB->get_records_sql("
        SELECT format, count(*) as count
        FROM {course} WHERE id > 1 GROUP BY format", array());
        foreach ($formcourseformats as $formatKey => $formatName) {
            if ($checkcontribonly && in_array($formatKey, $standard)) {
                continue;
            }
            $result['COURSE FORMATS'][$formatName][] = (isset($courseformatusages[$formatKey]) ? $courseformatusages[$formatKey]->count : 0);
        }
       //blocks
        //block - key
        $standard = core_plugin_manager::standard_plugins_list('block');
        $blocks = $DB->get_records('block', array(), 'name ASC');
        $blocknames = array();
        foreach ($blocks as $blockid => $block) {
            $blockname = $block->name;
            if ($checkcontribonly && in_array($blockname, $standard)) {
                continue;
            }
            if (file_exists("$CFG->dirroot/blocks/$blockname/block_$blockname.php")) {
                $blocknames[$blockid] = get_string('pluginname', 'block_' . $blockname);
            } else {
                $blocknames[$blockid] = $blockname;
            }
        }
        core_collator::asort($blocknames);
        foreach ($blocknames as $blockid => $strblockname) {
            $block = $blocks[$blockid];
            $blockname = $block->name;
            $totalcount = $DB->count_records('block_instances', array('blockname' => $blockname));
            $result['BLOCKS'][$strblockname][] = $totalcount;
        }
        //authentication
        //auth - key
        $standard = core_plugin_manager::standard_plugins_list('auth');
        $authsavailable = core_component::get_plugin_list('auth');
        get_enabled_auth_plugins(true);
        if (empty($CFG->auth)) {
            $authsenabled = array();
        } else {
            $authsenabled = explode(',', $CFG->auth);
        }
        $displayauths = array();
        foreach ($authsenabled as $auth) {
            if ($checkcontribonly && in_array($auth, $standard)) {
                continue;
            }
            $authplugin = get_auth_plugin($auth);
            $authtitle = $authplugin->get_title();
            $displayauths[$auth] = $authtitle;
        }

        foreach ($authsavailable as $auth => $dir) {
            if ($checkcontribonly && in_array($auth, $standard)) {
                continue;
            }
            if (array_key_exists($auth, $displayauths)) {
                continue; //already in the list
            }
            $authplugin = get_auth_plugin($auth);
            $authtitle = $authplugin->get_title();
            $displayauths[$auth] = $authtitle;
        }

        $tmpRet = array();
        foreach ($displayauths as $auth => $name) {
            $usercount = $DB->count_records('user', array('auth' => $auth, 'deleted' => 0));
            $displayname = $name;

            if ($auth == 'manual' or $auth == 'nologin') {
                $tmpRet[$displayname][] = $usercount;
            } else {
                $result['AUTHENTICATION'][$displayname][] = $usercount;
            }
            $result['AUTHENTICATION'] = $tmpRet + $result['AUTHENTICATION'];
        }

        //Activities
        //mod - key
        $standard = core_plugin_manager::standard_plugins_list('mod');
        $modules = $DB->get_records('modules', array(), 'name ASC');
        foreach ($modules as $module) {
            if ($checkcontribonly && in_array($module->name, $standard)) {
                continue;
            }
            try {
                $count = $DB->count_records_select($module->name, "course<>0");
            } catch (dml_exception $e) {
                $count = 'error';
            }
            $result['ACTIVITIES'][get_string('modulename', $module->name)][] = $count;
        }

//        $this->display($result);
        foreach ($result as $category => $plugins) {
            echo '====' . $category . "====\n";
            foreach ($plugins as $name => $usages) {
                $showpluginname = true;
                foreach ($usages as $usageType => $usage) {
                    if ($usageType != '0') {
                        if ($showpluginname) {
                            echo $name . ":\n";
                        }

                        $retusagetype = str_pad($usageType, 30, '-', STR_PAD_RIGHT);
                        $retstr = '  ► ' . $retusagetype . ' ' . $usage . "\n";

                        $showpluginname = false;
                    } else {
                        $retname = str_pad($name, 30, '-', STR_PAD_RIGHT);
                        $retstr = $retname . ' ' . $usage . "\n";
                    }

                    echo $retstr;
                }
            }
        }
    }


}