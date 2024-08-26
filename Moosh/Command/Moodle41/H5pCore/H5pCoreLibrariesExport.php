<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Michal Chruscielski <michalch775@gmail.com>
 */


namespace Moosh\Command\Moodle41\H5pCore;
use Moosh\MooshCommand;

class H5pCoreLibrariesExport extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('libraries-export', 'h5-core');

        $this->addOption('n|name:', 'name of exported csv file', "h5p-core-contenttypes-export.csv");
    }

    public function execute()
    {
        $filename = $this->expandedOptions['name'];

        $manager = new H5PCoreLibraryExportManager();
        $manager->exportLibraries($filename, $this->verbose);
    }
}