<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Generic\Language;
use Moosh\MooshCommand;

class LanguageStats extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('stats', 'lang');
        $this->addArgument('path/to/langfile.php');
    }


    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_NONE;
    }


    public function execute()
    {
        $options = $this->expandedOptions;
        $file1 = $this->arguments[0];

        $list = file_get_contents($file1);
        $list = explode("\n", $list);
        echo "File,number of strings,number of characters,number of words\n";

        foreach($list as $file) {
            $string = null;
            include($file);
            $nocharacters = 0;
            $nowords = 0;
            foreach ($string as $text) {
                $nostrings = count($string);
                $nowords += str_word_count($text);
                $nocharacters += strlen($text);
            }
            echo "$file,$nostrings,$nocharacters,$nowords\n";
        }

        echo "\n";
    }
}
