<?php
namespace Moosh2\Command\Completion;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompletionResetCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::Full;
    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->handler = new CompletionReset51Handler();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('completion:reset')
            ->setDescription('Reset completion data for a course')
            ->setHelp("Resets all completion data for a course or specific activity.\n\nExamples:\n  completion:reset 2 --run\n  completion:reset 2 --cmid 42 --run");
        $this->handler->configureCommand($this);
    }

    protected function getActiveHandler(): BaseHandler { return $this->handler; }
    protected function handle(InputInterface $input, OutputInterface $output): int { return $this->handler->handle($input, $output); }
}
