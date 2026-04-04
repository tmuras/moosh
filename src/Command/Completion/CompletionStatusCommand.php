<?php
namespace Moosh2\Command\Completion;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompletionStatusCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::Full;
    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->handler = new CompletionStatus52Handler();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('completion:status')
            ->setDescription('Show course completion status for a user')
            ->setHelp("Shows completion progress, activity states, and criteria status.\n\nExamples:\n  completion:status 2 --userid 3\n  completion:status 2 --userid 3 -o csv\n  completion:status 2 --all");
        $this->handler->configureCommand($this);
    }

    protected function getActiveHandler(): BaseHandler { return $this->handler; }
    protected function handle(InputInterface $input, OutputInterface $output): int { return $this->handler->handle($input, $output); }
}
