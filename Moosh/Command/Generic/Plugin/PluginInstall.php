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
    private $moodlerelease;
    private $moodleversion;

    public function __construct()
    {
        parent::__construct('install', 'plugin');

        $this->addArgument('plugin_name');

        $this->addOption('r|release:', 'Specify exact version to install e.g. 2019010700');
        $this->addOption('f|force', 'Force installation even if current Moodle version is unsupported.');
        $this->addOption('d|delete', 'If it already exists, automatically delete plugin before installing.');
    }

    private function init()
    {
        global $CFG;
        $this->moodlerelease = moodle_major_version();
        if (!is_float($this->moodlerelease)) {
            $this->moodlerelease = floatval($this->moodlerelease);
        }

        $this->moodleversion = $CFG->version;
    }

    public function execute()
    {
        global $CFG;

        require_once($CFG->libdir.'/adminlib.php');       // various admin-only functions
        require_once($CFG->libdir.'/upgradelib.php');     // general upgrade/install related functions
        require_once($CFG->libdir.'/environmentlib.php');
        require_once($CFG->dirroot.'/course/lib.php');

        $this->init();

        $pluginname     = $this->arguments[0];
        $pluginversion  = null;
        if (!empty($this->expandedOptions['release'])) {
            $pluginversion  = $this->expandedOptions['release'];
        }

        $version        = $this->get_plugin_to_install($pluginname, $pluginversion);
        $downloadurl    = $version->downloadurl;

        $split          = explode('_', $pluginname, 2);
        $type           = $split[0];
        $component      = $split[1];
        $tempdir        = home_dir() . '/.moosh/moodleplugins/';
        $downloadedfile = $tempdir . $component . ".zip";

        if (!file_exists($tempdir)) {
            mkdir($tempdir);
        }

        if (!fopen($downloadedfile, 'w')) {
            echo "Failed to save plugin - check permissions on $tempdir.\n";
            return;
        }

        try {
            file_put_contents($downloadedfile, file_get_contents($downloadurl));
        }
        catch (Exception $e) {
            die("Failed to download plugin from $downloadurl. " . $e . "\n");
        }

        $installpath = $this->get_install_path($type);
        $targetpath = $installpath . DIRECTORY_SEPARATOR . $component;

        if (file_exists($targetpath)) {
            if ($this->expandedOptions['delete']) {
                echo "Removing previously installed $pluginname from $targetpath\n";
                run_external_command("rm -rf $targetpath");
            } else {
                die("Something already exists at $targetpath - please remove it and try again, or run with the -d option.\n");
            }
        }

        run_external_command("unzip $downloadedfile -d $installpath");
        run_external_command("rm $downloadedfile");

        echo "Installing\n";
        echo "\tname:    $pluginname\n";
        echo "\tversion: $version->version\n";
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
    private function get_install_path($type)
    {
        global $CFG;

        if ($this->moodlerelease >= 2.6) {
            $types = \core_component::get_plugin_types();
        } else if ($this->moodlerelease >= 2.0) {
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

    /**
     * Get the best version to install.
     *
     * @param string $pluginname
     *   The plugin to install (example: 'block_checklist', 'format_topcoll')
     * @param string|null $pluginversion
     *   The version of the plugin to install (example: '2019010700') or null for the latest
     * @return stdClass $version
     *   The info about the version to install, download url etc
     */
    private function get_plugin_to_install($pluginname, $pluginversion = null) {
        $pluginlist = $this->get_plugins_data();

        if ($pluginversion === null) {
            $pluginversion = 'latest';
        }
        foreach ($pluginlist->plugins as $plugin) {
            if (!$plugin->component) {
                continue;
            }
            if ($plugin->component == $pluginname) {
                $bestversion = false;
                $altversion = false;
                foreach ($plugin->versions as $version) {
                    if ($version->version == $pluginversion) {
                        if ($this->is_supported_by_moodle($version)) {
                            $bestversion = $version;
                        } else {
                            $altversion = $version;
                        }
                    } else if ($pluginversion == 'latest') {
                        if ($this->is_supported_by_moodle($version) &&
                            (!$bestversion || $version->version > $bestversion->version)) {
                            $bestversion = $version;
                        } else if (!$altversion || $version->version > $altversion->version) {
                            $altversion = $version;
                        }
                    }
                }
                if (!$this->expandedOptions['force'] && !$bestversion && $altversion) {
                    $message =
                            "This plugin is not supported for your Moodle version (release $this->moodlerelease - version $this->moodleversion). ";
                    $message .= "Specify a different plugin version, or use the -f flag to force installation of (this) unsupported version.\n";
                    die($message);
                }

                if ($bestversion) {
                    return $bestversion;
                } else if ($altversion) {
                    return $altversion;
                }
            }
        }

        die("Couldn't find $pluginname $pluginversion\n");
    }

    private function is_supported_by_moodle($version)
    {
        foreach ($version->supportedmoodles as $supported) {
            if ($this->moodlerelease == $supported->release && $this->moodleversion >= $supported->version) {
                return true;
            }
        }
        return false;
    }

    private function get_plugins_data()
    {
        $pluginsfile = home_dir() . '/.moosh/plugins.json';

        $stat = @stat($pluginsfile);
        if (!$stat || time() - $stat['mtime'] > 60*60*24 || !$stat['size']) {
            die("plugins.json file not found or too old. Run moosh plugin-list to download newest plugins.json file\n");
        }

        $pluginsdata = file_get_contents($pluginsfile);
        $decodeddata = json_decode($pluginsdata);

        return $decodeddata;
    }

    public function requireHomeWriteable() {
        return true;
    }
}

