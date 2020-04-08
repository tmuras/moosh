<?php
/**
 * Get moodle backup information
 * moosh backup-info      [<moodle_backup.mbz> ...]
 *
 * @todo       Handle tar gz archives.
 * @copyright  2016 Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Generic\Backup;

use Moosh\MooshCommand;

class BackupInfo extends MooshCommand {
    public function __construct() {
        parent::__construct('info', 'backup');

        $this->addArgument('backup');
        $this->maxArguments = 255;
    }

    public function execute() {
        foreach ($this->arguments as $argument) {
            // Detect if an archive is a zip or tar gz.
            $format = $this->detectFormat($argument);
            if($format == 'gzip') {
                $command = "tar -zxOf $argument";
            } elseif ($format == 'gzip') {
                $command = "unzip -p $argument";
            } else {
                cli_error("I don't know how to extract from the backup file.");
            }

            // Get the users.xml for each moodle backup
            $resultinfo = exec("$command users.xml ", $result, $validrun);
            echo "For Moodle backup $argument \r\n";
            if (!$validrun) {
                $elem = new \SimpleXMLElement(implode("", $result));
                $usercount = $elem->count();
                echo "Number of Users is " . $usercount . "\r\n";
                unset ($elem, $result, $resultinfo);
            } else {
                echo "Sorry the local utility unzip is not available or the moodle backup $argument does not contain a users.xml file.\r\n";
            }

            // Get the gradebook.xml for each moodle backup
            $resultinfo = exec("$command gradebook.xml ", $result, $validrun);
            if (!$validrun) {
                $elem = new \SimpleXMLElement(implode("", $result));
                $gradecount = $elem->grade_items->grade_item->grade_grades->grade_grade->count();
                echo "Number of Grades is " . $gradecount . "\r\n";
                unset ($elem, $result, $resultinfo);
            } else {
                echo "Sorry the Moodle backup $argument does not contain a gradebook.xml file.\r\n";
            }

            // Get the course>logs.xml for each moodle backup
            $resultinfo = exec("$command course/logs.xml ", $result, $validrun);
            if (!$validrun) {
                $elem = new \SimpleXMLElement(implode("", $result));
                $logcount = $elem->count();
                echo "Number of Logs is " . $logcount . "\r\n";
                unset ($elem, $result, $resultinfo);
            } else {
                echo "Sorry the Moodle backup $argument does not contain a course>logs.xml file.\r\n";
            }
        }
    }

    private function detectFormat($file) {
        $resultinfo = exec("file $file", $result, $validrun);
        if ($validrun) {
            cli_error("Could not detect the format of the backup file by runnig 'file' utility.");
        }

        $format = null;
        if (stripos(join($result), 'gzip compressed') !== false) {
            $format = 'gzip';
        } else if (stripos(join($result), 'zip archive') !== false) {
            $format = 'zip';
        }

        if (!$format) {
            cli_error("Backup file format not supported or file can't be read.");
        }
        return $format;
    }

    public function bootstrapLevel() {
        return self::$BOOTSTRAP_NONE;
    }
}    
