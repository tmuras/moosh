<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Cache;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create a cache store instance.
 *
 * Canonical name: cache:create  |  Aliases: cache-create, cache-add-mem-store, cache-add-redis-store
 */
class CacheCreateCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::Full;

    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->handler = $this->resolveHandler($moodleVersion);
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('cache:create')
            ->setDescription('Create a cache store instance')
            ->setHelp(<<<'HELP'
                Creates a new cache store instance for redis, memcached, apcu, or file backends.

                Examples:
                  cache:create redis myredis --server 127.0.0.1 --run
                  cache:create redis myredis --server 127.0.0.1:6379 --password secret --prefix mdl_ --run
                  cache:create memcached mymem --server 127.0.0.1:11211 --run
                  cache:create apcu myapcu --prefix mdl_ --run
                  cache:create file myfile --run
                HELP);

        $this->handler->configureCommand($this);
    }

    protected function getActiveHandler(): BaseHandler
    {
        return $this->handler;
    }

    protected function handle(InputInterface $input, OutputInterface $output): int
    {
        $verbose = new VerboseLogger($output);
        $verbose->step('Delegating to handler: ' . get_class($this->handler));
        return $this->handler->handle($input, $output);
    }

    private function resolveHandler(?MoodleVersion $moodleVersion): BaseHandler
    {
        return new CacheCreate51Handler();
    }
}
