<?php
namespace Moosh2\Command\RecycleBin;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecycleBinRestoreCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::Full;
    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->handler = new RecycleBinRestore51Handler();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('recyclebin:restore')
            ->setDescription('Restore an item from the recycle bin')
            ->setHelp("Restores a deleted activity from the course recycle bin.\n\nExamples:\n  recyclebin:restore 5 2 --run");
        $this->handler->configureCommand($this);
    }

    protected function getActiveHandler(): BaseHandler { return $this->handler; }
    protected function handle(InputInterface $input, OutputInterface $output): int { return $this->handler->handle($input, $output); }
}
