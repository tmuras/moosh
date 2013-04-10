<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class CourseRestore extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('restore', 'course');

        $this->addArgument('backup_file');
        $this->addArgument('category_id');
    }

    public function execute()
    {
        global $CFG, $DB;

        //check if category is OK
        $arguments = $this->arguments;
        if ($arguments[0][0] != '/') {
            $arguments[0] = $this->cwd . DIRECTORY_SEPARATOR . $arguments[0];
        }
        if (!file_exists($arguments[0])) {
            cli_error("Backup file '" . $arguments[0] . "' does not exist.");
        }
        $category = $DB->get_record('course_categories', array('id' => $this->arguments[1]), '*', MUST_EXIST);


        //unzip into $CFG->dataroot . DIRECTORY_SEPARATOR . "temp" . DIRECTORY_SEPARATOR . "backup" . DIRECTORY_SEPARATOR . "auto_restore_" . $split[1];
        $path = $CFG->dataroot . DIRECTORY_SEPARATOR . "temp" . DIRECTORY_SEPARATOR . "backup" . DIRECTORY_SEPARATOR . "moosh_restore_" . uniqid();
        echo "Extracting to: '" . $path . "'\n";
        /** @var $fp file_packer */
        $fp = get_file_packer('application/vnd.moodle.backup');
        $fp->extract_to_pathname($arguments[0], $path);

        //extract original full & short names
        $xmlfile = $path . DIRECTORY_SEPARATOR . "course" . DIRECTORY_SEPARATOR . "course.xml";
        $xml = simplexml_load_file($xmlfile);
        $fullname = $xml->xpath('/course/fullname');
        $shortname = $xml->xpath('/course/shortname');
        list(, $fullname) = each($fullname);
        list(, $shortname) = each($shortname);

        echo $fullname;
        echo $shortname;
    }
}
