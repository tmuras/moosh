<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\File;
use Moosh\MooshCommand;

class FileUpload extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('upload', 'file');

        $this->addOption('f|filename', 'change name of file saved to moodle');
        $this->addOption('m|mintype', 'set type of displayed miniature');
        $this->addOption('l|license', 'set license of upload file');
        $this->addOption('c|contextid', 'set context id');
        $this->addOption('r|replace', 'replace existing file');

        $this->addArgument('file_path');
        $this->addArgument('user_id');
        $this->minArguments = 1;
        $this->maxArguments = 255;
    }

    public function execute()
    {
        global $CFG, $DB;

        if ($this->expandedOptions['license']) {
            $license = $this->expandedOptions['license'];
        } else {
            $license = "allrightsreserved";
        }

        if ($this->expandedOptions['contextid']) {
            $contextid = $this->expandedOptions['contextid'];
        } else {
            $contextid = "5";
        }

        if ($this->expandedOptions['mintype']) {
            $mintype = $this->expandedOptions['mintype'];
        } else {
            $mintype = "textplain";
        }

        if ($this->expandedOptions['filename']) {
            $filename = $this->expandedOptions['filename'];
        } else {
            $filename = basename($this->arguments[0]);
        }

        $contenthash = sha1_file($this->arguments[0]);
        $pathnamehash = $this->getFilepathHash($this->arguments[0], $contextid);
        $component = "user";
        $filearea = "private";
        $itemid = "0";
        $filepath = "/";
        $userid = $this->arguments[1];
        $filesize = filesize($this->arguments[0]);
        $status = "0";
        $source = basename($this->arguments[0]);
        $author = "Admin User";
        $timecreated = time();
        $timemodified = time();
        $sortorder = "0";

        $sql = "INSERT INTO {files}
                VALUES (NULL, \"$contenthash\", \"$pathnamehash\", \"$contextid\", \"$component\", \"$filearea\", \"$itemid\", \"$filepath\", \"$filename\", \"$userid\", \"$filesize\", \"$mintype\", \"$status\", \"$source\", \"$author\", \"$license\", \"$timecreated\", \"$timemodified\", \"$sortorder\", NULL, NULL, NULL)";


        $destDir = $CFG->dataroot . "/filedir/" . $this->getFileDirPath($this->arguments[0]);
        $dest = $destDir . "/" . $contenthash;

        echo "DUPADUPADUAP" . $destDir . " " . $dest . "\n";

        if (!mkdir($destDir, 0777, true)) {
            die('Failed to create folders...');
        }

        $source = $this->arguments[0];
        if (!copy($source, $dest)) {
            die('Failed to copy uploaded file...');
        }

        $DB->execute($sql);


           
    }

    private function getFilepathHash($filename, $contextid, $filepath = "")
    {
        $contextid = "/" + $contextid;
        $component = "/user";
        $filearea = "/private";
        $itemid = "/0";
        $filename = "/" . $filename;
        return sha1($contextid . $component . $filearea . $itemid . $filepath . $filename); 
    }

    private function getFileDirPath($filepath)
    {       
        $hash = sha1_file($filepath);
        $path = substr($hash, 0, 2) . "/" . substr($hash, 2, 2);
        return $path;
    }
}
