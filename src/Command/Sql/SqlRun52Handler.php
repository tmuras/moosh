<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Sql;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * sql:run implementation for Moodle 5.1.
 */
class SqlRun52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('sql', InputArgument::REQUIRED, 'SQL query (use {tablename} for Moodle tables)')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Maximum number of rows for SELECT queries', '0');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $runMode = $input->getOption('run');
        $query = trim($input->getArgument('sql'));
        $limit = (int) $input->getOption('limit');

        $isSelect = str_starts_with(strtoupper(ltrim($query)), 'SELECT');

        if ($isSelect) {
            return $this->executeSelect($query, $limit, $format, $verbose, $output);
        }

        return $this->executeWrite($query, $runMode, $verbose, $output);
    }

    private function executeSelect(
        string $query,
        int $limit,
        string $format,
        VerboseLogger $verbose,
        OutputInterface $output,
    ): int {
        global $DB;

        $verbose->step('Executing SELECT query');
        $verbose->info('SQL: ' . $query);

        try {
            $records = $DB->get_records_sql($query, null, 0, $limit ?: 0);
        } catch (\Throwable $e) {
            $output->writeln('<error>Query failed: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $verbose->done('Query returned ' . count($records) . ' row(s)');

        if (empty($records)) {
            $formatter = new ResultFormatter($output, $format);
            $formatter->display([], []);
            return Command::SUCCESS;
        }

        $first = reset($records);
        $headers = array_keys((array) $first);

        $rows = [];
        foreach ($records as $record) {
            $rows[] = array_values((array) $record);
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }

    private function executeWrite(
        string $query,
        bool $runMode,
        VerboseLogger $verbose,
        OutputInterface $output,
    ): int {
        global $DB;

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following query would be executed (use --run to execute):</info>');
            $output->writeln("  $query");
            return Command::SUCCESS;
        }

        $verbose->step('Executing write query');
        $verbose->info('SQL: ' . $query);

        try {
            $result = $DB->execute($query);
        } catch (\Throwable $e) {
            $output->writeln('<error>Query failed: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $verbose->done('Query executed');
        $output->writeln('Query executed successfully.');

        return Command::SUCCESS;
    }
}
