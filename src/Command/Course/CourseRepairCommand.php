<?php
namespace Moosh2\Command\Course;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CourseRepairCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::Full;
    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->handler = new CourseRepair51Handler();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('course:repair')
            ->setDescription('Check and repair course module sequence integrity')
            ->setHelp("Detects and fixes broken course module sequences (duplicates, orphans, missing references).\n\nExamples:\n  course:repair 2\n  course:repair 2 --run\n  course:repair --all\n  course:repair --all --run");
        $this->handler->configureCommand($this);
    }

    protected function getActiveHandler(): BaseHandler { return $this->handler; }
    protected function handle(InputInterface $input, OutputInterface $output): int { return $this->handler->handle($input, $output); }
}
