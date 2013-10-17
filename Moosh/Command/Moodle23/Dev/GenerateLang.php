<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Dev;
use Moosh\MooshCommand;
use mlang_parser_factory;
use mlang_version;
use mlang_component;
use mlang_string;

class GenerateLang extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('lang', 'generate');
        $this->maxArguments = 255;
    }

    public function execute()
    {
         $targets = array();
        //scan all files provided in as arguments or current directory for "."
        if(count($this->arguments)>0) {
            $targets = $this->arguments;
        } else { //or use the current edit file
            $this->loadSession();

            if(isset($this->session['generator.last-file'][$this->cwd]) && file_exists($this->cwd . '/' .$this->session['generator.last-file'][$this->cwd])) {
                $targets = array($this->session['generator.last-file'][$this->cwd]);
            }
        }

        if(count($targets) == 0) {
            cli_error('I don\'t know which file(s) to operate on');
        }

        $strings = array();
        foreach($targets as $target) {
            if($this->verbose) {
                echo "Processing $target\n";
            }
            $strings[$target] = array();
            $content = file_get_contents($this->cwd . '/' . $target);

            //get_string function
            $matches = null;
            preg_match_all("/get_string\('([^']+)'([^)]*?)\)/",$content, $matches);
            foreach($matches[1] as $k=>$lang) {
                $key = $lang;
                $langCat = null;
                $matches2 = null;
                if(preg_match("/'(.*)'/",$matches[2][$k],$matches2)) {
                    //var_dump($matches[2][$k]);
                    $langCat = $matches2[1];
                    $key .= '|' . $langCat;
                }
                //echo "$key\n";
                if(!isset($strings[$target][$key])) {
                    $strings[$target][$key] = array(
                        'number' => 1,
                        'lang' => $lang,
                        'category' => $langCat,
                    );
                } else {
                    $strings[$target][$key]['number']++;
                }
            }

            //addHelpButton('code', 'code', 'programming');
            $matches = null;
            preg_match_all("/addHelpButton\('([^']+)'\s*,\s*'([^']+)'\s*,\s*'([^']+)'/",$content, $matches);

            foreach($matches[2] as $k=>$lang) {
                $langCat = $matches[3][$k];
                $key = $lang.'|'.$langCat;
                if(!isset($strings[$target][$key])) {
                    $strings[$target][$key] = array(
                        'number' => 1,
                        'lang' => $lang,
                        'category' => $langCat,
                    );
                } else {
                    $strings[$target][$key]['number']++;
                }

                $lang .= '_help';
                $key = $lang.'|'.$langCat;
                if(!isset($strings[$target][$key])) {
                    $strings[$target][$key] = array(
                        'number' => 1,
                        'lang' => $lang,
                        'category' => $langCat,
                    );
                } else {
                    $strings[$target][$key]['number']++;
                }

            }
        }

        $langCategory = $this->getLangCategory();
        $langKeys = array();
        //find all possible strings from the current mod's namespace
        foreach($strings as $file=>$langFile) {
            echo "File $file\n";
            foreach($langFile as $langKey => $lang) {
                echo "\t" .  $lang['lang'] .','. $lang['category'] .','. $lang['number'] . "\n";
                if($langCategory == $lang['category']) {
                    $langKeys[$lang['lang']] = $lang['lang'];
                }
            }
        }

        //find lang file
        $langFile = $this->topDir . '/' . $this->relativeDir . "/lang/en/$langCategory.php";
        if (!file_exists($langFile)) {
            //search a bit more?
            $files = glob($this->topDir . '/' . $this->relativeDir . "/lang/en/*.php");
            if (count($files) == 1) {
                $langFile = $files[0];
            } else {
                $langFile = false;
            }
        }

        //extract all lang strings from the lang file
        if ($langFile) {
            require_once($this->mooshDir . '/vendor/moodlehq/moodle-local_amos/mlangparser.php');
            $parser = mlang_parser_factory::get_parser('php');
            // todo: adapt this to use the appropriate moodle version
            $component = new mlang_component($this->pluginInfo['name'], 'en', mlang_version::by_branch('MOODLE_24_STABLE'));
            $parser->parse(file_get_contents($langFile), $component);
            //check for the new lang strings
            echo "Lang keys to be added to $langFile:\n";
            foreach($langKeys as $key) {
                if(!$component->has_string($key)) {
                    echo "\t$key\n";
                    $string = new mlang_string($key,$key);
                    $component->add_string($string);
                }
            }

            //add them into the lang file
            $component->export_phpfile($langFile);
        }
    }
}
