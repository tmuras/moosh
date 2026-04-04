<?php
namespace Moosh2\Command\RecycleBin;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecycleBinListCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::Full;
    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->handler = new RecycleBinList51Handler();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('recyclebin:list')
            ->setDescription('List items in the recycle bin')
            ->setHelp("Lists deleted items in a course's recycle bin.\n\nExamples:\n  recyclebin:list 2\n  recyclebin:list 2 -o csv");
        $this->handler->configureCommand($this);
    }

    protected function getActiveHandler(): BaseHandler { return $this->handler; }
    protected function handle(InputInterface $input, OutputInterface $output): int { return $this->handler->handle($input, $output); }
}
