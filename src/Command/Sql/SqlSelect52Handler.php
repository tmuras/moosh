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
 * sql:select implementation for Moodle 5.1.
 */
class SqlSelect52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('query', InputArgument::REQUIRED, 'SQL SELECT query (use {tablename} for Moodle tables)')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Maximum number of rows to return', '0');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $query = trim($input->getArgument('query'));
        $limit = (int) $input->getOption('limit');

        // Basic safety check — only allow SELECT.
        $upperQuery = strtoupper(ltrim($query));
        if (!str_starts_with($upperQuery, 'SELECT')) {
            $output->writeln('<error>Only SELECT queries are allowed.</error>');
            return Command::FAILURE;
        }

        $verbose->step('Executing query');
        $verbose->info('SQL: ' . $query);

        try {
            $records = $DB->get_records_sql($query, null, 0, $limit ?: 0);
        } catch (\Throwable $e) {
            $output->writeln('<error>Query failed: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $verbose->done('Query returned ' . count($records) . ' row(s)');

        if (empty($records)) {
            // Output empty result with no headers.
            $formatter = new ResultFormatter($output, $format);
            $formatter->display([], []);
            return Command::SUCCESS;
        }

        // Build headers from first record's keys.
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
}
