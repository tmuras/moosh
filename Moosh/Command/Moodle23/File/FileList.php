<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\File;

use Moosh\MooshCommand;

class FileList extends MooshCommand {
    public function __construct() {
        parent::__construct('list', 'file');

        $this->addOption('i|id', 'display IDs only - used for piping into other file-related commands');
        $this->addOption('m|missingfromdisk', 'Check for existing DB files which are missing from the file system.');
        $this->addOption('r|removemissingfromdisk', 'Remove existing DB entries (files) which are missing from the file system.');
        $this->addOption('a|all', 'display all possible information');

        /*
contextid
component
filearea
itemid
filepath
filename
userid
filesize
mimetype
status
timecreated
timemodified
*/

        $this->addArgument('expression');
    }

    protected function getArgumentsHelp() {
        $help = parent::getArgumentsHelp();
        $help .= "\n\n";
        $help .= "To get all files from course N use 'course=N' as an argument.";

        return $help;
    }


    public function execute() {
        global $CFG, $DB;

        $fs = get_file_storage();

        $query = trim($this->arguments[0]);
        $output = array();

        //check if asking for course files: course=NNN
        $match = NULL;
        if (preg_match('/course=(\d+)/', $query, $match)) {
            //get all context IDs
            $courseid = $match[1];

            //get context path for course
            $context = \context_course::instance($courseid);
            $contexts = array($context->get_course_context()->id);
            $results = $DB->get_records_sql("SELECT * FROM {context} WHERE path LIKE '" . $context->get_course_context()->path . "/%'");
            foreach ($results as $result) {
                $contexts[] = $result->id;
            }
            list($sql, $params) = $DB->get_in_or_equal($contexts);

            $rs = $DB->get_recordset_sql("SELECT id FROM {files} WHERE filename <> '.' AND contextid $sql", $params);
        } else {
            $rs = $DB->get_recordset_sql("SELECT id FROM {files} WHERE " . $query);

        }

        // Looking in {files} table for files that are missing from the file system.
        // Use -r to remove them from the {files} table.
        if ($this->expandedOptions['missingfromdisk']) {

            foreach ($rs as $file) {
                $line = array();
                /** @var \stored_file $fileobject */
                $fileobject = $fs->get_file_by_id($file->id);
                try {
                    $fileexists = $fs->content_exists($fileobject->get_contenthash());
                } catch (Exception $e) {
                    // no file
                }
                if (!$fileexists) {
                    $contenthash = $fileobject->get_contenthash();
                    $l1 = $contenthash[0].$contenthash[1];
                    $l2 = $contenthash[2].$contenthash[3];
                    $line[] = $CFG->dataroot.DIRECTORY_SEPARATOR.'filedir/' . $l1 . '/' . $l2 . '/' .$contenthash ;
                    $output[] = $line;
                    if ($this->expandedOptions['removemissingfromdisk']) {
                        $DB->delete_records('files', array('id' => $file->id));
                    }
                }
            }
            $rs->close();

            foreach ($output as $line) {
                echo implode("\t", $line);
                echo "\n";
            }
            return 0; //die;
        }

        // Header.
        $header = array('id', 'status', 'flags', 'hash', 'time of creation', 'Moodle IDs');
        if ($this->expandedOptions['all']) {
            array_push($header,'mime', 'size', 'user (id)', 'location');
        }
        $header[] = 'path';

        if ($this->expandedOptions['id']) {
		$output = array();
	} else {
	        $output[] = $header;
	}

        foreach ($rs as $file) {
            if ($this->expandedOptions['id']) {
                $output[] = array($file->id);
                continue;
            }
            $line = array();
            /** @var \stored_file $fileobject */
            $fileobject = $fs->get_file_by_id($file->id);

            $line[] = $fileobject->get_id();

            $line[] = $fileobject->get_status();
            $flags = '';
            if ($fileobject->is_directory()) {
                $flags .= 'd';
            } else {
                $flags .= '.';
            }

            if ($fileobject->is_external_file()) {
                $flags .= 'e';
            } else {
                $flags .= '.';
            }

            if ($fileobject->is_valid_image()) {
                $flags .= 'i';
            } else {
                $flags .= '.';
            }
            if ($fileobject->get_timecreated() != $fileobject->get_timemodified()) {
                $flags .= 'm';
            } else {
                $flags .= '.';
            }
            $line[] = $flags;
            $line[] = $fileobject->get_contenthash();
            $line[] = userdate($fileobject->get_timecreated());
            $line[] = $fileobject->get_contextid() . ':' . $fileobject->get_component() . ':' . $fileobject->get_filearea() . ':' . $fileobject->get_itemid();

            if ($this->expandedOptions['all']) {
                $line[] = $fileobject->get_mimetype();
                $line[] = $fileobject->get_filesize();
                $user = $DB->get_record('user', array('id' => $fileobject->get_userid()));
                $line[] = $user->username . " ({$user->id})";
                $line[] = $this->getLocation($fileobject);
            }
            $line[] = substr($fileobject->get_filepath(), 1) . $fileobject->get_filename();

            //echo "\n\r";
            // @TODO Optionally display line & force flush after each line?
            /*
            echo chr(27) . "[0G";
            flush();
            */
            $output[] = $line;
        }
        $rs->close();

        foreach ($output as $line) {
            echo implode("\t", $line);
            echo "\n";
        }

    }

    /**
     * Get location for given mdl_file.id entry
     * @param $fileid
     */
    protected function getLocation(\stored_file $file) {
        global $CFG, $DB;
        // To handle atm:course:legacy, mod_folder:content, mod_resource:content
        if ($file->get_component() == 'course' && $file->get_filearea() == 'legacy') {
            return "Legacy course files: " . $CFG->wwwroot . "/files/index.php?contextid=" . $file->get_contextid();
        }

        if ($file->get_component() == 'mod_folder' && $file->get_filearea() == 'content') {
            $context = $DB->get_record('context', array('id' => $file->get_contextid()));
            return "Folder resource: " . $CFG->wwwroot . "/mod/folder/view.php?id=" . $context->instanceid;
        }

        if ($file->get_component() == 'mod_resource' && $file->get_filearea() == 'content') {
            $context = $DB->get_record('context', array('id' => $file->get_contextid()));
            return "Resource: " . $CFG->wwwroot . "/mod/resource/view.php?id=" . $context->instanceid;
        }

    }
}


