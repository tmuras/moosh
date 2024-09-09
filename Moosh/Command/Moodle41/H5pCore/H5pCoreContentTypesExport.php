<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle41\H5pCore;
use Moosh\MooshCommand;


/**
 * Exports content types from H5p core to file in csv format. In fact content type in H5p is just
 *  a runnable library. Be aware that H5p Plugin libraries are stored separately from H5p Core libraries.
 * moosh hp5-core-contenttypes-export [-n, --name]
 *
 * @example 1: Export content types with default filename (h5p-core-contenttypes-export.csv).
 * moosh hp5-core-contenttypes-export
 *
 * @example 2: Export content types to with custom filename: "my-custom-file.csv"
 * moosh hp5-core-contenttypes-export -n my-custom-file
 *
 * @example 3: Export content types to txt file (using csv format)
 * moosh hp5-core-contenttypes-export -n my-custom-txt-file.txt
 *
 * @author Michal Chruscielski <michalch775@gmail.com>
 */
class H5pCoreContentTypesExport extends MooshCommand
{

    public function __construct()
    {
        parent::__construct('contenttypes-export', 'h5p-core');

        $this->addOption('n|name:', 'name of exported csv file', "h5p-core-contenttypes-export.csv");
    }

    public function execute()
    {
        $filename = $this->expandedOptions['name'];

        $manager = new H5PCoreExportManager();
        $manager->exportContentTypes($filename, $this->verbose);

        if($this->verbose) {
            mtrace("Content types export successful.");
        }
    }
}
