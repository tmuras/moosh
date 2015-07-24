<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Kacper Golewski <k.golewski@gmail.com>
 */

namespace Moosh\Command\Generic\Plugin;
use Moosh\MooshCommand;

class PluginInstall extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('install', 'plugin');

        $this->addArgument('plugin_name');
        $this->addArgument('moodle_version');
    }

    public function execute()
    {
        global $CFG;

        require_once($CFG->libdir.'/adminlib.php');       // various admin-only functions
        require_once($CFG->libdir.'/upgradelib.php');     // general upgrade/install related functions
        require_once($CFG->libdir.'/environmentlib.php');
        require_once($CFG->dirroot.'/course/lib.php');
        require_once($CFG->libdir.'/classes/plugin_manager.php');

        $pluginname = $this->arguments[0];
        $moodleversion = $this->arguments[1];
        $pluginsfile = home_dir() . '/.moosh/plugins.json';

        $stat = @stat($pluginsfile);
        if(!$stat || time() - $stat['mtime'] > 60*60*24 || !$stat['size']) {
            die("plugins.json file not found or too old. Run moosh file-list to download newest plugins.json file\n");
        }

        $pluginsdata = file_get_contents($pluginsfile);
        $decodeddata = json_decode($pluginsdata);
        foreach($decodeddata->plugins as $k=>$plugin) {
            if(!$plugin->component) {
                continue;
            }
            if($plugin->component == $pluginname) {
                foreach($plugin->versions as $j) {
                    foreach($j->supportedmoodles as $v) {
                        if($v->release == $moodleversion) {
                            $downloadurl = $j->downloadurl;
                        }
                    }
                }
            }
        }

        /* echo "Couldn't find $pluginname\n"; */
        /* die(); */

        $split = explode('_',$this->arguments[0],2);
        $tempdir = home_dir() . '/.moosh/moodleplugins/';

        if (!fopen($tempdir . $split[1] . ".zip", 'w')) {
            echo "Failed to save plugin.\n";
            return;
        }
        try {
            file_put_contents($tempdir . $split[1] . ".zip", file_get_contents($downloadurl));
        }
        catch (Exception $e) {
            echo "Failed to download plugin. " . $e . "\n";
            return;
        }

        try {
            shell_exec("unzip " . $tempdir . $split[1] . ".zip -d " . home_dir() . "/.moosh/moodleplugins/");

            shell_exec("cp -r " . $tempdir . $split[1] . "/ " . $CFG->dirroot.  "/" . $split[0]);
        } catch (Exception $e) {
            echo "Failed to unzip plugin. " . $e . "\n";
            return;
        }

        upgrade_noncore(true);
    }

}
