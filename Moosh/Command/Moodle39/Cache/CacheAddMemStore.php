<?php
/**
 * Adds a new mem store instance to cache.
 * moosh cache-add-mem-store [-p, --password] [-k, --key-prefix] <name> <server>
 *
 * @example 1: Add new instance "Test" with server set to "localhost".
 * moosh cache-add-mem-store "Test" "localhost"
 *
 * @example 2: Add new instance "Test2" with multiple servers
 * moosh cache-add-mem-store "Test2" "192.168.0.1,192.168.0.2"
 *
 * @example 3: Add new instance "Test3" with server set to "localhost", password set to "123456" and key prefix set to "key_"
 * moosh cache-add-mem-store --password "123456" -k "key_" "Test3" "localhost"
 *
 * @example 4: Add new instance "Test4" with server set to "localhost", with serialiser and compression enabled and password set to "12345"
 * moosh cache-add-mem-store --compression "1" --serialiser "1" --password "12345" "Test4" "localhost"
 *
 * @copyright  2022 Ryan Skelton, Synergy Learning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Ryan Skelton.
 */

namespace Moosh\Command\Moodle39\Cache;
use Moosh\MooshCommand;

class CacheAddMemStore extends MooshCommand {

    private const PLUGIN_NAME = 'memcached';
    private const DEFAULT_EDITING = 0;
    private const DEFAULT_LOCKING = null;

    private const MAP_HASH = array(
        "md5"      => \Memcached::HASH_MD5,
        "crc"      => \Memcached::HASH_CRC,
        "fnv1_64"  => \Memcached::HASH_FNV1_64,
        "fnv1a_64" => \Memcached::HASH_FNV1A_64,
        "fnv1_32"  => \Memcached::HASH_FNV1_32,
        "fnv1a_32" => \Memcached::HASH_FNV1A_64,
        "hsieh"    => \Memcached::HASH_HSIEH,
        "murmur"   => \Memcached::HASH_MURMUR
    );

    public function __construct() {
        parent::__construct('add-mem-store', 'cache');

        $this->addArgument('Name');
        $this->addArgument('Server');
        $this->addOption('p|password:', "The server connection password");
        $this->addOption('k|key-prefix:', "The key prefix");
        $this->addOption('c|compression:', "Use compression for Mem Cache");
        $this->addOption('h|hash:', "Sets the hash method for the Cache storage");
        $this->addOption('b|bufferwrites:', "Determines whether a buffer is written");
        $this->addOption('z|serialiser:', "Determines whether the cache store is serialised");
        $this->addOption('s|shared:', "Enables the cache to be shared");
        $this->addOption('e|enable-set-cluster:', "Enables the set clusters to be enabled.");
        $this->addOption('v|set-cluster-servers:', "Sets certain cluster servers to be added to cache store");
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
        $this->defineData($data, $options);

        $this->action($data, $cacheconfig);   
    }

    private function defineData(\stdClass $data, $options): void {
        $data->plugin = self::PLUGIN_NAME;
        $data->name = $this->arguments[0];
        $data->servers = str_replace(',', "\n", $this->arguments[1]);

        $data->prefix = $options['key-prefix'];
        $data->password = $options['password'];
        $data->isshared = $options['shared'] ? 1 : 0;
        $data->compression = $options['compression'] ? 1 : 0;
        $data->bufferwrites = $options['bufferwrites'] ? 1 :0;
        $data->clustered = $options['enable-set-cluster'] ? 1 : 0;

        $data->lock = self::DEFAULT_LOCKING;
        $data->editing = self::DEFAULT_EDITING;

        $data->hash = $this->map_hash($options['hash']);
        $data->setservers = str_replace(',', "\n", $options['set-cluster-servers']);
        $data->serialiser = (empty($options['serialiser'])) ? \Memcached::SERIALIZER_PHP : $options['serialiser'];
    }

    private function action(\stdClass $data, \cache_config $cacheconfig): void {
        $config = \cache_administration_helper::get_store_configuration_from_data($data);
        $writer = \cache_config_writer::instance();

        if (array_key_exists($data->name, $cacheconfig->get_all_stores())){
            cli_error("Duplicate name specificed for cache plugin instance $data->name. You must provide a unique name.");
        }

        $writer->add_store_instance($data->name, $data->plugin, $config);
        exit(0);
    }

    private function map_hash(string $hash): int {
        $hash = strtolower($hash);
        return $this::MAP_HASH[$hash] ?? \Memcached::HASH_DEFAULT;
    }
}
