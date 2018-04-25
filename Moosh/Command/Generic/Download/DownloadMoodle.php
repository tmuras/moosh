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
    const downloadUrl = "https://download.moodle.org/download.php/direct/stable<version>/moodle-<major>.<minor>.<point>.tgz";

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
        // Latest 3.4: https://download.moodle.org/download.php/direct/stable34/moodle-latest-34.tgz
        // 3.4.1:      https://download.moodle.org/download.php/direct/stable34/moodle-3.4.1.tgz
        // 3.4.0:      https://download.moodle.org/download.php/direct/stable34/moodle-3.4.tgz
        // Latest 3.3: https://download.moodle.org/download.php/direct/stable33/moodle-latest-33.tgz


        if(!$options['version']) {
            $releasepage = file_get_contents('https://download.moodle.org/releases/latest/');
            preg_match('(https:\/\/download.moodle.org\/download.php\/stable[0-9].\/moodle-latest-[0-9].\.tgz)', $releasepage, $downloadurl);
            $downloadpage = file_get_contents($downloadurl[0]);
            preg_match('(\/download\.php\/direct\/stable[0-9].\/moodle-latest-[0-9].\.tgz)', $downloadpage, $downloadurl);
            $url = 'https://download.moodle.org' . $downloadurl[0];
            run_external_command("wget --continue --timestamping '$url'", "Fetching file failed");
            die();
        }

        $version = explode('.', $options['version']);
        if(count($version) == 3) {
            $major = $version[0];
            $minor = $version[1];
            $point = $version[2];
        } else if (count($version) == 2) {
            $major = $version[0];
            $minor = $version[1];
            $point = -1; // Latest $major.$minor
         } else {
            die("Provide version in X.Y or X.Y.Z format");
         }

        $url = str_replace('<version>', $major . $minor, self::downloadUrl);
        $url = str_replace('<major>', $major, $url);
        $url = str_replace('<minor>', $minor, $url);
        if($point != -1) {
          $url = str_replace('<point>', $point, $url);
        }

        run_external_command("wget --continue --timestamping '$url'", "Fetching file failed");
    }
}
