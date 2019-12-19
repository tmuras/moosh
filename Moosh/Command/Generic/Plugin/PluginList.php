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

class PluginList extends MooshCommand
{
    static $APIURL = "https://download.moodle.org/api/1.3/pluglist.php";

    public function __construct()
    {
        parent::__construct('list', 'plugin');

        $this->addOption('p|path:', 'path to plugins.json file', home_dir() . '/.moosh/plugins.json');
        $this->addOption('v|versions', 'display plugin versions instead of supported moodle versions');
        $this->addOption('n|name-only', 'display only the frankenstyle name');
    }

    public function execute()
    {

        $filepath = $this->expandedOptions['path'];

        $stat = NULL;
        if(file_exists($filepath)) {
            $stat = stat($filepath);
        }
        if(!$stat || time() - $stat['mtime'] > 60*60*24 || !$stat['size']) {
            @unlink($filepath);
            file_put_contents($filepath, fopen(self::$APIURL, 'r'));
        }
        $jsonfile = file_get_contents($filepath);

        if($jsonfile === false) {
            die("Can't read json file");
        }

        $data = json_decode($jsonfile);
        if(!$data) {
            unlink($filepath);
            cli_error("Invalid JSON file, deleted $filepath. Run command again.");
        }
        $fulllist = array();
        foreach($data->plugins as $k=>$plugin) {
            $highestpluginversion = 0;
            if (!$plugin->component) {
                continue;
            }
            $fulllist[$plugin->component] = array('releases' => array(), 'latestversion' => "");
            foreach ($plugin->versions as $v => $version) {
                if ($version->version >= $highestpluginversion) {
                    $highestpluginversion = $version->version;
                    $fulllist[$plugin->component]['latestversion'] = $version;

                    if($this->expandedOptions['versions']) {
                        $fulllist[$plugin->component]['releases'][$version->version] = $version;
                    } else {
                        foreach ($version->supportedmoodles as $supportedmoodle) {
                            $fulllist[$plugin->component]['releases'][$supportedmoodle->release] = $version;
                        }
                    }
                }
            }
            $fulllist[$plugin->component]['url'] = $fulllist[$plugin->component]['latestversion']->downloadurl;
        }


        ksort($fulllist);
        foreach($fulllist as $pluginname => $plugin) {
            if($this->expandedOptions['name-only']) {
                echo "$pluginname\n";
                continue;
            }
            $versions = array_keys($plugin['releases']);
            sort($versions);

            echo "$pluginname," .implode(",",$versions) . ",".$plugin['url'] ."\n";
        }
    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_NONE;
    }

    public function requireHomeWriteable() {
        return true;
    }
}
