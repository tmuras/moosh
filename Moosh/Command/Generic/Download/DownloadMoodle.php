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

        $this->addOption('v|version:', 'version');
    }

    public function bootstrapLevel() {
        return self::$BOOTSTRAP_NONE;
    }

    public function execute() {
        $options = $this->expandedOptions;

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
        $url = str_replace('<version>', $version[0] . $version[1], self::downloadUrl);
        $url = str_replace('<major>', $version[0], $url);
        $url = str_replace('<minor>', $version[1], $url);
        $url = str_replace('<point>', $version[2], $url);

        run_external_command("wget --continue --timestamping '$url'", "Fetching file failed");
    }
}
