<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Bootstrap;

use Moosh2\Output\VerboseLogger;

/**
 * Locates a Moodle installation by walking up the directory tree.
 *
 * Equivalent to the original find_top_moodle_dir() / is_top_moodle_dir().
 */
final class MoodlePathResolver
{
    private const MAX_DEPTH = 10;

    private ?VerboseLogger $verbose;

    public function __construct(?VerboseLogger $verbose = null)
    {
        $this->verbose = $verbose;
    }

    /**
     * Walk up from $startDir looking for a Moodle root (config.php + version.php).
     *
     * @return string|null  Absolute path to Moodle root, or null if not found.
     */
    public function resolve(?string $startDir = null): ?string
    {
        $dir = $startDir ?? getcwd();
        if ($dir === false) {
            return null;
        }

        $this->verbose?->step('Searching for Moodle root (up to ' . self::MAX_DEPTH . ' levels)');
        $this->verbose?->detail('Start directory', $dir);

        for ($i = 0; $i <= self::MAX_DEPTH; $i++) {
            $this->verbose?->info('Checking: ' . $dir);
            if ($this->isMoodleRoot($dir)) {
                $this->verbose?->done('Found Moodle root at ' . $dir);
                return $dir;
            }
            $parent = dirname($dir);
            if ($parent === $dir) {
                $this->verbose?->warn('Reached filesystem root without finding Moodle');
                break;
            }
            $dir = $parent;
        }

        $this->verbose?->warn('Moodle root not found after scanning ' . self::MAX_DEPTH . ' levels');
        return null;
    }

    /**
     * Check whether a directory looks like a Moodle root.
     */
    private function isMoodleRoot(string $dir): bool
    {
        return file_exists($dir . '/config.php')
            && file_exists($dir . '/version.php')
            && file_exists($dir . '/lib/moodlelib.php');
    }
}
