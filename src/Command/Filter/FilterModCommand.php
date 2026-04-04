<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Filter;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FilterModCommand extends BaseCommand
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
            ->setName('filter:mod')
            ->setDescription('Modify a text filter state, order, or configuration')
            ->setHelp(<<<'HELP'
                Modifies filter global or local state, reorders filters, or sets configuration.

                States: on (active), off (available but inactive), disabled (completely off)

                Examples:
                  filter:mod mathjaxloader --state on --run
                  filter:mod multilang --state disabled --run
                  filter:mod mathjaxloader --move up --run
                  filter:mod mathjaxloader --apply-to-strings 1 --run
                  filter:mod mathjaxloader --state on --context 12 --run
                  filter:mod mathjaxloader --config "key=value" --context 12 --run
                HELP);
        $this->handler->configureCommand($this);
    }

    protected function getActiveHandler(): BaseHandler { return $this->handler; }

    protected function handle(InputInterface $input, OutputInterface $output): int
    {
        return $this->handler->handle($input, $output);
    }

    private function resolveHandler(?MoodleVersion $moodleVersion): BaseHandler
    {
        return new FilterMod51Handler();
    }
}
