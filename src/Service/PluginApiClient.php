<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Service;

/**
 * Client for the moodle.org plugin directory API.
 *
 * Fetches and caches the plugin list, resolves compatible versions,
 * and downloads plugin ZIP files.
 */
final class PluginApiClient
{
    private const API_URL = 'https://download.moodle.org/api/1.3/pluglist.php';
    private const CACHE_TTL = 86400; // 24 hours

    private ?string $proxy;

    public function __construct(?string $proxy = null)
    {
        $this->proxy = $proxy;
    }

    /**
     * Return the full plugin list from the API (cached locally).
     */
    public function getPluginList(bool $forceRefresh = false): object
    {
        $cachePath = self::getCachePath();
        $cacheDir = dirname($cachePath);

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $needsRefresh = $forceRefresh;
        if (!$needsRefresh) {
            if (!file_exists($cachePath)) {
                $needsRefresh = true;
            } else {
                $stat = stat($cachePath);
                if (!$stat || !$stat['size'] || (time() - $stat['mtime'] > self::CACHE_TTL)) {
                    $needsRefresh = true;
                }
            }
        }

        if ($needsRefresh) {
            $content = file_get_contents(self::API_URL, false, $this->createStreamContext());
            if ($content === false) {
                throw new \RuntimeException('Failed to fetch plugin list from ' . self::API_URL);
            }
            file_put_contents($cachePath, $content);
        }

        $json = file_get_contents($cachePath);
        if ($json === false) {
            throw new \RuntimeException("Cannot read cache file: $cachePath");
        }

        $data = json_decode($json);
        if (!$data) {
            @unlink($cachePath);
            throw new \RuntimeException("Invalid JSON in cache file (deleted). Run command again.");
        }

        return $data;
    }

    /**
     * Find a plugin by its frankenstyle component name.
     */
    public function findPlugin(string $component): ?object
    {
        $data = $this->getPluginList();

        foreach ($data->plugins as $plugin) {
            if (!empty($plugin->component) && $plugin->component === $component) {
                return $plugin;
            }
        }

        return null;
    }

    /**
     * Find the best version of a plugin for the given Moodle release.
     *
     * @param string      $component      Frankenstyle name (e.g. mod_attendance)
     * @param string      $moodleRelease  Moodle major version (e.g. "4.5")
     * @param string|null $pluginVersion  Specific plugin version, or null for latest
     * @param bool        $force          Allow unsupported versions
     * @return object     Version object with downloadurl, version, supportedmoodles, etc.
     */
    public function findBestVersion(
        string $component,
        string $moodleRelease,
        ?string $pluginVersion = null,
        bool $force = false,
    ): object {
        $plugin = $this->findPlugin($component);
        if ($plugin === null) {
            throw new \RuntimeException("Plugin '$component' not found in the moodle.org directory.");
        }

        $bestVersion = null;
        $altVersion = null;

        foreach ($plugin->versions as $version) {
            $supported = $this->isSupportedByMoodle($version, $moodleRelease);

            if ($pluginVersion !== null) {
                if ((string) $version->version === $pluginVersion) {
                    if ($supported) {
                        $bestVersion = $version;
                    } else {
                        $altVersion = $version;
                    }
                }
            } else {
                // Latest: pick the highest supported version
                if ($supported && (!$bestVersion || $version->version > $bestVersion->version)) {
                    $bestVersion = $version;
                } elseif (!$altVersion || $version->version > $altVersion->version) {
                    $altVersion = $version;
                }
            }
        }

        if ($bestVersion) {
            return $bestVersion;
        }

        if ($altVersion && $force) {
            return $altVersion;
        }

        if ($altVersion) {
            throw new \RuntimeException(
                "Plugin '$component' is not supported for Moodle $moodleRelease. "
                . "Use --force to install an unsupported version."
            );
        }

        $label = $pluginVersion ?? 'latest';
        throw new \RuntimeException("Could not find '$component' version $label.");
    }

    /**
     * Download a file from a URL to a local path.
     */
    public function downloadFile(string $url, string $targetPath): void
    {
        $content = file_get_contents($url, false, $this->createStreamContext());
        if ($content === false) {
            throw new \RuntimeException("Failed to download from $url");
        }

        if (file_put_contents($targetPath, $content) === false) {
            throw new \RuntimeException("Failed to write to $targetPath");
        }
    }

    /**
     * Path to the local plugins.json cache file.
     */
    public static function getCachePath(): string
    {
        $home = getenv('HOME') ?: (getenv('USERPROFILE') ?: '/tmp');
        return $home . '/.moosh/plugins.json';
    }

    private function isSupportedByMoodle(object $version, string $moodleRelease): bool
    {
        foreach ($version->supportedmoodles as $supported) {
            if ((string) $supported->release === $moodleRelease) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return resource
     */
    private function createStreamContext()
    {
        $httpConfig = [
            'method' => 'GET',
            'header' => "User-Agent: moosh2\r\n"
                . "Accept: application/json\r\n"
                . "Connection: close\r\n",
            'request_fulluri' => true,
        ];

        $proxyUrl = $this->proxy
            ?? (getenv('http_proxy') ?: (getenv('HTTP_PROXY') ?: null));

        if ($proxyUrl) {
            $uriParts = parse_url($proxyUrl);
            $httpConfig['proxy'] = sprintf(
                '%s://%s%s',
                $uriParts['scheme'] ?? 'tcp',
                $uriParts['host'],
                empty($uriParts['port']) ? '' : ':' . $uriParts['port'],
            );

            if (!empty($uriParts['user']) && !empty($uriParts['pass'])) {
                $authEncoded = base64_encode($uriParts['user'] . ':' . $uriParts['pass']);
                $httpConfig['header'] .= 'Proxy-Authorization: Basic ' . $authEncoded . "\r\n";
            }
        }

        return stream_context_create(['http' => $httpConfig]);
    }
}
