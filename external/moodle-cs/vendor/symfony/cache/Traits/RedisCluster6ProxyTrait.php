<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Cache\Traits;

if (version_compare(phpversion('redis'), '6.1.0-dev', '>')) {
    /**
     * @internal
     */
    trait RedisCluster6ProxyTrait
    {
        public function getex($key, $options = []): \RedisCluster|string|false
        {
            return ($this->lazyObjectState->realInstance ??= ($this->lazyObjectState->initializer)())->getex(...\func_get_args());
        }

        public function publish($channel, $message): \RedisCluster|bool|int
        {
            return ($this->lazyObjectState->realInstance ??= ($this->lazyObjectState->initializer)())->publish(...\func_get_args());
        }

        public function waitaof($key_or_address, $numlocal, $numreplicas, $timeout): \RedisCluster|array|false
        {
            return ($this->lazyObjectState->realInstance ??= ($this->lazyObjectState->initializer)())->waitaof(...\func_get_args());
        }
    }
} else {
    /**
     * @internal
     */
    trait RedisCluster6ProxyTrait
    {
        public function publish($channel, $message): \RedisCluster|bool
        {
            return ($this->lazyObjectState->realInstance ??= ($this->lazyObjectState->initializer)())->publish(...\func_get_args());
        }
    }
}
