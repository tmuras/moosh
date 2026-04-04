<?php
namespace Moosh2\Command\Debug;

use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DebugOff51Handler extends BaseHandler
{
    public function handle(InputInterface $input, OutputInterface $output): int
    {
        set_config('debug', 0);
        set_config('debugdisplay', 0);
        set_config('debugsmtp', 0);
        set_config('perfdebug', 7);
        set_config('debugstringids', 0);
        set_config('debugpageinfo', 0);
        set_config('themedesignermode', 0);
        set_config('cachejs', 1);
        $output->writeln('Developer debug mode disabled.');
        return Command::SUCCESS;
    }
}
