<?php
namespace Moosh2\Command\Maintenance;

use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MaintenanceOn52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addOption('message', 'm', InputOption::VALUE_REQUIRED, 'Maintenance message');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $message = $input->getOption('message');
        if ($message !== null) {
            set_config('maintenance_message', $message);
        }
        set_config('maintenance_enabled', 1);
        $output->writeln('Maintenance mode enabled.');
        return Command::SUCCESS;
    }
}
