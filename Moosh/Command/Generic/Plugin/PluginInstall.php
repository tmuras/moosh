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
        $this->addArgument('plugin_version', ARG_GENERIC, true);
    }

    public function execute()
    {
        global $CFG;

        require_once($CFG->libdir.'/adminlib.php');       // various admin-only functions
        require_once($CFG->libdir.'/upgradelib.php');     // general upgrade/install related functions
        require_once($CFG->libdir.'/environmentlib.php');
        require_once($CFG->dirroot.'/course/lib.php');

        $pluginname = $this->arguments[0];
        $moodleversion = $this->arguments[1];
        if (sizeof($this->arguments) >= 3) {
            $pluginversion = $this->arguments[2];
        } else {
            $pluginversion = -1;                         // "use latest compatible version"
        }
        $pluginsfile = home_dir() . '/.moosh/plugins.json';

        $stat = @stat($pluginsfile);
        if(!$stat || time() - $stat['mtime'] > 60*60*24 || !$stat['size']) {
            die("plugins.json file not found or too old. Run moosh plugin-list to download newest plugins.json file\n");
        }

        $pluginsdata = file_get_contents($pluginsfile);
        $decodeddata = json_decode($pluginsdata);
        $downloadurl = NULL;

        $downloadurl = $this->get_plugin_url($decodeddata, $pluginname, $moodleversion, $pluginversion);

        if(!$downloadurl) {
            die("Couldn't find $pluginname $moodleversion\n");
        }

        $split = explode('_',$this->arguments[0],2);
        $tempdir = home_dir() . '/.moosh/moodleplugins/';

        if(!file_exists($tempdir)) {
            mkdir($tempdir);
        }

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
            shell_exec("cp -r " . $tempdir . $split[1] . "/ " . $this->get_install_path($split[0], $moodleversion));
        } catch (Exception $e) {
            echo "Failed to unzip plugin. " . $e . "\n";
            return;
        }

        echo "Installing $pluginname $moodleversion\n";
        upgrade_noncore(true);
        echo "Done\n";
    }
    
    /**
     * Get the relative path for a plugin given it's type
     * 
     * @param string $type
     *   The plugin type (example: 'auth', 'block')
     * @param string $moodleversion
     *   The version of moodle we are running (example: '1.9', '2.9')
     * @return string
     *   The installation path relative to dirroot (example: 'auth', 'blocks', 
     *   'course/format')
     */
    private function get_install_path($type, $moodleversion)
    {
        global $CFG;
        
        // Convert moodle version to a float for more acurate comparison
        if (!is_float($moodleversion)) {
            $moodleversion = floatval($moodleversion);
        }        
        
        if ($moodleversion >= 2.6) {
            $types = \core_component::get_plugin_types();
        } else if ($moodleversion >= 2.0) {
            $types = get_plugin_types();
        } else {
            // Moodle 1.9 does not give us a way to determine plugin 
            // installation paths.
            $types = array();
        }
        
        if (empty($types) || !array_key_exists($type, $types)) {
            // Either the moodle version is lower than 2.0, in which case we
            // don't have a reliable way of determining the install path, or the
            // plugin is of an unknown type.
            // 
            // Let's fall back to make our best guess.
            return $CFG->dirroot . '/' . $type; 
        }
        
        return $types[$type];
    }

    function get_plugin_url($pluginlist, $pluginname, $moodleversion, $pluginversion) {
        foreach($pluginlist->plugins as $k=>$plugin) {
            if(!$plugin->component) {
                continue;
            }
            if($plugin->component == $pluginname) {
                $downloadurl = null;
                foreach($plugin->versions as $j) {
                    foreach($j->supportedmoodles as $v) {
                        if($v->release == $moodleversion) {
                            # Record the url for the most recent (assumed to be highest) 
                            # version of plugin that is compatible with the given Moodle 
                            # version...
                            $downloadurl = $j->downloadurl;

                            # ...and return it if this version matches the given 
                            # version.
                            if ($pluginversion >= 0 && $v->version == $pluginversion) {
                                return $downloadurl;
                            } 
                        }
                    }
                }
                # Negative pluginversion indicates we should return the latest version
                # compatible with the given version of Moodle (recorded above)
                if ($pluginversion < 0 and ! is_null($downloadurl)) {
                    return $downloadurl;
                }
            }
        }
    }
}

