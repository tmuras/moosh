<?php
/**
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Erick Lima (github.com/erickcomp)
 */
declare(strict_types=1);

if (!\class_exists('Memcached')) {
    /**
     *
     * This class is intented to provide Memcached constants used by "Moosh\Command\Moodle3x\Cache\CacheAddMemStore" classes
     * so the app does not break when Memcached extension is not installed
     *
     * In a php installation which has memcached installed, one can run:
     *
     *     echo '\Memcached::HASH_MD5      = ' . \Memcached::HASH_MD5       . PHP_EOL;
     *     echo '\Memcached::HASH_CRC      = ' . \Memcached::HASH_CRC       . PHP_EOL;
     *     echo '\Memcached::HASH_FNV1_64  = ' . \Memcached::HASH_FNV1_64   . PHP_EOL;
     *     echo '\Memcached::HASH_FNV1A_64 = ' . \Memcached::HASH_FNV1A_64  . PHP_EOL;
     *     echo '\Memcached::HASH_FNV1_32  = ' . \Memcached::HASH_FNV1_32   . PHP_EOL;
     *     echo '\Memcached::HASH_HSIEH    = ' . \Memcached::HASH_HSIEH     . PHP_EOL;
     *     echo '\Memcached::HASH_MURMUR   = ' . \Memcached::HASH_MURMUR    . PHP_EOL;
     *
     * And that will print:
     *
     *     \Memcached::HASH_MD5      = 1
     *     \Memcached::HASH_CRC      = 2
     *     \Memcached::HASH_FNV1_64  = 3
     *     \Memcached::HASH_FNV1A_64 = 4
     *     \Memcached::HASH_FNV1_32  = 5
     *     \Memcached::HASH_HSIEH    = 7
     *     \Memcached::HASH_MURMUR   = 8
     *
     */
    class Memcached
    {
        public const HASH_MD5      = 1;
        public const HASH_CRC      = 2;
        public const HASH_FNV1_64  = 3;
        public const HASH_FNV1A_64 = 4;
        public const HASH_FNV1_32  = 5;
        public const HASH_HSIEH    = 7;
        public const HASH_MURMUR   = 8;
    }
}
