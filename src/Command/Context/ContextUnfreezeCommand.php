<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Context;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Unfreeze (unlock) one or more contexts.
 *
 * Canonical name: context:unfreeze  |  Alias: context-unfreeze
 */
class ContextUnfreezeCommand extends BaseCommand
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
            ->setName('context:unfreeze')
            ->setDescription('Unfreeze (unlock) one or more contexts')
            ->setHelp(<<<'HELP'
                Unfreezes contexts so that write capabilities are restored.

                By default, arguments are context IDs (from mdl_context.id).
                Use --level to pass instance IDs instead (e.g. course IDs with --level=course).
                Use --children to also unfreeze all child contexts.

                Examples:
                  context:unfreeze 42 --run
                  context:unfreeze 5 --level=course --run
                  context:unfreeze 5 --level=course --children --run
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
        return new ContextUnfreeze52Handler();
    }
}
