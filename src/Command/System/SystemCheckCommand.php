<?php
namespace Moosh2\Command\System;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SystemCheckCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::Full;
    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->handler = new SystemCheck51Handler();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('system:check')
            ->setDescription('Run system health and security checks')
            ->setHelp("Runs Moodle's built-in health and security checks.\n\nExamples:\n  system:check\n  system:check --status warning\n  system:check -o csv");
        $this->handler->configureCommand($this);
    }

    protected function getActiveHandler(): BaseHandler { return $this->handler; }
    protected function handle(InputInterface $input, OutputInterface $output): int { return $this->handler->handle($input, $output); }
}
