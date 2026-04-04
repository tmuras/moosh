<?php
namespace Moosh2\Command\Dashboard;

use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DashboardReset51Handler extends BaseHandler
{
    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $runMode = $input->getOption('run');

        if (!$runMode) {
            $output->writeln('<info>Dry run — would reset all user dashboards (use --run to execute).</info>');
            return Command::SUCCESS;
        }

        require_once $CFG->dirroot . '/my/lib.php';
        my_reset_page_for_all_users(MY_PAGE_PRIVATE, 'my-index');
        $output->writeln('All user dashboards reset to default.');
        return Command::SUCCESS;
    }
}
