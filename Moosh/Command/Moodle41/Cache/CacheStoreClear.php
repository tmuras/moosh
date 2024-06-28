<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright 2021 unistra {@link http://unistra.fr}
 * @author 2021 Céline Perves <cperves@unistra.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle41\Cache;
use Moosh\MooshCommand;

class CacheStoreClear extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('store-clear', 'cache');
        $this->addOption('d|definition:', 'cache definition, command will only purge this cache definition');
        $this->addArgument('storename');
        $this->minArguments = 1;
    }

    public function execute()
    {
        global $CFG;
        require_once($CFG->dirroot.'/cache/classes/factory.php');
        require_once($CFG->dirroot.'/cache/classes/helper.php');
        $instance = \cache_config::instance();
        $factory = \cache_factory::instance();
        $config = $factory->create_config_instance();
        $stores = $instance->get_all_stores();
        $this->expandOptionsManually($this->arguments);
        $options = $this->expandedOptions;
        $storename = $this->arguments[0];
        if (!array_key_exists($storename, $stores)) {
            cli_error("Store with name $storename does not exists.");
        }
        $storedetails= $stores[$storename];
        $class = $storedetails['class'];
        $storeinstance = new $class($storedetails['name'], $storedetails['configuration']);
        if (!$storeinstance->are_requirements_met()){
            cli_error("Store $storename does not meet requirements.");
        }
        if(!$storeinstance->is_ready()) {
            cli_error("Store $storename si not ready.");
        }
        $definitionid = $options['definition'];
        if (!empty($options['definition'])) {
            $definitions = $config->get_definitions();
            if (!array_key_exists($definitionid, $definitions)) {
                cli_error("Cache definition $definitionid not found in the $storename cache store.");
            }
            $definition = $definitions[$definitionid];
            $definition = \cache_definition::load($definitionid, $definition);
            $definitioninstance = clone($storeinstance);
            $definitioninstance->initialise($definition);
            if (!$definitioninstance->purge()) {
                cli_error("For $storename cache store , Cache definition $definitionid purge return false.");
            } else {
                cli_writeln("Cache definition $definitionid in $storename cache store sucessfully purged.");
            }
            unset($definitioninstance);
        } else {
            // Purge all cache definitions.
            if (\cache_helper::purge_store($storename,$config)) {
                cli_writeln("Store $storename purged.");
            } else {
                cli_error("Store $storename purge returned false.");
            }
        }
    }

    protected function getArgumentsHelp() {
        $help = parent::getArgumentsHelp();
        $help .= "\n\n";
        $help .= "Clear a specific cache";
        $help .= "\nIt is possible to only clean a cache definition";

        return $help;
    }
}
