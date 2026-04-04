<?php
namespace Moosh2\Command\System;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SystemCheck51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addOption('status', null, InputOption::VALUE_REQUIRED, 'Filter by status: ok, info, warning, critical, error, unknown');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $filterStatus = $input->getOption('status');

        $verbose->step('Running system checks');

        $checks = \core\check\manager::get_checks('status');
        $headers = ['status', 'component', 'check', 'info'];
        $rows = [];
        $statusCounts = ['ok' => 0, 'info' => 0, 'warning' => 0, 'critical' => 0, 'error' => 0, 'unknown' => 0];

        foreach ($checks as $check) {
            $result = $check->get_result();
            $status = $result->get_status();

            // Map status constant to string.
            $statusMap = [
                \core\check\result::OK => 'ok',
                \core\check\result::INFO => 'info',
                \core\check\result::WARNING => 'warning',
                \core\check\result::CRITICAL => 'critical',
                \core\check\result::ERROR => 'error',
                \core\check\result::UNKNOWN => 'unknown',
                \core\check\result::NA => 'ok',
            ];
            $statusName = $statusMap[$status] ?? 'unknown';
            $statusCounts[$statusName] = ($statusCounts[$statusName] ?? 0) + 1;

            if ($filterStatus !== null && $statusName !== $filterStatus) {
                continue;
            }

            $rows[] = [
                $statusName,
                $check->get_component(),
                $check->get_name(),
                strip_tags($result->get_summary()),
            ];
        }

        if (empty($rows) && $filterStatus !== null) {
            $output->writeln("No checks with status '$filterStatus'.");
            return Command::SUCCESS;
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        $output->writeln('');
        $output->writeln("Summary: {$statusCounts['ok']} ok, {$statusCounts['warning']} warning, {$statusCounts['critical']} critical, {$statusCounts['error']} error");

        return Command::SUCCESS;
    }
}
