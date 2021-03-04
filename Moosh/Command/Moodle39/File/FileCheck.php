<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle35\File;
use Moosh\MooshCommand;

class FileCheck extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('check', 'file');

        //$this->addArgument('name');

        //$this->addOption('t|test', 'option with no value');
        $this->addOption('s|stop:', 'Stop after this many missing files found. Set to 0 for no limit.', 100);

    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_NONE;
    }

    public function execute()
    {
        global $DB, $CFG;

        // Some variables you may want to use
        //  $this->cwd - the directory where moosh command was executed
        //  $this->mooshDir - moosh installation directory
        //  $this->expandedOptions - commandline provided options, merged with defaults
        //  $this->topDir - top Moodle directory
        //  $this->arguments[0] - first argument passed
        //  $this->pluginInfo - array with information about the current plugin (based on cwd), keys:'type','name','dir'
        //  $this->verbose - if set to true, then "moosh -v" was run - add more verbose / debug information

        $CFG = read_config($this->cwd . '/config.php');
        $CFG->dirroot = $this->cwd;

        $CFG->tempdir = $CFG->dataroot . '/temp';
        $CFG->admin = $CFG->dirroot . '/admin';
        $CFG->cachedir = $CFG->dirroot . '/cache';

        $CFG->libdir = $this->cwd  .'/lib';

        $CFG->directorypermissions = 02777;
        $CFG->filepermissions = ($CFG->directorypermissions & 0666);
        $CFG->umaskpermissions = (($CFG->directorypermissions & 0777) ^ 0777);
        $CFG->debugdeveloper = NULL;
        $CFG->langotherroot = NULL;
        $CFG->langlocalroot = NULL;

        define('CACHE_DISABLE_ALL', false);

        require_once($CFG->libdir .'/setuplib.php');        // Functions that MUST be loaded first
        require_once($CFG->libdir .'/classes/component.php');

        spl_autoload_register('core_component::classloader');
        // Load up standard libraries
        //require_once($CFG->libdir .'/filterlib.php');       // Functions for filtering test as it is output
        //require_once($CFG->libdir .'/ajax/ajaxlib.php');    // Functions for managing our use of JavaScript and YUI
        require_once($CFG->libdir .'/weblib.php');          // Functions relating to HTTP and content
        //require_once($CFG->libdir .'/outputlib.php');       // Functions for generating output
        //require_once($CFG->libdir .'/navigationlib.php');   // Class for generating Navigation structure
        require_once($CFG->libdir .'/dmllib.php');          // Database access
        //require_once($CFG->libdir .'/datalib.php');         // Legacy lib with a big-mix of functions.
        //require_once($CFG->libdir .'/accesslib.php');       // Access control functions
        //require_once($CFG->libdir .'/deprecatedlib.php');   // Deprecated functions included for backward compatibility
        require_once($CFG->libdir .'/moodlelib.php');       // Other general-purpose functions
        //require_once($CFG->libdir .'/enrollib.php');        // Enrolment related functions
        require_once($CFG->libdir .'/pagelib.php');         // Library that defines the moodle_page class, used for $PAGE
        //require_once($CFG->libdir .'/blocklib.php');        // Library for controlling blocks
        //require_once($CFG->libdir .'/eventslib.php');       // Events functions
        //require_once($CFG->libdir .'/grouplib.php');        // Groups functions
        require_once($CFG->libdir .'/sessionlib.php');      // All session and cookie related stuff
        //require_once($CFG->libdir .'/editorlib.php');       // All text editor related functions and classes
        //require_once($CFG->libdir .'/messagelib.php');      // Messagelib functions
        require_once($CFG->libdir .'/modinfolib.php');      // Cached information on course-module instances
        //require_once($CFG->dirroot.'/cache/lib.php');       // Cache API

        setup_DB();
        $options = $this->expandedOptions;
        $stopafter = (int)$options['stop'];
        $missing = 0;
        $filestorage = get_file_storage();
        $filesystem = $filestorage->get_file_system();
        $rs = $DB->get_recordset_sql("SELECT MAX(id) AS id, contenthash FROM {files} GROUP BY contenthash");
        foreach ($rs as $file) {
            $line = array();
            /** @var \stored_file $fileobject */
            $fileobject = $filestorage->get_file_by_id($file->id);
            $fileexists = $filesystem->is_file_readable_locally_by_hash($file->contenthash);

            if (!$fileexists) {
                $contenthash = $fileobject->get_contenthash();
                $l1 = $contenthash[0].$contenthash[1];
                $l2 = $contenthash[2].$contenthash[3];
                echo "Missing " . $CFG->dataroot.DIRECTORY_SEPARATOR.'filedir/' . $l1 . '/' . $l2 . '/' .$contenthash . "\n";
                echo $fileobject->get_component() . ' / ' . $fileobject->get_filearea() . ' "' . $fileobject->get_filename() . '" ' .
                        date('Y-m-d H:i' ,$fileobject->get_timecreated()) . ' / ' . date('Y-m-d H:i' ,$fileobject->get_timemodified()) . "\n";
                $missing++;
                if ($stopafter && $missing === $stopafter) {
                    echo "Found $missing missing files, not searching anymore. Set -s 0 option to disable the limit.\n";
                    break;
                }
            }
        }
        $rs->close();
    }
}
