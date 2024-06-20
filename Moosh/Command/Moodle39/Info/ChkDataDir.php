<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Info;
use Moosh\MooshCommand;

class ChkDataDir extends MooshCommand
{
    var $filelist = '';
    
    public function __construct() {
        parent::__construct('chkdatadir');
        $this->filelist = '';
    }

    public function execute() {
        global $CFG;
        //if ($CFG->version <  2010112400) {
        //    $this->execute19();
        //} else {
        //    $this->execute2();
        //}
        
        if (posix_getuid() == 0) {
            echo "You are running this script with root privileges. Try again on another account.";
            return;
        }
        
        $username = posix_getpwuid (posix_getuid());
        $username = $username['name'];
        
        $parentdir = $CFG->dataroot;
        $this->chkmydir($parentdir);
        echo "Checked dir: $parentdir\n";
        if ($this->filelist) {
            echo "Following files are not writable by user '$username':\n";
            echo $this->filelist;
        } else {
            echo "All files are writable by user '$username'.\n";
        }
    }
    
    private function chkmydir($parentdir) {
        $endchar = $parentdir[strlen($parentdir) - 1];
        if ($endchar != '/') {
            $parentdir .= '/';
        }
        $files = scandir($parentdir);
        foreach  ($files as $file){
            $fullpath = $parentdir.$file;
            if (is_dir($fullpath)) {
                if ($file != '.' and $file != '..') {
                    $this->chkmydir($fullpath);
                }
            } else {
                if (is_writable($fullpath)) {
                    
                } else {
                    $this->filelist .= "$fullpath\n";
                }
            }
        }
    }
    
}
