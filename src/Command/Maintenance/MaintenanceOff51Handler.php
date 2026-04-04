<?php
namespace Moosh2\Command\Maintenance;

use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MaintenanceOff51Handler extends BaseHandler
{
    public function handle(InputInterface $input, OutputInterface $output): int
    {
        set_config('maintenance_message', '');
        set_config('maintenance_enabled', 0);
        $output->writeln('Maintenance mode disabled.');
        return Command::SUCCESS;
    }
}
