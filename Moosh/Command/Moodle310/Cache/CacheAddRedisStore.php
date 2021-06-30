<?php
/**
 * Adds a new redis store instance to cache.
 * moosh cache-add-redis-store [-p, --password] [-k, --key-prefix] <name> <server>
 *
 * Add new instance "Test" with server set to "localhost"
 * @example moosh cache-add-redis-store "Test" "localhost"
 *
 * Add new instance "Test2" with server set to "localhost", password set to "123456" and key prefix set to "key_"
 * @example moosh cache-add-redis-store --password "123456" -k "key_" "Test2" "localhost"
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @introduced 2021-01-29
 * @author     Jakub Kleban <jakub.kleban2000@gmail.com>
 */

namespace Moosh\Command\Moodle310\Cache;
use Moosh\MooshCommand;

class CacheAddRedisStore extends MooshCommand {

    public function __construct() {
        parent::__construct('add-redis-store', 'cache');

        $this->addArgument('name');
        $this->addArgument('server');
        $this->addOption('p|password:', "The server connection password");
        $this->addOption('k|key-prefix:', "The key prefix");
    }

    public function execute() {
        global $CFG;

        $options = $this->expandedOptions;

        require_once($CFG->dirroot.'/cache/locallib.php');
        require_once($CFG->dirroot.'/cache/lib.php');

        //Get store configuration
        $cacheconfig = new \cache_config();
        if (!$cacheconfig->load()){
            cli_error("Unable to load configuration!");
        }

        $data = new \stdClass();
        $data->plugin = 'redis';
        $data->editing = 0;
        $data->name = $this->arguments[0];
        $data->lock = null;
        $data->server = $this->arguments[1];
        $data->password = $options['password'];
        $data->prefix = $options['key-prefix'];
        $data->serializer = 1;
        $data->compressor = 0;

        $adminhelper = \cache_factory::instance()->get_administration_display_helper();
        $config = $adminhelper->get_store_configuration_from_data($data);

        $writer = \cache_config_writer::instance();

        if (array_key_exists($data->name, $cacheconfig->get_all_stores())){
            cli_error("Duplicate name specificed for cache plugin instance $data->name. You must provide a unique name.");
        }

        $writer->add_store_instance($data->name, $data->plugin, $config);

        exit(0);
    }
}