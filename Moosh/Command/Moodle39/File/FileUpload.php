<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Kacper Golewski <k.golewski@gmail.com>
 */

namespace Moosh\Command\Moodle39\File;
use Moosh\MooshCommand;

class FileUpload extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('upload', 'file');

        $this->addArgument('file_path');

        $this->addOption('c|contextid:', 'set context id', "5");
        $this->addOption('m|component:', 'set type of component, user by default', "user");
        $this->addOption('f|filearea:', 'set filearea, private by default', 'private');
        $this->addOption('i|itemid:', 'set item id, default 0', "0");
        $this->addOption('s|sortorder:', 'set sortorder, 0 by default', '0');
        $this->addOption('n|filename:', 'change name of file saved to moodle, default full name');
        $this->addOption('p|filepath:', 'change path of file saved to moodle, default full path');

    }

    public function execute()
    {
        $arguments = $this->arguments;
        if ($this->expandedOptions['filename']) {
            $filename = $this->expandedOptions['filename'];
        } else {
            $filename = basename($this->arguments[0]);
        }
        if ($this->expandedOptions['filepath']) {
            $savedfilepath = $this->expandedOptions['filepath'];
        } else {
            $savedfilepath = dirname($this->arguments[0]);
        }

        if ($arguments[0][0] != '/') {
            $arguments[0] = $this->cwd . DIRECTORY_SEPARATOR . $arguments[0];
        }

        if (!file_exists($arguments[0])) {
            cli_error("File '" . $arguments[0] . "' does not exist.");
        }

        if (!is_readable($arguments[0])) {
            cli_error("File '" . $arguments[0] . "' is not readable.");
        }

        $fp = get_file_storage();

        $filerecord = new \stdClass();

        $filerecord->contextid  = $this->expandedOptions['contextid'];
        $filerecord->component  = $this->expandedOptions['component'];
        $filerecord->filearea   = $this->expandedOptions['filearea'];
        $filerecord->itemid     = $this->expandedOptions['itemid'];
        $filerecord->sortorder  = $this->expandedOptions['sortorder'];
        $filerecord->filepath   = '/' . $savedfilepath . "/";
        $filerecord->filename   = $filename;

        try {
            $fp->create_file_from_pathname($filerecord, $arguments[0]);
            echo "File uploaded successfully!\n";
        }
        catch (Exception $e) {
            echo "File was not uploaded. Error: " . $e . "\n";
        }
    }
}
