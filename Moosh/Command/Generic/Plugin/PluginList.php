<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Kacper Golewski <k.golewski@gmail.com>
 * @author     Andrej Vitez <contact@andrejvitez.com>
 */

namespace Moosh\Command\Generic\Plugin;

use Moosh\MooshCommand;

class PluginList extends MooshCommand {
    static $APIURL = "https://download.moodle.org/api/1.3/pluglist.php";

    public function __construct() {
        parent::__construct('list', 'plugin');

        $this->addOption('p|path:', 'path to plugins.json file', home_dir() . '/.moosh/plugins.json');
        $this->addOption('v|versions', 'display plugin versions instead of supported moodle versions');
        $this->addOption('n|name-only', 'display only the frankenstyle name');
        $this->addOption('r|proxy:', 'Proxy URI scheme. Example: tcp://user:pass@host:port. You may also use env var http_proxy.');
    }

    public function execute() {
        $filepath = $this->expandedOptions['path'];

        $stat = null;
        if (file_exists($filepath)) {
            $stat = stat($filepath);
        }
        if (!$stat || time() - $stat['mtime'] > 60 * 60 * 24 || !$stat['size']) {
            @unlink($filepath);
            file_put_contents($filepath, file_get_contents(self::$APIURL, false, $this->createProxyContext()));
        }

        $jsonfile = file_get_contents($filepath);

        if ($jsonfile === false) {
            die("Can't read json file");
        }

        $data = json_decode($jsonfile);
        if (!$data) {
            unlink($filepath);
            cli_error("Invalid JSON file, deleted $filepath. Run command again.");
        }
        $fulllist = array();
        foreach ($data->plugins as $k => $plugin) {
            $highestpluginversion = 0;
            if (!$plugin->component) {
                continue;
            }
            $fulllist[$plugin->component] = array('releases' => array(), 'latestversion' => "");
            foreach ($plugin->versions as $v => $version) {
                if ($version->version >= $highestpluginversion) {
                    $highestpluginversion = $version->version;
                    $fulllist[$plugin->component]['latestversion'] = $version;

                    if ($this->expandedOptions['versions']) {
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
        foreach ($fulllist as $pluginname => $plugin) {
            if ($this->expandedOptions['name-only']) {
                echo "$pluginname\n";
                continue;
            }
            $versions = array_keys($plugin['releases']);
            sort($versions);

            echo "$pluginname," . implode(",", $versions) . "," . $plugin['url'] . "\n";
        }
    }

    /**
     * @return resource|null
     */
    private function createProxyContext() {
        $proxyUrl = !empty($this->expandedOptions['proxy'])
            ? $this->expandedOptions['proxy']
            : (getenv('http_proxy') ? getenv('http_proxy') : (getenv('HTTP_PROXY') ?: null));

        if (!$proxyUrl) {
            return null;
        }

        $uriParts = parse_url($proxyUrl);
        $httpConfig = [
            'proxy' => sprintf(
                '%s://%s%s',
                $uriParts['scheme'] ?? 'tcp',
                $uriParts['host'],
                empty($uriParts['port']) ? '443' : ':' . $uriParts['port']
            ),
            'request_fulluri' => true,
        ];

        if (!empty($uriParts['user']) && !empty($uriParts['pass'])) {
            $authEncoded = base64_encode($uriParts['user'] . ':' . $uriParts['pass']);
            $httpConfig['header'] = 'Proxy-Authorization: Basic ' . $authEncoded;
        }

        return stream_context_create(['http' => $httpConfig]);
    }

    public function bootstrapLevel() {
        return self::$BOOTSTRAP_NONE;
    }

    public function requireHomeWriteable() {
        return true;
    }
}
