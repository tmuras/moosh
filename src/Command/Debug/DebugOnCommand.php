<?php
namespace Moosh2\Command\Debug;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DebugOnCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::Full;
    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->handler = new DebugOn51Handler();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('debug:on')
            ->setDescription('Enable developer debug mode')
            ->setHelp('Enables full developer debugging: debug display, SMTP debug, performance debug, string IDs, theme designer mode, disables JS cache.');
    }

    protected function getActiveHandler(): BaseHandler { return $this->handler; }
    protected function handle(InputInterface $input, OutputInterface $output): int { return $this->handler->handle($input, $output); }
}
