<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class FilePath extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('path', 'file');

        $this->addRequiredArgument('id_or_hash');

    }

    public function execute()
    {
        global $CFG, $DB;

        $id = trim($this->arguments[0]);
        if (strlen($id) == 40) {
            //this is file hash
            $hash = $id;
        } else {
            $fs = get_file_storage();
            $file = $fs->get_file_by_id($id);
        }
        
        if (isset($CFG->filedir)) {
            $filedir = $CFG->filedir;
        } else {
            $filedir = $CFG->dataroot.'/filedir';
        }
        $l1 = $hash[0].$hash[1];
        $l2 = $hash[2].$hash[3];
        return "$this->filedir/$l1/$l2";
    }

}

