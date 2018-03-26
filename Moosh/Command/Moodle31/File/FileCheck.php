<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle31\File;
use Moosh\MooshCommand;

class FileCheck extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('check', 'file');

        //$this->addArgument('name');

        //$this->addOption('t|test', 'option with no value');
        //$this->addOption('o|option:', 'option with value and default', 'default');

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

        $options = $this->expandedOptions;
        $fs = get_file_storage();
        $rs = $DB->get_recordset_sql("SELECT MAX(id) AS id, contenthash FROM {files} GROUP BY contenthash");
        foreach ($rs as $file) {
            $line = array();
            /** @var \stored_file $fileobject */
            $fileobject = $fs->get_file_by_id($file->id);
            $fileexists = $fs->content_exists($fileobject->get_contenthash());

            if (!$fileexists) {
                $contenthash = $fileobject->get_contenthash();
                $l1 = $contenthash[0].$contenthash[1];
                $l2 = $contenthash[2].$contenthash[3];
                echo $CFG->dataroot.DIRECTORY_SEPARATOR.'filedir/' . $l1 . '/' . $l2 . '/' .$contenthash . "\n";
            }
        }
        $rs->close();

        /* if verbose mode was requested, show some more information/debug messages
        if($this->verbose) {
            echo "Say what you're doing now";
        }
        */
    }
}
