<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Fontawesome;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Refresh the Font Awesome icon mapping cache.
 *
 * Replaces Moosh\Command\Generic\Fontawesome\FontawesomeRefreshcache.
 * Canonical name: fontawesome:refresh-cache  |  Alias: fontawesome-refresh-cache
 */
class FontawesomeRefreshCacheCommand extends BaseCommand
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
            ->setName('fontawesome:refresh-cache')
            ->setDescription('Refresh the Font Awesome icon mapping cache')
            ->setHelp('Purges and regenerates the Font Awesome icon mapping cache. Requires --run.');

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
        if ($moodleVersion !== null && $moodleVersion->isAtLeast('5.2')) {
            return new FontawesomeRefreshCache52Handler();
        }
        return new FontawesomeRefreshCache51Handler();
    }
}
