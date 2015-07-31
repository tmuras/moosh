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
    const downloadUrl = "https://download.moodle.org/download.php/direct/stable<version>/moodle-<major>.<minor>.tgz";

    public function __construct() {
        parent::__construct('moodle', 'download');

        $this->addOption('v|version:', 'version');
    }

    public function bootstrapLevel() {
        return self::$BOOTSTRAP_NONE;
    }

    public function execute() {
        $options = $this->expandedOptions;
        $version = str_replace('.', '', $options['version']);
        list($major, $minor) = explode('.', $options['version']);
        $url = str_replace('<version>', $version, self::downloadUrl);
        $url = str_replace('<major>', $major, $url);
        $url = str_replace('<minor>', $minor, $url);

        run_external_command("wget --continue --timestamping '$url'", "Fetching file failed");
    }
}
