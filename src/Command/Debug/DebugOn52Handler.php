<?php
namespace Moosh2\Command\Debug;

use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DebugOn52Handler extends BaseHandler
{
    public function handle(InputInterface $input, OutputInterface $output): int
    {
        set_config('debug', E_ALL | E_STRICT);
        set_config('debugdisplay', 1);
        set_config('debugsmtp', 1);
        set_config('perfdebug', 15);
        set_config('debugstringids', 1);
        set_config('debugpageinfo', 1);
        set_config('themedesignermode', 1);
        set_config('cachejs', 0);
        $output->writeln('Developer debug mode enabled.');
        return Command::SUCCESS;
    }
}
