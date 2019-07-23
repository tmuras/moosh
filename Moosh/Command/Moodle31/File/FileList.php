<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2018 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle31\File;

use Moosh\MooshCommand;

class FileList extends MooshCommand {

    private $headerbasic = ['id', 'status', 'flags', 'hash', 'creationtime', 'size', 'path'];
    private $headeradvanced = ['moodleid', 'mime', 'userid', 'location'];
    private $headerall = [];

    public function __construct() {
        parent::__construct('list', 'file');
        $this->headerall = array_merge($this->headerbasic, $this->headeradvanced);

        $this->addOption('a|all', 'display all possible information');
        $this->addOption('c|course:', 'select files only from one course ID');
        $this->addOption('i|id', 'display IDs only - used for piping into other file-related commands. Implies --no-header.');
        $this->addOption('h|header:', 'display custom header, possible comma-separated values: ' . implode(',', $this->headerall));
        $this->addOption('n|no-header', "don't show the first line with the header.");

        $this->addArgument('expression');

        // Allow for moosh file-list -c 123
        $this->minArguments = 0;
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

        $query = null;
        if(isset($this->arguments[0])) {
            $query = trim($this->arguments[0]);
        }

        // Check if asking for course files: course=NNN.
        $courseid = $this->expandedOptions['course'];
        if($courseid) {
            // Get context path for course.
            $context = \context_course::instance($courseid);
            $contexts = array($context->get_course_context()->id);
            $results = $DB->get_records_sql("SELECT * FROM {context} WHERE path LIKE '" . $context->get_course_context()->path .
                    "/%'");
            foreach ($results as $result) {
                $contexts[] = $result->id;
            }
            list($coursecontextsql, $coursecontextparams) = $DB->get_in_or_equal($contexts);
        }

        $match = null;
        $finalquery = "SELECT id FROM {files} WHERE filename <> '.'";
        $debugquery = $finalquery;
        $params = [];
        if ($courseid) {
            $finalquery .= " AND contextid $coursecontextsql";
            $debugquery .= " AND contextid <COURSE_CONTEXTS_LIST>";
            $params = $coursecontextparams;
        }
        if($query) {
            $finalquery .= " AND $query";
            $debugquery .= " AND $query";
        }

        if($this->verbose) {
            echo $debugquery. "\n";
        }

        $rs = $DB->get_recordset_sql($finalquery, $params);

        // What fields do we display - set up header.
        // By default just basic options.
        $header = $this->headerbasic;
        if ($this->expandedOptions['all']) {
            $header = $this->headerall;
        }
        if ($this->expandedOptions['header']) {
            $header = explode(',', $this->expandedOptions['header']);
        }

        // Validate header.
        if (!count($header)) {
            cli_error("Invalid header requested");
        }

        $diff = array_diff($header, $this->headerall);
        if (count($diff)) {
            cli_error("Invalid header field(s) requested: " . implode(',',$diff));
        }


        if ($this->expandedOptions['id'] || $this->expandedOptions['no-header']) {
            $output = [];
        } else {
            $output[] = $header;
        }

        foreach ($rs as $file) {
            if ($this->expandedOptions['id']) {
                $output[] = array($file->id);
                continue;
            }
            $line = array();

            $fileobject = $fs->get_file_by_id($file->id);

            foreach ($header as $column) {
                $line[] = $this->get_column($fileobject, $column);
            }

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
     *
     * @param $fileid
     */
    protected function get_location(\stored_file $file) {
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

    /**
     * @param \stored_file $fileobject
     * @return string
     */
    protected function get_flags(\stored_file $fileobject) {
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
        return $flags;
    }

    protected function get_column(\stored_file $fileobject, $name) {
        global $DB;

        // 'id', 'status', 'flags', 'hash', 'creationtime', 'size', 'path'
        // 'moodleid', 'mime','userid', 'location'
        switch($name) {
            case 'id':
                return $fileobject->get_id();
                break;
            case 'status':
                return $fileobject->get_status();
                break;
            case 'flags':
                return $this->get_flags($fileobject);
                break;
            case 'hash':
                return $fileobject->get_contenthash();
                break;
            case 'creationtime':
                return userdate($fileobject->get_timecreated());
                break;
            case 'size':
                return $fileobject->get_filesize();
                break;
            case 'path':
                return substr($fileobject->get_filepath(), 1) . $fileobject->get_filename();
                break;
            case 'moodleid':
                return $fileobject->get_contextid() . ':' . $fileobject->get_component() . ':' . $fileobject->get_filearea() . ':' .
                        $fileobject->get_itemid();
                break;
            case 'mime':
                return $fileobject->get_mimetype();
                break;
            case 'userid':
                $user = $DB->get_record('user', array('id' => $fileobject->get_userid()));
                return $user->username . " ({$user->id})";
                break;
            case 'location':
                return $this->get_location($fileobject);
                break;
        }
    }
}


