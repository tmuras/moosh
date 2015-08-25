<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Kacper Golewski <k.golewski@gmail.com>
 */

namespace Moosh\Command\Generic\Tools;
use Moosh\MooshCommand;

class ToolsCodeCheck extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('code-check');
        $this->addOption('p|path:', 'path to check code');
        $this->addOption('i|interactive', 'interactive code check');
        $this->addOption('r|repair', 'repair code before check. Commit changes before, or data might be lost');
    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_NONE;
    }

    public function execute()
    {

        require_once($this->mooshDir."/includes/codesniffer/CodeSniffer.php");
        require_once($this->mooshDir."/includes/codesniffer/lib.php");
        require_once($this->mooshDir."/includes/coderepair/CodeRepair.php");

        $moodle_sniffs = $this->mooshDir."/includes/codesniffer/moodle";

        $options = $this->expandedOptions;
        $interactive = $options['interactive'];

        if (isset($options['path'])) {
            $this->checkFileArg($options['path']);
            $path = $options['path'];
        } else {
            $path = $this->cwd;
        }

        $files = $this->_get_files($path);
        if ($options['repair'] === true) {
            $code_repair = new \CodeRepair($files);
            $code_repair->drymode = false;
            $code_repair->start();         
        }
        $phpcs = new \PHP_CodeSniffer(1, 0, 'utf-8', false);
        $phpcs->setCli(new \codesniffer_cli());
        $numerrors = $phpcs->process($files, $moodle_sniffs);
        $phpcs->reporting->printReport('full', false, null);

    }

    private function _clean_path($path) {
       return str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $path);
    }

    private function _get_files($path) {

        $extensions_to_check = array(
            "php",
        );

        $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));

        $files = array();

        if ($handle = opendir($path)) {

            foreach($objects as $entry => $object){
                if (!$object->isDir()) {
                    $ext = pathinfo($entry, PATHINFO_EXTENSION);
                    if (in_array($ext, $extensions_to_check)) {
                        $files[] = $entry;
                    }
                }
            }
            closedir($handle);
        }
        return $files;
    }
}

