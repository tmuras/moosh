<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Generic\Download;
use Moosh\MooshCommand;

class DownloadMoodle extends MooshCommand
{
    const downloadUrl = "http://download.moodle.org/download.php/direct/stable<major>/moodle-latest-<major>.tgz";

    public function __construct()
    {
        parent::__construct('moodle', 'download');

        $this->addOption('v|version:', 'version');
    }

    public function isBootstraped()
    {
        return false;
    }

    public function execute()
    {
        $options = $this->expandedOptions;
        var_dump($options);
        $version = str_replace('.', '', $options['version']);
        $url = str_replace('<major>',$version,self::downloadUrl);

        //rename lang/en/newmodule.php
        $ret = null;
        system("wget --continue --timestamping '$url'",$ret);
        if ($ret) {
            cli_error("Fetching file failed");
        }
    }
}
