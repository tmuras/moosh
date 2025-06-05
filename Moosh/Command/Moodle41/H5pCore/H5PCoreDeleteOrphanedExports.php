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
 * Deletes H5P orphaned exports.
 *  moosh hp5-core-delete-orphaned-exports [-d, --delete]
 *
 * @example 1: dry run
 * moosh hp5-core-delete-orphaned-exports
 *
 * @example 2: perform deletion
 * moosh hp5-core-delete-orphaned-exports -d
 *
 * @author Fabien Dallet <fabien.dallet@enovationsolutions.fr>
 */
class H5PCoreDeleteOrphanedExports extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('delete-orphaned-exports', 'h5p-core');

        $this->addOption('d|delete','Perform the deletion. Dry-run without this option.');
    }

    public function execute()
    {
        global $CFG, $DB;
        require_once($CFG->libdir . '/filelib.php');
        \core_h5p\local\library\autoloader::register();

        $fs = get_file_storage();
        $context = \context_system::instance();
        $count = 0;

        // Get ids of existing H5P records.
        $existingids = $DB->get_fieldset_select('h5p', 'id', '');

        // Get all H5P exports.
        $files = $fs->get_area_files($context->id, \core_h5p\file_storage::COMPONENT, \core_h5p\file_storage::EXPORT_FILEAREA, 0,
                'id', false);
        foreach ($files as $file) {
            // Extract id from filename, exports can't be identified in any other way.
            if (preg_match('/-(\d+)\.h5p$/', $file->get_filename(), $matches)) {
                $id = $matches[1];
                // Delete file if id does not correspond to an existing H5P.
                if (!in_array($id, $existingids)) {
                    if ($this->expandedOptions['delete']) {
                        $file->delete();
                    }
                    echo $file->get_filename() . ' (id ' . $file->get_id() . ') deleted.' . PHP_EOL;
                    $count++;
                }
            }
        }
        echo 'Orphaned H5P exports deleted: ' . $count . PHP_EOL;
    }
}
