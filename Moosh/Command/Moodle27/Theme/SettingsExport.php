<?php

namespace Moosh\Command\Moodle27\Theme;
use Moosh\MooshCommand;
use \Phar,
    \PharData;
use \DOMDocument;
use \core_plugin_manager;
use \theme_config;
use \context_system;

class SettingsExport extends MooshCommand {

    public function __construct() {
        parent::__construct('settings-export', 'theme');

        $this->addOption('t|themename:', 'the name of the theme');
        $this->addOption('o|outputdir:', 'the directory where output xml file is to be saved');
        $this->maxArguments = 3;
    }

    public function execute() {
        global $CFG;

        require_once "$CFG->libdir/classes/plugin_manager.php";

        // Init vars
        $themetoexport = null;
        $outputdir = $this->cwd;

        //Validate theme
        $availablethemes = core_plugin_manager::instance()->get_plugins_of_type('theme');
        if (!empty($availablethemes)) {
            $availablethemenames = array_keys($availablethemes);
        }

        if ($this->parsedOptions->has('themename') && in_array($this->parsedOptions['themename']->value, $availablethemenames)) {
            $themetoexport = $this->parsedOptions['themename']->value;
        } else {
            $dirparts = explode('/', $this->cwd);

            if ($index = array_search('theme', $dirparts)) {
                $index++;
                if (array_key_exists($index, $dirparts) && in_array($dirparts[$index], $availablethemenames)) {
                    $themetoexport = $dirparts[$index];
                }
            }
        }

        if (!$themetoexport) {
            echo "Unknown theme. Available themes:- \r\n - ". implode("\r\n - ", $availablethemenames). "  \n";
            exit(0);
        }

        // Validate output dir
        if ($this->parsedOptions->has('outputdir')) {
            $outputdir = rtrim($this->parsedOptions['outputdir']->value, '/');
        }

        if (!is_writable($outputdir)) {
            echo "Output directory is not writable \n";
            exit(0);
        }

        // Load theme settings
        $themecomponent = $availablethemes[$themetoexport]->component;
        $themeconfig = theme_config::load($themetoexport);
        $themesettings = $themeconfig->settings;

        if (!empty($themesettings)) {
            $time = time();
            $tarname = "{$outputdir}/{$themetoexport}_settings_{$time}.tar";
            $phar = new PharData($tarname);

            $dom = new DOMDocument('1.0', 'utf-8');
            $root = $dom->createElement('theme');
            $root->setAttribute('name', $themetoexport);
            $root->setAttribute('component', $themecomponent);
            $root->setAttribute('version', $themesettings->version);
            $dom->appendChild($root);

            foreach ($themesettings as $settingname => $settingvalue) {
                if ($settingname == 'version') continue;

                $element = $dom->createElement('setting');
                $element->appendChild($dom->createTextNode($settingvalue));
                $element->setAttribute('name', $settingname);

                if ($settingvalue && $settingvalue[0] == '/' && strpos($settingvalue, '.') !== FALSE) {

                    $fs = get_file_storage();
                    if ($files = $fs->get_area_files(context_system::instance()->id, $themecomponent, $settingname, $settingvalue)) {
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

            $xmlfilename = "{$themetoexport}_settings.xml";
            $outputxml = "{$outputdir}/{$xmlfilename}";
            if ($dom->save($outputxml)) {
                $phar->addFile($outputxml, $xmlfilename);
                $phar->compress(Phar::GZ);
                unlink($outputxml);
                unlink($tarname);

                echo "Settings exported to $tarname.gz \n";
                exit(0);
            }
        } else {
            echo "No settings to export \n";
            exit(0);
        }
    }
}
