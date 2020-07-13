<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle35\Info;

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
        require_once($CFG->libdir . '/questionlib.php');
        $result = array();
        $options = $this->expandedOptions;
        $plugincheck = $this->arguments[0];

        $result = null;

        if (strpos($plugincheck, 'assignsubmission_') === 0) {
            $result = $this->get_assignsubmission_stats($plugincheck);
        }
        $this->display($result, false, false);

        if($plugincheck != 'all') {
            return;
        }

        // filters
        //filter - key
        $standard = core_plugin_manager::standard_plugins_list('filter');
        $plugininfos = core_plugin_manager::instance()->get_plugins_of_type('filter');
        $states = filter_get_global_states();
        $pluginnames = array_keys($plugininfos);
        $checkcontribonly = false;
        if (isset($options['contribonly']) && (int) $options['contribonly'] > 0) {
            $checkcontribonly = true;
        }

        foreach ($pluginnames as $pluginname) {
            if ($checkcontribonly && in_array($pluginname, $standard)) {
                continue;
            }
            if (isset($states[$pluginname]) && $states[$pluginname]->active != TEXTFILTER_DISABLED) {
                $active = true;
            } else {
                $active = false;
            }

            $result['FILTER'][$plugininfos[$pluginname]->displayname][] = ($active ? 'ON' : 'disabled');
        }

        // question type
        //qtype - key
        $standard = core_plugin_manager::standard_plugins_list('qtype');
        $counts = $DB->get_records_sql("
        SELECT qtype, COUNT(1) as numquestions, SUM(hidden) as numhidden
        FROM {question} GROUP BY qtype", array());
        $qtypes = question_bank::get_all_qtypes();

        foreach ($qtypes as $qtypename => $qtype) {
            $this->getExtendedListRequirements($qtype->plugin_name());
        }

        foreach ($qtypes as $qtypename => $qtype) {
            if ($checkcontribonly && in_array($qtypename, $standard)) {
                continue;
            }
            if (!isset($counts[$qtypename])) {
                $counts[$qtypename] = new \stdClass;
                $counts[$qtypename]->numquestions = 0;
                $counts[$qtypename]->numhidden = 0;
            }
            $count = $counts[$qtypename]->numquestions - $counts[$qtypename]->numhidden;
            $result['QUESTION TYPES'][$qtype->local_name()]['No. questions'] = $count;
            if ($counts[$qtypename]->numhidden > 0) {
                $result['QUESTION TYPES'][$qtype->local_name()]['Hidden questions'] = $counts[$qtypename]->numhidden;
            }
            $needed = $this->needed[$qtype->plugin_name()];

            if (!empty($needed) && is_array($needed)) {
                $requiredBy = array();
                foreach ($needed as $need) {
                    $pluginname = explode('_', $need);
                    $pluginname = $pluginname[1];
                    if (isset($counts[$pluginname]) && $counts[$pluginname]->numquestions > 0) {
                        $requiredBy[] = $qtypes[$pluginname]->local_name();
                    }
                }

                if (!empty($requiredBy)) {
                    $result['QUESTION TYPES'][$qtype->local_name()]['needed'] = implode(', ', $requiredBy);
                }
            }
        }

        // course format
        //format - key
        $standard = core_plugin_manager::standard_plugins_list('format');
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
        // enrol plugins
        //enrol - key
        $standard = core_plugin_manager::standard_plugins_list('enrol');
        $all = enrol_get_plugins(false);

        foreach (array_keys($all) as $enrol) {
            if ($checkcontribonly && in_array($enrol, $standard)) {
                continue;
            }
            $ci = $DB->count_records('enrol', array('enrol' => $enrol));
            $cp = $DB->count_records_select('user_enrolments', "enrolid IN (SELECT id FROM {enrol} WHERE enrol = ?)", array($enrol));
            $usage = "$ci / $cp";
            if (get_string_manager()->string_exists('pluginname', 'enrol_' . $enrol)) {
                $name = get_string('pluginname', 'enrol_' . $enrol);
            } else {
                $name = $enrol;
            }
            $result['ENROLS (Instances / enrolments)'][$name][] = $usage;
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

        $this->display($result);
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

    /**
     * Returns extended list of all plugins required for this plugin not only from firs panet level but from all
     *
     * @param string $qtypeName
     */
    protected function getExtendedListRequirements($qtypeName) {
        $pluginmanager = core_plugin_manager::instance();
        $needed = $pluginmanager->other_plugins_that_require($qtypeName);

        if (!isset($this->needed[$qtypeName])) {
            $this->needed[$qtypeName] = array();
        }

        if (!empty($needed)) {
            foreach ($needed as $need) {
                if (empty($this->needed[$need])) {
                    $this->getExtendedListRequirements($need);
                }
                $this->needed[$qtypeName][] = $need;
                $this->needed[$qtypeName] = array_unique(array_merge($this->needed[$qtypeName], $this->needed[$need]));
            }
        }
    }

    protected function get_assignsubmission_stats($plugincheck) {
        global $DB;
        $stats = [];

        $name = explode('_', $plugincheck, 2);
        $name = $name[1];

        // Check mdl_assign_plugin_config if it's configured for use anywhere.

        //        $todogroups = $DB->get_records_sql('SELECT id FROM {group} WHERE ' . $DB->sql_compare_text('description') . ' = ' . $DB->sql_compare_text(':description'), ['description' => 'TODO']);
        $record =
                $DB->get_record_sql("SELECT COUNT(*) AS 'c' FROM {assign_plugin_config} WHERE plugin = :name AND subtype = 'assignsubmission' AND name = 'enabled' AND value = '1'",
                        ['name' => $name]);
        $stats['enabled'] = $record->c;
        //$stats['enabled'] = $DB->count_records('assign_plugin_config', ['plugin' => $name, 'subtype' => 'assignsubmission', 'name' => 'enabled', 'value' => 1]);

        // Check number of submissions in table like assignsubmission_<type>
        $stats['submitted'] = $DB->count_records("assignsubmission_$name");

        return $stats;
    }
}
