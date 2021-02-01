<?php
/**
 * Gets cache config and print_r() it
 * moosh cache-config-get [-a, --all] [-smdDliL]
 *
 * Show every cache config
 * @example moosh cache-config-get --all
 *
 * Show all of the configured stores
 * @example moosh cache-config-get --stores
 *
 * Show all the known definitions and definition mappings
 * @example moosh cache-config-get -dD
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @introduced 2021-01-29
 * @author     Jakub Kleban <jakub.kleban2000@gmail.com>
 */

namespace Moosh\Command\Moodle39\Cache;
use Moosh\MooshCommand;

class CacheConfigGet extends MooshCommand {

    public function __construct() {
        parent::__construct('config-get', 'cache');

        $this->addOption('a|all', "Show all");
        $this->addOption('s|stores', "Show all of the configured stores");
        $this->addOption('m|mode-mappings', "Show all of the configured mode mappings");
        $this->addOption('d|definitions', "Show all the known definitions");
        $this->addOption('D|definition-mappings', "Show all of the known definition mappings");
        $this->addOption('l|locks', "Show an array of the configured locks");
        $this->addOption('i|site-identifier', "Show the site identifier used by the cache API");
        $this->addOption('L|lock-mappings', "Show all of the known lock mappings");
    }

    public function execute() {
        global $CFG;

        $options = $this->expandedOptions;

        require_once($CFG->dirroot.'/cache/classes/config.php');

        //Get store configuration
        $cacheconfig = new \cache_config();
        if (!$cacheconfig->load()){
            cli_error("Unable to load configuration!");
        }

        $newcacheconfig = [];

        if ($options['stores'] || $options['all']) {
           $newcacheconfig += ['configstores' => $cacheconfig->get_all_stores()];
        }

        if ($options['mode-mappings'] || $options['all']) {
            $newcacheconfig += ['configmodemappings' => $cacheconfig->get_mode_mappings()];
        }

        if ($options['definitions'] || $options['all']) {
            $newcacheconfig += ['configdefinitions' => $cacheconfig->get_definitions()];
        }

        if ($options['definition-mappings'] || $options['all']){
            $newcacheconfig += ['configdefinitionmappings' => $cacheconfig->get_definition_mappings()];
        }

        if ($options['locks'] || $options['all']){
            $newcacheconfig += ['configlocks' => $cacheconfig->get_locks()];
        }

        if ($options['site-identifier'] || $options['all']){
            $newcacheconfig += ['siteidentifier' => $cacheconfig->get_site_identifier()];
        }

        if ($options['lock-mappings'] || $options['all']){
            $newcacheconfig += ['configlockmappings' => $cacheconfig->configlockmappings];
        }

        if (empty($newcacheconfig)) {
            cli_error("You have to choose an option. Have a look at --help or documentation.");
        }

        print_r($newcacheconfig);
        exit(0);
    }
}