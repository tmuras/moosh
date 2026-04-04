<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Bootstrap;

/**
 * Parses Moodle's version.php and provides version-comparison helpers.
 *
 * Replaces moosh_moodle_version() and the per-directory version scheme.
 */
final class MoodleVersion
{
    private string $branch;
    private string $release;
    private float $numericVersion;

    private function __construct(string $branch, string $release, float $numericVersion)
    {
        $this->branch = $branch;
        $this->release = $release;
        $this->numericVersion = $numericVersion;
    }

    /**
     * Parse Moodle's version.php from the given root directory.
     */
    public static function fromMoodleDir(string $moodleDir): self
    {
        $versionFile = $moodleDir . '/version.php';
        if (!file_exists($versionFile)) {
            throw new \RuntimeException("version.php not found in $moodleDir");
        }

        $branch = '';
        $release = '';
        $version = 0;

        // Parse the file line-by-line to extract $branch, $release, and $version.
        // We avoid require/include to prevent executing arbitrary Moodle code at this stage.
        $contents = file_get_contents($versionFile);

        if (preg_match('/\$branch\s*=\s*\'(\d+)\'\s*;/', $contents, $m)) {
            $branch = $m[1];
        }
        if (preg_match('/\$release\s*=\s*\'([^\']+)\'\s*;/', $contents, $m)) {
            $release = $m[1];
        }
        if (preg_match('/\$version\s*=\s*([\d.]+)\s*;/', $contents, $m)) {
            $version = (float) $m[1];
        }

        return new self($branch, $release, $version);
    }

    /**
     * Check whether the detected Moodle version is at least $minVersion.
     *
     * Accepts branch-style strings like '405' (Moodle 4.5) or dot-notation
     * like '4.5' or '5.2'.
     */
    public function isAtLeast(string $minVersion): bool
    {
        $normalisedCurrent = $this->normaliseToDot($this->branch);
        $normalisedMin = $this->normaliseToDot($minVersion);

        return version_compare($normalisedCurrent, $normalisedMin, '>=');
    }

    /**
     * Return the raw branch string (e.g. '405', '502').
     */
    public function getBranch(): string
    {
        return $this->branch;
    }

    /**
     * Return the human-readable release string (e.g. '4.5.1 (Build: 20241120)').
     */
    public function getRelease(): string
    {
        return $this->release;
    }

    /**
     * Return the numeric version (e.g. 2024112000.00).
     */
    public function getNumericVersion(): float
    {
        return $this->numericVersion;
    }

    /**
     * Normalise a version to dot notation for comparison.
     *
     * '405' → '4.05', '52' → '5.2', '4.5' → '4.5', '5.2' → '5.2'.
     */
    private function normaliseToDot(string $version): string
    {
        // Already in dot notation.
        if (str_contains($version, '.')) {
            return $version;
        }

        $len = strlen($version);
        if ($len <= 2) {
            // e.g. '52' → '5.2'
            return $version[0] . '.' . substr($version, 1);
        }

        // e.g. '405' → '4.05', '4500' → '45.00'
        // Moodle convention: first digit(s) = major, last two digits = minor.
        $minor = substr($version, -2);
        $major = substr($version, 0, -2);

        return $major . '.' . $minor;
    }
}
