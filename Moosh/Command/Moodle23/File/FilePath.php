<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\File;

use Moosh\MooshCommand;

class FilePath extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('path', 'file');

        $this->addArgument('id_or_hash');
        $this->minArguments = 0;
        $this->maxArguments = 255;
        $this->addOption('s|stdin', 'read list of file IDs from standard input');
        $this->addOption('r|relative', 'show path relative from Moodle data');

    }

    public function execute()
    {
        global $CFG, $DB;

        if ($this->expandedOptions['stdin']) {
            while ($line = fgets(STDIN)) {
                $this->printFile($line);
            }
        } else {
            foreach ($this->arguments as $argument) {
                $this->printFile($argument);
            }
        }

    }

    private function printFile($arg)
    {
        global $CFG;

        $id = trim($arg);
        if (strlen($id) == 40) {
            //this is file hash
            $hash = $id;
        } else {
            $fs = get_file_storage();
            $file = $fs->get_file_by_id($id);
            if (!$file) {
                echo "File '$id' not found\n";
                return;
            }
            $hash = $file->get_contenthash();
        }

        if (isset($CFG->filedir)) {
            $filedir = $CFG->filedir;
        } else {
            $filedir = $CFG->dataroot . '/filedir';
        }
        $l1 = $hash[0] . $hash[1];
        $l2 = $hash[2] . $hash[3];

        if ($this->expandedOptions['relative']) {
            echo "filedir/$l1/$l2/$hash\n";
        } else {
            echo "$filedir/$l1/$l2/$hash\n";

        }
    }
}

