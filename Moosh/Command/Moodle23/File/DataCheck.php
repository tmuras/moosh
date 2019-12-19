<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\File;

use Moosh\MooshCommand;

class DataCheck extends MooshCommand {
    private $quiet = false;
    
    public function __construct() {
        parent::__construct('datacheck', 'file');
        $this->addOption('q|quiet', 'Quiet mode. Only outputs errors.');
    }

    protected function getArgumentsHelp() {
        $help = parent::getArgumentsHelp();
        $help .= "\n\n";
        $help .= "Check moodledata files for coruption.";

        return $help;
    }

    private function checkDirectory($dir, &$errors) {
        $dir .= DIRECTORY_SEPARATOR;
        
        // Read contents of current directory
        $list = scandir($dir);
        
        // Exclude .  ..  warning.txt
        $list = array_diff($list, array('.', '..', 'warning.txt'));
        
        foreach ($list as &$entry) {
            if (is_dir($dir.$entry)) {
                // Recursive call on directories
                $this->checkDirectory($dir.$entry, $errors);
            }
            
            else {
                if (!$this->checkFile($dir, $entry)) {
                    $errors[] = $dir.$entry;
                }
            }
            
            if (!$this->quiet) echo '.';
        }
    }
    
    private function checkFile($dir, $file) {
        return (strcmp(sha1_file($dir.$file),$file) == 0);
    }
    
    public function execute() {
        global $CFG;
        
        if ($this->finalOptions['quiet']) {
            $this->quiet = true;
        }

        $errors = array();
        $this->checkDirectory($CFG->dataroot.DIRECTORY_SEPARATOR.'filedir', $errors);
        if (!$this->quiet) echo "Done.\n\n";
        
        if (empty($errors)) {
            if (!$this->quiet) echo "The contents of moodledata appear to be OK.\n";
        }
        
        else {
            foreach ($errors as $file) {
                // Could write this to STDERR.
                echo $file . " Checksum does not match filename.\n";
            }
        }
    }
}
