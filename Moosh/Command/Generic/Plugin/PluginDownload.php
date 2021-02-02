<?php
/**
 * Download plugin for a given Moodle version to current directory.
 * moosh plugin-download [-v, --version] [-u, --url] <plugin_name>
 *
 * Download block_fastnav for moodle 3.9 into block_fastnav.zip
 * @example moosh plugin-download -v 3.9 block_fastnav
 *
 * Only show link for block_fastnav moodle 3.9
 * @example moosh plugin-download -v 3.9 -u block_fastnav
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @introduced 2020-12-23
 * @author     Jakub Kleban <jakub.kleban2000@gmail.com>
 */

namespace Moosh\Command\Generic\Plugin;
use Moosh\MooshCommand;

class PluginDownload extends MooshCommand
{
    private $moodlerelease;

    public function __construct()
    {
        parent::__construct('download', 'plugin');

        $this->addArgument('plugin_name');

        $this->addOption('v|version:', 'Moodle major version (eg. 3.9)');
        $this->addOption('u|url', 'Only display the download URL.');
    }

    private function setupRelease()
    {
        if (!empty($this->expandedOptions['version'])) {
            $this->moodlerelease = $this->expandedOptions['version'];
        }
        else {
            global $CFG;
            require_once($CFG->libdir.'/adminlib.php');       // various admin-only functions
            require_once($CFG->libdir.'/upgradelib.php');     // general upgrade/install related functions
            require_once($CFG->libdir.'/environmentlib.php');
            require_once($CFG->dirroot.'/course/lib.php');

            $this->moodlerelease = moodle_major_version();
        }
    }

    public function execute()
    {
        $this->setupRelease();

        $pluginname     = $this->arguments[0];
        $pluginversion  = null;
        //if (!empty($this->expandedOptions['plugin-version'])) {
        //    $pluginversion  = $this->expandedOptions['plugin-version'];
        //}

        $version        = $this->get_plugin_to_install($pluginname, $pluginversion);
        $downloadurl    = $version->downloadurl;

        if (!empty($this->expandedOptions['url'])) {
            cli_error("$downloadurl\n");
        }

        $tempdir        = $this->cwd;
        $downloadedfile = $tempdir . '/' . $pluginname . ".zip";

        if (!fopen($downloadedfile, 'w')) {
            cli_error("Failed to save plugin - check permissions on $tempdir.\n");
        }

        try {
            file_put_contents($downloadedfile, file_get_contents($downloadurl));
        }
        catch (Exception $e) {
            die("Failed to download plugin from $downloadurl. " . $e . "\n");
        }

        echo "Downloaded:\n";
        echo "\tplugin:  $pluginname\n";
        echo "\tversion: $version->version\n";
        echo "\trelease: $this->moodlerelease\n";
        echo "\tfile:    $downloadedfile\n";
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
                if (!$bestversion && $altversion) {
                    $message =
                        "This plugin is not supported for your Moodle version (release $this->moodlerelease).\n";
                    die($message);
                }

                if ($bestversion) {
                    return $bestversion;
                } else if ($altversion) {
                    return $altversion;
                }
            }
        }

        die("Couldn't find $pluginname with version - $pluginversion\n");
    }

    private function is_supported_by_moodle($version)
    {
        foreach ($version->supportedmoodles as $supported) {
            if ($this->moodlerelease == $supported->release) {
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

    public function bootstrapLevel() {
        $argc = count($_SERVER['argv']);

        $nomoodleargv = ['-v', '--version', '-uv', '-h', '--help'];

        if (array_intersect($nomoodleargv, $_SERVER['argv'])) {
            return self::$BOOTSTRAP_NONE;
        }

        if ( $argc == 2 ) {
            return self::$BOOTSTRAP_NONE;
        }
    }

    public function requireHomeWriteable() {
        return true;
    }
}

