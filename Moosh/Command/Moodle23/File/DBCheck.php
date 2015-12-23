<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * DBCheck - Darío Gómez <dario.gomez@solutionsad.com>
 */

namespace Moosh\Command\Moodle23\File;

use Moosh\MooshCommand;

class DBCheck extends MooshCommand {
    private $quiet = false;
    
    public function __construct() {
        parent::__construct('dbcheck', 'file');
        $this->addOption('q|quiet', 'Quiet mode. Only outputs errors.');
    }

    protected function getArgumentsHelp() {
        $help = parent::getArgumentsHelp();
        $help .= "\n\n";
        $help .= "Check moodledata files present on disk but not in DB.";

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
            } else {
                if (!$this->checkFile($entry)) {
                    $errors[] = $dir.$entry;
                }
            }
            
            if (!$this->quiet) echo '.';
        }
    }
    
    private function checkFile($file) {
        global $DB;

        if ($DB->count_records("files", array("contenthash"=>$file))!=0) {
             return true;
        } else {
             return false;
        }
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
        } else {
            echo "List of files on disk but not in DB:\n";
            foreach ($errors as $file) {
                // Could write this to STDERR.
                echo $file . "\n";
            }
        }
    }
}
