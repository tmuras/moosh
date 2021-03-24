<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\File;

use Moosh\MooshCommand;

class FileHashDelete extends MooshCommand{
    
    public function __construct() {
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
        
        echo 'There is: ' . count($mainContentHash) . ' results of this hash. '.PHP_EOL;

        foreach ($mainContentHash as $mainContentHashDetails) {
            if ($mainContentHashDetails->filename == '.') {
                cli_error("You cant delete all dot '.' files.");
            }
            
            //get all elements in directory based on path up to contenthash-file
            $mainContentHashElementsInPath = $DB->get_records('files', array(
                'itemid'    => $mainContentHashDetails->itemid,
                'contextid' => $mainContentHashDetails->contextid,
                'component' => $mainContentHashDetails->component,
                'filearea'  => $mainContentHashDetails->filearea,
            ));

            if ($this->checkForDirRootDotFile($mainContentHashElementsInPath) !== true) {
                echo "Error: Broken entry in DB. For given directory there is no root dir '.' file. "
                   . "Recreate it first. Not deleting this entry.".PHP_EOL;
                continue;
            }

            $countMainContentHashElementsInPath = count($mainContentHashElementsInPath);

            if ($countMainContentHashElementsInPath > 2) {
                //there are more than 2 files in path of mainContentHash
                //check if one of the files in directory has the same id as initial content hash - delete it
                foreach ($mainContentHashElementsInPath as $mainContentHashElementsInPathDetails) {
                    if ($mainContentHashElementsInPathDetails->id == $mainContentHashDetails->id) {
                        $this->deleteFiles([$mainContentHashElementsInPathDetails]);
                    }
                }
            } else {
                $this->deleteFiles($mainContentHashElementsInPath);
            }
        }
    }

    private function deleteFiles(array $files) {
        global $DB;
        
        $fileIds = [];
        
        foreach ($files as $file) {
            $fileIds[] = $file->id;
        }
        
        $where = "id IN (" . implode(',', $fileIds) . ")";

        $DB->delete_records_select('files', $where);
        echo "Successfully deleted files." . PHP_EOL;
        foreach ($files as $file) {
            echo "File ID: {$file->id}, contenthash: {$file->contenthash}, "
                . "itemid: {$file->itemid}, component: {$file->component}, "
                . "filearea: {$file->filearea}, filename: {$file->filename}" . PHP_EOL;
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
