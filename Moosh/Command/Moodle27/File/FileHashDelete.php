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
    
    public function execute() {
        global $DB;

        $hash = $this->arguments[0];
        $mainContentHash = $DB->get_records('files', array('contenthash' => $hash));

        if (empty($mainContentHash)) {
            cli_error("no file was found");
        }

        foreach ($mainContentHash as $mainContentHashDetails) {
            if ($mainContentHashDetails->filename == '.') {
                echo 'There is: ' . count($mainContentHash) . ' results of this hash. ';
                cli_error("You cant delete all dot '.' files.");
            }
            //get all elements in directory based on path up to contenthash-file
            $mainContentHashElementsInPath = $DB->get_records('files', array(
                'itemid'    => $mainContentHashDetails->itemid,
                'contextid' => $mainContentHashDetails->contextid,
                'component' => $mainContentHashDetails->component,
                'filearea'  => $mainContentHashDetails->filearea,
            ));
            //var_dump($mainContentHashElementsInPath);
            $countMainContentHashElementsInPath = count($mainContentHashElementsInPath);

            if ($countMainContentHashElementsInPath > 2) {
                //there are more than 2 files in path of mainContentHash
                //check if one of the files in directory has the same id as initial content hash - delete it

                if ($this->checkForDirRootDotFile($mainContentHashElementsInPath) !== true) {
                    echo "For given directory there is no root dir '.' file";

                }
                
                foreach ($mainContentHashElementsInPath as $file) {
                    if ($file->id == $mainContentHashDetails->id) {
                        $safeDelete = true;
                        $this->deleteFiles($mainContentHash, $safeDelete);

                    }
                }
            }

            if ($countMainContentHashElementsInPath <= 2) {
                if ($countMainContentHashElementsInPath === 1) {
                    // there is just one file or empty dir, delete it.
                    $this->deleteFiles($mainContentHashElementsInPath, true);
                }

                if ($countMainContentHashElementsInPath === 2) {
                    // there are 2 elements in given dir. Check if one of them is root dir entry '.'
                    $safeDelete = false;

                    if ($this->checkForDirRootDotFile($mainContentHashElementsInPath) === true) {
                            $safeDelete = true;
                    } else {
                        echo "For given directory there is no root dir '.' file";
                    }

                    if($safeDelete === true) {
                        $this->deleteFiles($mainContentHashElementsInPath, $safeDelete);
                    }
                }
            }
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
            foreach ($files as $file) {
                echo "File ID: {$file->id}, contenthash: {$file->contenthash}, "
                    . "itemid: {$file->itemid}, component {$file->component}, "
                    . "filearea {$file->filearea}, filename {$file->filename}" . PHP_EOL;
            }
        } else {
            echo "safeDelete is not set. Refuse to remove any records in DB.";
        }
}
    
    private function checkForDirRootDotFile($files) {
        foreach($files as $file) {
            if ($file->filename == ".") {
                return true;
            }
        }
    }
}
