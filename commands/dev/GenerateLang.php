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
        //scan all files provided in as arguments or current directory

        //find all possible strings from the current mod's namespace

        //add them into the lang file

        //add string to a lang file: discovered or found in lang/en/*.php
        $langFile = $this->topDir . '/' . $this->relativeDir . "/lang/en/$langCategory.php";
        var_dump($this->pluginInfo);
        var_dump($this->relativeDir);
        var_dump($this->topDir);
        var_dump($langFile);

    }
}
