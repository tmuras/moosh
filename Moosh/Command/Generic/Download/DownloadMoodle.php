<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Generic\Download;

use Moosh\MooshCommand;

class DownloadMoodle extends MooshCommand {
    public function __construct() {
        parent::__construct('moodle', 'download');

        $this->addOption('v|version:', 'Download Moodle version: use 3.5 for the latest 3.5, 3.5.1 for an exact minor version.');
    }

    public function bootstrapLevel() {
        return self::$BOOTSTRAP_NONE;
    }

    public function execute() {
        $options = $this->expandedOptions;

        // Example URLs
        // Change in 3.9 naming convention for most recent release, when no version given.
        // Changed from moodle-latest-38.tgz -> moodle-3.9.tgz (no 'latest', extra period).
        // It appears the most recent release can't be "fetched" if specified with -v <major>.<minor>
        //
        // Latest 3.4: https://download.moodle.org/download.php/direct/stable34/moodle-latest-34.tgz
        // 3.4.1:      https://download.moodle.org/download.php/direct/stable34/moodle-3.4.1.tgz
        // 3.4.0:      https://download.moodle.org/download.php/direct/stable34/moodle-3.4.tgz
        // Latest 3.3: https://download.moodle.org/download.php/direct/stable33/moodle-latest-33.tgz

        if (!$options['version']) {
            $releasepage = file_get_contents('https://download.moodle.org/releases/latest/');
            $lateststable = null;

            // Example: https://download.moodle.org/download.php/stable310/moodle-latest-310.tgz
            preg_match('|https://download.moodle.org/download.php/stable(\d+)/moodle-latest-\d+\.tgz|',
                $releasepage, $lateststable);
            if (!$lateststable) {
                cli_error("Couldn't find the latest stable version of Moodle on https://download.moodle.org/releases/latest/");
            }

            $versioncollapsed = $lateststable[1];
            $exactversion = 'latest-' . $versioncollapsed;
        } else {
            $version = explode('.', $options['version']);
            if (count($version) == 3) {
                $versioncollapsed  = $version[0]. $version[1];
                $exactversion = $version[0] . '.' .  $version[1] . '.' . $version[2];
            } else if (count($version) == 2) {
                // Latest version requested
                $versioncollapsed  = $version[0]. $version[1];
                $exactversion = "latest-$versioncollapsed";
            } else {
                die("Provide version in X.Y or X.Y.Z format");
            }
        }
        // Download example: https://download.moodle.org/download.php/direct/stable310/moodle-latest-310.tgz
        $url =  "https://download.moodle.org/download.php/direct/stable$versioncollapsed/moodle-$exactversion.tgz";
        run_external_command("wget --continue --timestamping '$url'", "Fetching file failed");
    }
}
