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
 * Exports libraries from H5p to file in csv format. In fact content type in H5p is just
 *  a runnable library. Be aware that H5p Plugin libraries are stored separately from H5p Core libraries.
 * moosh hp5-core-libraries-export [-n, --name]
 *
 * @example 1: Export libraries with default filename (h5p-core-libraries-export.csv).
 * moosh hp5-core-libraries-export
 *
 * @example 2: Export libraries to with custom filename: "my-custom-file.csv"
 * moosh hp5-core-libraries-export -n my-custom-file
 *
 * @example 3: Export libraries to txt file (using csv format)
 * moosh hp5-core-libraries-export -n my-custom-txt-file.txt
 *
 * @author Michal Chruscielski <michalch775@gmail.com>
 */
class H5pCoreLibrariesExport extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('libraries-export', 'h5p-core');

        $this->addOption('n|name:', 'name of exported csv file', "h5p-core-libraries-export.csv");
    }

    public function execute()
    {
        $filename = $this->expandedOptions['name'];

        $manager = new H5PCoreExportManager();
        $manager->exportLibraries($filename, $this->verbose);

        if($this->verbose) {
            mtrace("Libraries export successful.");
        }
    }
}