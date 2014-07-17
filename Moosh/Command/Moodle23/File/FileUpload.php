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

        $this->addArgument('file_path');

        $this->addOption('c|contextid:', 'set context id', "5");
        $this->addOption('p|component:', 'set type of component, user by default', "user");
        $this->addOption('f|filearea:', 'set filearea, private by default', 'private');
        $this->addOption('i|itemid:', 'set item id, default 0', "0");
        $this->addOption('s|sortorder:', 'set sortorder, 0 by default', '0');
        $this->addOption('n|filename:', 'change name of file saved to moodle, default full name');

    }

    public function execute()
    {
        global $CFG, $DB;

        $filepath = $this->arguments[0];
        if ($this->expandedOptions['filename']) {
            $filename = $this->expandedOptions['filename'];
        } else {
            $filename = basename($this->arguments[0]);
        }

        $fp = get_file_storage();

        $filerecord = new \stdClass();

        $filerecord->contextid  = $this->expandedOptions['contextid'];
        $filerecord->component  = $this->expandedOptions['component'];
        $filerecord->filearea   = $this->expandedOptions['filearea'];
        $filerecord->itemid     = $this->expandedOptions['itemid'];
        $filerecord->sortorder  = $this->expandedOptions['sortorder'];
        $filerecord->filepath   = $filepath . "/";
        $filerecord->filename   = $filename;

        $content = file_get_contents($filepath);

        try {
            $fp->create_file_from_string($filerecord, $content);
            echo "File uploaded successfully!\n";
        }
        catch (Exception $e) {
            echo "File was not uploaded. Error: " . $e . "\n";
        }
    }
}
