<?php
/**
 * Edits default mode mappings
 * moosh cache-edit-mappings [-a, --application] [-s, --session] [-r, --request]
 *
 * Show default mode mappings without changing
 * @example moosh cache-edit-mappings
 *
 * Set MODE_APPLICATION to "new"
 * @example moosh cache-edit-mappings --application new
 *
 * Set Application to "store name", Session to "Tests" and Request to "default_request"
 * @example moosh cache-edit-mappings -a "store name" -s Tests -r default_request
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @introduced 2021-02-01
 * @author     Jakub Kleban <jakub.kleban2000@gmail.com>
 */

namespace Moosh\Command\Moodle39\Cache;
use Moosh\MooshCommand;

class CacheEditMappings extends MooshCommand {

    public function __construct() {
        parent::__construct('edit-mappings', 'cache');

        $this->addOption('a|application:', 'Give an application caches store name. These are shared caches.');
        $this->addOption('s|session:', "Give a session caches store name. Just access to the PHP session.");
        $this->addOption('r|request:', "Give a request caches store name. Static caches really.");
    }

    public function execute() {
        global $CFG;

        $options = $this->expandedOptions;

        require_once($CFG->dirroot.'/cache/locallib.php');
        require_once($CFG->dirroot.'/cache/lib.php');
        //require_once($CFG->dirroot.'/cache/classes/store.php');

        //Get store configuration
        $cacheconfig = new \cache_config();
        if (!$cacheconfig->load()){
            cli_error("Unable to load configuration!");
        }
        $defaultconfig = $cacheconfig->get_mode_mappings();

        $data = new \stdClass();
        if ($options['application']){
            $data->{'mode_'.\cache_store::MODE_APPLICATION} = $options['application'];
        }
        else {
            $data->{'mode_'.\cache_store::MODE_APPLICATION} = $defaultconfig[0]['store'];
        }

        if ($options['session']){
            $data->{'mode_'.\cache_store::MODE_SESSION} = $options['session'];
        }
        else {
            $data->{'mode_'.\cache_store::MODE_SESSION} = $defaultconfig[1]['store'];
        }

        if ($options['request']){
            $data->{'mode_'.\cache_store::MODE_REQUEST} = $options['request'];
        }
        else {
            $data->{'mode_'.\cache_store::MODE_REQUEST} = $defaultconfig[2]['store'];
        }

        $mappings = array(
            \cache_store::MODE_APPLICATION => array($data->{'mode_'.\cache_store::MODE_APPLICATION}),
            \cache_store::MODE_SESSION => array($data->{'mode_'.\cache_store::MODE_SESSION}),
            \cache_store::MODE_REQUEST => array($data->{'mode_'.\cache_store::MODE_REQUEST}),
        );

        $writer = \cache_config_writer::instance();

        try {
            $writer->set_mode_mappings($mappings);
        }
        catch (\cache_exception $e) {
            cli_error($e->getMessage());
        }

        echo "Config Mode Mappings:";
        echo "\nMODE_APPLICATION: ".$mappings[\cache_store::MODE_APPLICATION][0];
        echo "\nMODE_SESSION: ".$mappings[\cache_store::MODE_SESSION][0];
        echo "\nMODE_REQUEST: ".$mappings[\cache_store::MODE_REQUEST][0];

        echo "\n\nDefault mode mappings ";
        if ($options['application'] || $options['session'] || $options['request']) {
            echo "edited\n";
        }
        else {
            echo "not changed\n";
        }

        exit(0);
    }
}