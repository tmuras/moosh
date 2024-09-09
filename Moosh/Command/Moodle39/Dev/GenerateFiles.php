<?php
/**
 * This command creates new mod_resource in course with <courseid> with a selected number of files and their size.
 * moosh generate-files [-n, --name] [-s, --section] <courseid> <filescount> <filesize>
 *
 * Example of total sizes:
 * - 1KB (1 file x 1024 Bytes),
 * - 1MB (64 files x 16384 Bytes),
 * - 10MB (128 files x 81920 Bytes),
 * - 100MB (1024 files x 102400 Bytes),
 * - 1GB (16384 files x 65536 Bytes)
 * - 2GB (32768 files x 65536 Bytes)
 *
 * Add 1 file that weighs 1MB into course with id = 4
 * @example moosh generate-files 4 1 1048576
 *
 * Add 1000 files (1KB each) into course with id=4, the file should be named 'Test' and placed in section number 3
 * @example moosh generate-files -n 'Test' -s 3 4 1000 1024
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @introduced 2021-07-30
 * @author     Jakub Kleban <jakub.kleban2000@gmail.com>
 */

namespace Moosh\Command\Moodle39\Dev;
use Moosh\MooshCommand;

class GenerateFiles extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('files', 'generate');

        $this->addArgument('courseid');
        $this->addArgument('filescount');
        $this->addArgument('filesize');
        $this->addOption('n|name:', 'name of new mod_resource', 'Files');
        $this->addOption('s|section:', 'section for new mod_resource', 0);
    }

    public function execute()
    {
        $this->load_variables();

        // Switch to admin user account.
        \core\session\manager::set_user(get_admin());

        $this->make();
        echo PHP_EOL.'Course: ' . course_get_url($this->courseid) . PHP_EOL;
    }

    /**
     * @var string Course id
     */
    private $courseid;

    /**
     * @var int Number of files created in a single file activity
     */
    private $filescount;

    /**
     * @var int Size of each file
     */
    private $filesize;

    /**
     * @var testing_data_generator Data generator
     */
    private $generator;

    /**
     * @var int Epoch time at which last dot was displayed
     */
    private $lastdot;

    /**
     * @var int Epoch time at which last percentage was displayed
     */
    private $lastpercentage;

    /**
     * @var int Epoch time at which current step (current set of dots) started
     */
    private $starttime;

    /**
     * @var string name of new mod_resource
     */
    private $modulename;

    /**
     * @var int section for new mod_resource
     */
    private $section;

    private function load_variables() {
        global $DB;
        $options = $this->expandedOptions;

        $this->courseid = $this->arguments[0];

        // Check if course exists.
        if (!$this->course_exists($this->courseid)) {
            cli_error("Course with id: $this->courseid doesn't exists.");
        }

        $this->filescount = $this->arguments[1];
        $this->filesize = $this->arguments[2];

        $this->modulename = $options['name'];
        $this->section = $options['section'];
    }

    /**
     * Runs the entire 'make' process.
     *
     * @return int Course id
     */
    private function make() {
        global $CFG;
        require_once($CFG->dirroot.'/lib/phpunit/classes/util.php');

        raise_memory_limit(MEMORY_EXTRA);

        $entirestart = microtime(true);

        // Get generator.
        $this->generator = \phpunit_util::get_data_generator();

        $this->create_files();

        $filessize = $this->human_filesize($this->filesize * $this->filescount);
        echo "Created '$this->modulename' mod_resource with ($this->filescount files x $this->filesize Bytes ".
            "= $filessize) in course $this->courseid under section $this->section\n";
    }

    /**
     * Creates one resource activity with a lot of small files.
     */
    private function create_files() {
        global $CFG;
        require_once($CFG->dirroot.'/course/lib.php');

        $this->log();
        $count = $this->filescount;

        // Create resource with default textfile only.
        $resourcegenerator = $this->generator->get_plugin_generator('mod_resource');
        $record = array('course' => $this->courseid, 'name' => $this->modulename);
        \course_create_sections_if_missing($this->courseid, $this->section);
        $options = array('section' => $this->section);
        $resource = $resourcegenerator->create_instance($record, $options);

        // Add files.
        $fs = get_file_storage();
        $context = \context_module::instance($resource->cmid);
        $filerecord = array('component' => 'mod_resource', 'filearea' => 'content',
            'contextid' => $context->id, 'itemid' => 0, 'filepath' => '/');
        for ($i = 0; $i < $count; $i++) {
            $filerecord['filename'] = 'smallfile' . $i . '.dat';

            // Generate random binary data (different for each file so it
            // doesn't compress unrealistically).
            $data = random_bytes($this->filesize);

            $fs->create_file_from_string($filerecord, $data);
            $this->dot($i, $count);
        }

        $this->end_log();
    }

    private function course_exists($courseid) {
        global $DB;
        if ($DB->count_records('course', ['id' => $courseid]) == 1) {
            return true;
        } else {
            return false;
        }
    }

    private function human_filesize($bytes, $dec = 2)
    {
        $size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

    private function log() {
        echo "* Creating files ($this->filescount): ";
        $this->lastdot = time();
        $this->lastpercentage = $this->lastdot;
        $this->starttime = microtime(true);
    }

    /**
     * Outputs dots. There is up to one dot per second. Once a minute, it
     * displays a percentage.
     * @param int $number Number of completed items
     * @param int $total Total number of items to complete
     */
    private function dot($number, $total) {
        $now = time();
        if ($now == $this->lastdot) {
            return;
        }
        $this->lastdot = $now;

        echo '.';

        if ($now - $this->lastpercentage >= 30) {
            echo round(100.0 * $number / $total, 1) . '%';
            $this->lastpercentage = $now;
        }
    }

    /**
     * Ends a log string that was started using log function with $leaveopen.
     */
    private function end_log() {
        $time = round(microtime(true) - $this->starttime, 1);
        echo "done ($time s)\n";
    }
}

