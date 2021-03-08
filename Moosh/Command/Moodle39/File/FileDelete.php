<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\File;
use Moosh\MooshCommand;

class FileDelete extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('delete', 'file');

        //the option below was added because I didn't get auto-detecting if stdin is present in a reliable way
        //with stream_set_blocking(STDIN, 0), I had to add sleep to make sure stdin is there.
        //if you know how to implement it in PHP without that annoying sleep() - give me a shout!
        $this->addOption('s|stdin', 'read list of file IDs from standard input');
        $this->addOption('f|flush', 'delete trashdir directory');

        $this->addArgument('file_id');
        $this->minArguments = 0;
        $this->maxArguments = 255;
    }

    public function execute()
    {
        global $CFG, $DB;

        if ($this->expandedOptions['flush']) {
            if (isset($CFG->trashdir)) {
                $trashdir = $CFG->trashdir;
            } else {
                $trashdir = $CFG->dataroot.'/trashdir';
            }
            require_once ($CFG->libdir . '/filelib.php');
            fulldelete($trashdir);
            exit(0);
        }

        if ($this->expandedOptions['stdin']) {
            while ($line = fgets(STDIN)) {
                $this->fileDelete($line);
            }
        } else {
            foreach ($this->arguments as $argument) {
                $this->fileDelete($argument);
            }
        }
    }


    private function fileDelete($id)
    {
        $id = intval($id);
        $fs = get_file_storage();
        $file = $fs->get_file_by_id($id);
        if ($file) {
            echo "deleting file '$id'\n";
            $file->delete();
        } else {
            echo "File '$id' does not exist\n";
        }

    }
}

