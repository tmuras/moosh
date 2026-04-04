<?php
namespace Moosh2\Command\Content;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ContentHttpsReplaceCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::Full;
    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->handler = new ContentHttpsReplace52Handler();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('content:https-replace')
            ->setDescription('Replace HTTP URLs with HTTPS across the database')
            ->setHelp("Migrates embedded HTTP URLs to HTTPS. Use --list to see affected domains first.\n\nExamples:\n  content:https-replace --list\n  content:https-replace --run");
        $this->handler->configureCommand($this);
    }

    protected function getActiveHandler(): BaseHandler { return $this->handler; }
    protected function handle(InputInterface $input, OutputInterface $output): int { return $this->handler->handle($input, $output); }
}
