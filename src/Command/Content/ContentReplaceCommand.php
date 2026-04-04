<?php
namespace Moosh2\Command\Content;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ContentReplaceCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::Full;
    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->handler = new ContentReplace52Handler();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('content:replace')
            ->setDescription('Find and replace text across the entire database')
            ->setHelp("Performs database-wide text replacement. Useful for domain migrations.\n\nExamples:\n  content:replace 'http://old.example.com' 'https://new.example.com' --run\n  content:replace 'old-domain.com' 'new-domain.com' --skip-tables=mdl_config,mdl_config_log --run");
        $this->handler->configureCommand($this);
    }

    protected function getActiveHandler(): BaseHandler { return $this->handler; }
    protected function handle(InputInterface $input, OutputInterface $output): int { return $this->handler->handle($input, $output); }
}
