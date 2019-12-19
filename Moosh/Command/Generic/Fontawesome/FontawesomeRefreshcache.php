<?php
/**
 * moosh - Moodle Shell
 *
 * @author     Soar Technology (soartech.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Generic\Fontawesome;

use Moosh\MooshCommand;

class FontawesomeRefreshcache extends MooshCommand {
    public function __construct() {
        parent::__construct('refreshcache', 'fontawesome');
    }

    public function execute() {
        $cache = \cache::make('core', 'fontawesomeiconmapping');
        $cache->delete('mapping');
        $instance = \core\output\icon_system::instance(\core\output\icon_system::FONTAWESOME);
        $instance->get_icon_name_map();
        echo "Successfully refreshed FontAwesome cache.";
    }
}
