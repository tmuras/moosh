<?php

namespace Moosh\Command\Moodle27\Theme;
use Moosh\MooshCommand;
use \Phar,
    \PharData;
use \DOMDocument;
use \core_plugin_manager;
use \stdClass;
use \Exception;
use \context_system;

class SettingsImport extends MooshCommand {

    private $inputfilepath;
    private $extractiondir;

    public function __construct() {
        parent::__construct('settings-import', 'theme');

        $this->addOption('t|targettheme:', 'Destination theme for settings, if different from source');

        $this->addArgument('inputfile');
        $this->maxArguments = 2;
    }

    public function execute() {
        global $CFG;

        require_once "$CFG->libdir/classes/plugin_manager.php";

        $this->inputfilepath = $this->checkFileArg($this->arguments[0]);
        $filename = basename($this->inputfilepath);
        $outputdirname = rtrim($filename, '.tar.gz');
        $this->extractiondir = "{$this->cwd}/{$outputdirname}/";

        if (!is_writable($this->cwd)) {
            echo "Directory {$this->cwd} not writable \n";
            exit(0);
        } else {
            try {
                mkdir($this->extractiondir);
            } catch (Exception $e) {
                echo "Unable to create extraction directory \n";
                exit(0);
            }
        }


        $this->extract_settings();
        $this->import_settings();
        $this->delete_extraction_directory();
        exit(0);
    }

    private function import_settings() {
        global $DB;

        $context = context_system::instance();
        $fs = get_file_storage();

        $filename = basename($this->inputfilepath);
        $filenameparts = explode('_', $filename);
        $themename = implode('_', array_slice($filenameparts, 0, -1));

        $dom = new DOMDocument();
        $dom->load("{$this->extractiondir}/{$themename}.xml");
        $themedom = $dom->documentElement;

        $themename = $themedom->getAttribute('name');
        $themecomponent = $themedom->getAttribute('component');

        if (!empty($this->expandedOptions['targettheme'])) {
            $themename = $this->expandedOptions['targettheme'];
            $themecomponent = "theme_$themename";
        }

        $settingsdom = $themedom->getElementsByTagName('setting');

        $availablethemes = core_plugin_manager::instance()->get_plugins_of_type('theme');
        if (!$availablethemes || !in_array($themename, array_keys($availablethemes))) {
            echo "$themecomponent not installed \n";
            $this->delete_extraction_directory();
            exit(0);
        }

        $settingsimported = 0;
        if ($settingsdom->length) {
            foreach ($settingsdom as $setting) {
                $settingname = $setting->getAttribute('name');
                $settingvalue = $setting->nodeValue;

                if ($setting->hasAttribute('file')) {
                    $filename = ltrim($settingvalue, '/');
                    $fileinfo = array(
                            'contextid' => $context->id,
                            'component' => $themecomponent,
                            'filearea' => $settingname,
                            'itemid' => 0,
                            'filepath' => '/',
                            'filename' => $filename
                    );

                    if ($fs->file_exists($fileinfo['contextid'], $fileinfo['component'],
                            $fileinfo['filearea'], 0, $fileinfo['filepath'], $fileinfo['filename'])) {

                        $fs->delete_area_files($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'], 0);
                    }

                    $filepath = $this->extractiondir.$setting->getAttribute('file');
                    $fs->create_file_from_pathname($fileinfo, $filepath);
                }

                $todb = new stdClass;
                $todb->plugin = $themecomponent;
                $todb->name = $settingname;
                $todb->value = $settingvalue;

                if ($existing = $DB->get_record('config_plugins', ['plugin' => $todb->plugin, 'name' => $todb->name])) {
                    $todb->id = $existing->id;
                    $DB->update_record('config_plugins', $todb);
                } else {
                    $DB->insert_record('config_plugins', $todb);
                }
                $settingsimported++;
            }
            echo "$settingsimported settings imported to $themecomponent \n";
        } else {
            echo "No settings to import \n";
        }
    }

    private function extract_settings() {
        $tarfilepath = rtrim($this->inputfilepath, '.gz');

        try {
            $p1 = new PharData($this->inputfilepath, Phar::GZ);
            $p1->decompress();
        }
        catch (Exception $e) {
            exit($e->getMessage());
        }

        try {
            $p2 = new PharData($tarfilepath);
            $p2->extractTo($this->extractiondir, null, true);
            unlink($tarfilepath);
        }
        catch (Exception $e) {
            unlink($tarfilepath);
            exit($e->getMessage());
        }
    }


    private function delete_extraction_directory() {
        if (empty($this->extractiondir) || $this->extractiondir === '/') exit;

        if (!is_dir($this->extractiondir)) {
            throw new Exception("$this->extractiondir must be a directory");
        }

        $files = glob($this->extractiondir . '*', GLOB_MARK);
        foreach ($files as $file) {
            unlink($file);
        }
        rmdir($this->extractiondir);
    }
}
