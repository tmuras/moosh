<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle29\Cache;

use Moosh\MooshCommand;

class MemcacheTest extends MooshCommand
{
    public function __construct()
    {
        global $DB;
        parent::__construct('test', 'cache');

        //$this->addArgument('contextid');

        //$this->addOption('t|test', 'option with no value');
        //$this->addOption('o|option:', 'option with value and default', 'default');

    }

    public function execute()
    {
        cli_error('Not implemented yet');
        global $CFG, $DB;
        require_once($CFG->wwwdir . '/cache/stores/memcache/lib.php');

        //some variables you may want to use
        //$this->cwd - the directory where moosh command was executed
        //$this->mooshDir - moosh installation directory
        //$this->expandedOptions - commandline provided options, merged with defaults
        //$this->topDir - top Moodle directory
        //$this->arguments[0] - first argument passed
        //$this->pluginInfo - array with information about the current plugin (based on cwd), keys:'type','name','dir'

        $options = $this->expandedOptions;
        $contextid = $this->arguments[0];
        //$contextpath = $this->arguments[0];
        $configuration = array();
        $configuration['servers'] = '127.0.0.1';

        $start = microtime(true);
        $memcache = new cachestore_memcache('memcache_test', $configuration);
        $result = sprintf('%01.4f', microtime(true) - $start);

    }
}
