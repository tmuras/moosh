<?php
/**
 * Exports configuration of plugin to .xml
 * moosh config-plugin-export [-o, --outputdir] <component>
 *
 * @example moosh config-plugin-export book
 * @example moosh config-plugin-export -o /tmp/plugin/ mod_book
 *
 * @copyright  2020 onwards Jakub Kleban
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Config;
use Moosh\MooshCommand;
use PharData;
use DOMDocument;
use \context_system;
use DateTime;

class ConfigPluginexport extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('plugin-export', 'config');

        $this->addArgument('component');
        $this->addOption('o|outputdir:', 'the directory where output xml file is to be saved');

        $this->minArguments = 1;
        $this->maxArguments = 1;
    }

    public function execute()
    {
        global $CFG;

        require_once($CFG->dirroot.'/lib/classes/plugin_manager.php');

        // Init vars
        $componenttoexport = null;
        $pluginmanager = \core_plugin_manager::instance();


        // Validate output dir
        if ($this->parsedOptions->has('outputdir')) {
            $outputdir = rtrim($this->parsedOptions['outputdir']->value, '/');

            if (substr($outputdir, 0, 2) == '..'){
                $outputdir = $this->cwd . '/' . $outputdir;
            }
            else {
                if ($outputdir[0] == '.'){
                    $outputdir = $this->cwd . substr($outputdir, 1);
                }
            }
        }
        else {
            $outputdir = $this->cwd;
        }

        if (is_dir($outputdir)){
            if (!is_writable($outputdir)) {
                echo "Output directory $outputdir is not writable \n";
                exit(0);
            }
        }
        else{
            echo "$outputdir is not a directory or doesn't exist \n";
            exit(0);
        }

        //get name of plugin from user
        $componenttoexport = $this->arguments[0];

        //check if plugin exist, set correct name
        try {
            $pluginname = $pluginmanager->plugin_name($componenttoexport);
        }
        catch (\Exception $e) {
            echo "Cought exception: " . $e->getMessage() . " \n";
            echo "Not found plugin: $componenttoexport\n";
            exit(0);
        }

        // Load plugin settings
        $config = get_config($pluginname);

        if (!empty($config)) {
            $time = time();
            $tarname = "{$outputdir}/{$pluginname}_config_{$time}.tar";
            $phar = new PharData($tarname);

            $dom = new DOMDocument('1.0', 'utf-8');
            $root = $dom->createElement('config');
            $root->setAttribute('plugin', $pluginname);
            if (isset($config->version)) {
                $root->setAttribute('version', $config->version);
            }

            $dom->appendChild($root);

            foreach ($config as $settingname => $settingvalue) {
                if ($settingname == 'version') continue;

                $element = $dom->createElement('setting', $settingvalue);
                $element->setAttribute('name', $settingname);

                if ($settingvalue && $settingvalue[0] == '/' && strpos($settingvalue, '.') !== FALSE) {

                    $fs = get_file_storage();
                    if ($files = $fs->get_area_files(context_system::instance()->id, $pluginname, $settingname, $settingvalue)) {
                        foreach ($files as $f) {
                            if (!$f->is_directory()) {
                                $fh = $f->get_content_file_handle();

                                $meta = stream_get_meta_data($fh);
                                $uriparts = explode('/', $meta['uri']);
                                $hash = array_pop($uriparts);

                                $phar->addFile($meta['uri'], $hash);
                                $element->setAttribute('file', $hash);
                                $root->appendChild($element);
                            }
                        }
                    }
                } else {
                    $root->appendChild($element);
                }
            }

            $date = new DateTime();
            $xmlfilename = "{$pluginname}_config_{$date->getTimestamp()}.xml";
            $outputxml = "{$outputdir}/{$xmlfilename}";

            $dom->save($outputxml);
            echo "Config exported to $outputxml\n";
            exit(0);
        }
        else {
            echo "No config to export \n";
            exit(0);
        }
    }
}
