<?php
namespace Moosh2\Command\Session;

use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SessionKill52Handler extends BaseHandler
{
    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $runMode = $input->getOption('run');

        if (!$runMode) {
            $output->writeln('<info>Dry run — would destroy all user sessions (use --run to execute).</info>');
            return Command::SUCCESS;
        }

        \core\session\manager::kill_all_sessions();
        $output->writeln('All user sessions destroyed.');
        return Command::SUCCESS;
    }
}
