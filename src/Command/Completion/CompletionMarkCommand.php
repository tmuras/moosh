<?php
namespace Moosh2\Command\Completion;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompletionMarkCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::Full;
    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->handler = new CompletionMark52Handler();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('completion:mark')
            ->setDescription('Mark course or activity as complete for a user')
            ->setHelp("Marks a course or activity as complete/incomplete for a user.\n\nExamples:\n  completion:mark 2 --userid 3 --run\n  completion:mark 2 --userid 3 --cmid 42 --run\n  completion:mark 2 --userid 3 --cmid 42 --state incomplete --run");
        $this->handler->configureCommand($this);
    }

    protected function getActiveHandler(): BaseHandler { return $this->handler; }
    protected function handle(InputInterface $input, OutputInterface $output): int { return $this->handler->handle($input, $output); }
}
