<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class GenerateLang extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('lang', 'generate');
    }

    public function execute()
    {
        //scan all files provided in as arguments or current directory for "."
        if(count($this->arguments)>0) {
            $targets = $this->arguments;
        } else { //or use the current edit file

        }


        //find all possible strings from the current mod's namespace

        //find lang file
        $langCategory = $this->getLangCategory();
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
            $component = new mlang_component($this->pluginInfo['name'], 'en', mlang_version::by_branch('MOODLE_24_STABLE'));
            $parser->parse(file_get_contents($langFile), $component);
            var_dump($component);
        }

        //add them into the lang file

        //add string to a lang file: discovered or found in lang/en/*.php
        var_dump($this->pluginInfo);
        var_dump($this->relativeDir);
        var_dump($this->topDir);
        //var_dump($langFile);

    }
}
