<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Nadav Kavalerchik <nadavkav@gmail.com>
 */

namespace Moosh\Command\Moodle39\Filter;
use Moosh\MooshCommand;
use core_plugin_manager;

class FilterSet extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('set', 'filter');

        $this->addArgument('name');
        $this->addArgument('newstate'); // On = 1 , Off/but available per course = -1 , Off = -9999
        // TODO: Add proper string flags instead of state numbers, for newstate.
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once($CFG->libdir.'/adminlib.php');       // various admin-only functions
        require_once($CFG->libdir.'/classes/plugin_manager.php');

        $filtername = $this->arguments[0];
        $newstate = $this->arguments[1];

        if($newstate != 1 && $newstate != -1 && $newstate != -9999) {
            cli_error("Invalid filter value, use: 1 for on, -1 per course, -9999 for off");
        }

        // Clean up bogus filter states first.
        $plugininfos = core_plugin_manager::instance()->get_plugins_of_type('filter');
        $filters = array();
        $states = filter_get_global_states();
        foreach ($states as $state) {
            if (!isset($plugininfos[$state->filter]) and !get_config('filter_'.$state->filter, 'version')) {
                // Purge messy leftovers after incorrectly uninstalled plugins and unfinished installs.
                $DB->delete_records('filter_active', array('filter' => $state->filter));
                $DB->delete_records('filter_config', array('filter' => $state->filter));
                error_log('Deleted bogus "filter_'.$state->filter.'" states and config data.');
            } else {
                $filters[$state->filter] = $state;
            }
        }

        if (!isset($filters[$filtername])) {
            cli_error("Invalid filter name: '$filtername''. Possible values: " . implode(",", array_keys($filters)) . '.');
        }

        filter_set_global_state($filtername, $newstate);
        if ($newstate == TEXTFILTER_DISABLED) {
            filter_set_applies_to_strings($filtername, false);
        }

        reset_text_filters_cache();
        core_plugin_manager::reset_caches();

        echo "Updated $filtername to state = $newstate\n";
    }
}
