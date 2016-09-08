<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle27\File;

use Moosh\MooshCommand;

class FileHashDelete extends MooshCommand{
    
    public function __construct() 
    {
        parent::__construct('hash-delete', 'file');
        
        $this->addArgument('hash');
    }
    
    public function execute()
    {
        global $DB;
        
        $hash = $this->arguments[0];
        
        $result = $DB->get_records('files', array('contenthash' => $hash));

        if (count($result) == 0){
            cli_error("no file was found");
        }

        if (count($result) <= 2) {
            if (count($result) === 1){
                // there is just one file, delete it.
                $this->deleteFiles($result, true);
            }
            
            if (count($result) === 2) {
                // there are 2 files. Check if one of them is '.'
                $safeDelete = false;
                
                foreach ($result as $file){
                    if ($file->filename == ".") {
                        $safeDelete = true;
                    }
                }
                
                // If one of them is '.' delete both, otherwise inform user
                
                if ($safeDelete) {
                    $this->deleteFiles($result, $safeDelete);
                } else {
                    $this->tooManyFiles($result);
                }
            }
        }else{
            // give user info that there is too many files for safe deletion
            $this->tooManyFiles($result);
        }
    }

    private function deleteFiles(array $files, $safeDelete = false) {
        global $DB;
        
        $fileIds = [];
        
        foreach ($files as $file) {
            $fileIds[] = $file->id;
        }
        
        $where = "id IN (" . implode(',', $fileIds) . ")";

        if($safeDelete === true) {
            $DB->delete_records_select('files', $where);
            echo "Successfully deleted files." . PHP_EOL;
            echo "File ID: {$file->id}, contenthash: {$file->contenthash}, itemid: {$file->itemid}, component {$file->component}, filearea {$file->filearea}, filename {$file->filename}" . PHP_EOL;
        } else {
            echo "safeDelete is not set. Refuse to remove any records in DB.";
        }
}
    
    private function tooManyFiles($files) {
        echo "There are too many files for safe delete.".PHP_EOL;
        echo "All files count: " . count($files) . PHP_EOL;
    }
}
