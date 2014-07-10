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

class PluginInstall extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('install', 'plugin');

        $this->addArgument('plugin_name');
        $this->addArgument('moodle_version');
    }

    public function execute()
    {
        global $CFG;

        require_once($CFG->libdir.'/adminlib.php');       // various admin-only functions
        require_once($CFG->libdir.'/upgradelib.php');     // general upgrade/install related functions
        require_once($CFG->libdir.'/environmentlib.php');
        require_once($CFG->libdir.'/pluginlib.php');
        require_once($CFG->dirroot.'/course/lib.php');

        $pluginname = $this->arguments[0];

        $moodleversion = $this->arguments[1];

        $pluginurl = "https://moodle.org/plugins/view.php?plugin=" . $pluginname . "&moodle_version=" . $moodleversion;

        $page = file_get_contents($pluginurl);
        // check if website exists
        try {
            $doc = new \DOMDocument();
            $doc->loadHTML($page);
        }
        catch(Exception $e) {
            die("Failed to load plugin web info\n");
        }

        $xpath = new \DOMXpath($doc);

        $elements = $xpath->query("//a[@class='download btn latest']");
        $downloadlink = $elements->item(0)->getAttribute('href');
        
        $split = explode('_',$this->arguments[0],2);

        $tempdir = home_dir() . '/.moosh/moodleplugins/';

        if (!fopen($tempdir . $split[1] . ".zip", 'w')) {
            echo "Failed to save plugin.\n";
            return;
        }
        try {
            file_put_contents($tempdir . $split[1] . ".zip", file_get_contents($downloadlink));
        }
        catch (Exception $e) {
            echo "Failed to download plugin. " . $e . "\n";
            return;
        }

        try {
            shell_exec("unzip " . $tempdir . $split[1] . ".zip -d " . home_dir() . "/.moosh/moodleplugins/");

            shell_exec("cp -r " . $tempdir . $split[1] . "/ " . $CFG->dirroot.  "/" . $split[0]);
        } catch (Exception $e) {
            echo "Failed to unzip plugin. " . $e . "\n";
            return;
        }

        upgrade_noncore(true);
    }

}
