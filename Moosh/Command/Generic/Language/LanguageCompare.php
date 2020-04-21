<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Generic\Language;
use Moosh\MooshCommand;

class LanguageCompare extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('compare', 'lang');
        $this->addArgument('lang-file-1');
        $this->addArgument('lang-file-2');
    }


    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_NONE;
    }


    public function execute()
    {
        $options = $this->expandedOptions;
        $file1 = $this->arguments[0];
        $file2 = $this->arguments[1];

        $string = null;
        include($file1);
        $lang1 = $string;

        $string = null;
        include($file2);
        $lang2 = $string;

        echo "Comparing $file1 and $file2. Summary:\n";
        $notin2 = array_diff_key($lang1, $lang2);
        $notin1 = array_diff_key($lang2, $lang1);

        echo "Number of strings in $file1: " . count($lang1) . "\n";
        echo "Number of strings in $file2: " . count($lang2) . "\n";

        echo "Number of strings missing in $file2: " . count($notin2) . "\n";
        echo "Number of strings missing in $file1: " . count($notin1) . "\n";

        if(count($notin2)) {
            echo "\n";
            echo "List of language strings that exist in $file1 but are not present in $file2\n";
            echo implode("\n", array_keys($notin2));
            echo "\n\n";
        }
        if(count($notin1)) {
            echo "List of language strings that exist in $file2 but are not present in $file1\n";
            echo implode("\n", array_keys($notin1));
        }
        echo "\n";
    }
}
