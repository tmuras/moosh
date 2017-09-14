<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle31\Info;

use Moosh\MooshCommand;
use core_plugin_manager;
use question_bank;
use core_component;
use core_collator;

class PluginsUsage extends MooshCommand
{
    protected $needed;

    public function __construct()
    {
        parent::__construct('usage', 'plugins');
    }

    public function execute()
    {
        global $CFG, $DB;
        require_once($CFG->libdir . '/questionlib.php');
        $result = array();

        // filters
        $plugininfos = core_plugin_manager::instance()->get_plugins_of_type('filter');
        $states = filter_get_global_states();
        $pluginNames = array_keys($plugininfos);

        foreach ($pluginNames as $pluginName) {
            if (isset($states[$pluginName]) && $states[$pluginName]->active != TEXTFILTER_DISABLED) {
                $active = true;
            } else {
                $active = false;
            }

            $result['FILTER'][$plugininfos[$pluginName]->displayname][] = ($active ? 'ON' : 'disabled');

        }

        // question type
        $counts = $DB->get_records_sql("
        SELECT qtype, COUNT(1) as numquestions, SUM(hidden) as numhidden
        FROM {question} GROUP BY qtype", array());
        $qtypes = question_bank::get_all_qtypes();

        foreach ($qtypes as $qtypename => $qtype) {
            $this->getExtendedListRequirements($qtype->plugin_name());
        }

        foreach ($qtypes as $qtypename => $qtype) {
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
        $courseformats = get_sorted_course_formats(true);
        foreach ($courseformats as $courseformat) {
            $formcourseformats[$courseformat] = get_string('pluginname', "format_$courseformat");
        }
        $courseformatusages = $DB->get_records_sql("
        SELECT format, count(*) as count
        FROM {course} WHERE id > 1 GROUP BY format", array());
        foreach ($formcourseformats as $formatKey => $formatName) {
            $result['COURSE FORMATS'][$formatName][] = (isset($courseformatusages[$formatKey]) ? $courseformatusages[$formatKey]->count : 0);
        }
        // enrol plugins
        $all = enrol_get_plugins(false);

        foreach (array_keys($all) as $enrol) {
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
        $blocks = $DB->get_records('block', array(), 'name ASC');
        $blocknames = array();
        foreach ($blocks as $blockid => $block) {
            $blockname = $block->name;
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
        $authsavailable = core_component::get_plugin_list('auth');
        get_enabled_auth_plugins(true);
        if (empty($CFG->auth)) {
            $authsenabled = array();
        } else {
            $authsenabled = explode(',', $CFG->auth);
        }
        $displayauths = array();
        foreach ($authsenabled as $auth) {
            $authplugin = get_auth_plugin($auth);
            $authtitle = $authplugin->get_title();
            $displayauths[$auth] = $authtitle;
        }

        foreach ($authsavailable as $auth => $dir) {
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
        }
        $result['AUTHENTICATION'] = $tmpRet + $result['AUTHENTICATION'];

        //Activities
        $modules = $DB->get_records('modules', array(), 'name ASC');
        foreach ($modules as $module) {
            try {
                $count = $DB->count_records_select($module->name, "course<>0");
            } catch (dml_exception $e) {
                $count = 'error';
            }
            $result['ACTIVITIES'][get_string('modulename', $module->name)][] = $count;
        }


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
    protected function getExtendedListRequirements($qtypeName)
    {
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
}
