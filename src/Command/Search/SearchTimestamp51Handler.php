<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Search;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * search:timestamp implementation for Moodle 5.1.
 */
class SearchTimestamp51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('timestamp', InputArgument::OPTIONAL, 'Exact Unix timestamp to search for')
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Range start (Unix timestamp)')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Range end (Unix timestamp)')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Max results per table', '100');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $timestamp = $input->getArgument('timestamp');
        $from = $input->getOption('from');
        $to = $input->getOption('to');
        $limit = (int) $input->getOption('limit');

        // Validate input.
        if ($timestamp === null && $from === null && $to === null) {
            $output->writeln('<error>Provide either a timestamp argument or --from and --to options.</error>');
            return Command::FAILURE;
        }
        if ($timestamp !== null && ($from !== null || $to !== null)) {
            $output->writeln('<error>Cannot use both timestamp argument and --from/--to options.</error>');
            return Command::FAILURE;
        }
        if (($from !== null) !== ($to !== null)) {
            $output->writeln('<error>Both --from and --to must be provided together.</error>');
            return Command::FAILURE;
        }

        $isRange = $from !== null;
        $tsFrom = $isRange ? (int) $from : (int) $timestamp;
        $tsTo = $isRange ? (int) $to : (int) $timestamp;

        $verbose->step('Scanning database schema for timestamp columns');

        // Find all integer columns containing 'time' in their name.
        $manager = $DB->get_manager();
        $schema = $manager->get_install_xml_schema();
        $tables = $schema->getTables();

        $candidates = [];
        foreach ($tables as $table) {
            $tableName = $table->getName();
            $timeCols = [];

            foreach ($table->getFields() as $column) {
                if ($column->getType() !== XMLDB_TYPE_INTEGER) {
                    continue;
                }
                if (stripos($column->getName(), 'time') === false) {
                    continue;
                }
                $timeCols[] = $column->getName();
            }

            if (!empty($timeCols) && $table->getField('id')) {
                $candidates[$tableName] = $timeCols;
            }
        }

        $verbose->done('Found ' . count($candidates) . ' table(s) with timestamp columns');

        // Search each table.
        $verbose->step('Searching for matches');

        $headers = ['table', 'id', 'column', 'value', 'datetime'];
        $rows = [];
        $totalMatches = 0;

        foreach ($candidates as $tableName => $columns) {
            $whereParts = [];
            $params = [];

            foreach ($columns as $col) {
                if ($isRange) {
                    $whereParts[] = "($col >= ? AND $col <= ?)";
                    $params[] = $tsFrom;
                    $params[] = $tsTo;
                } else {
                    $whereParts[] = "$col = ?";
                    $params[] = $tsFrom;
                }
            }

            $selectCols = 'id, ' . implode(', ', $columns);
            $sql = "SELECT $selectCols FROM {{$tableName}} WHERE " . implode(' OR ', $whereParts);

            try {
                $rs = $DB->get_recordset_sql($sql, $params, 0, $limit);
            } catch (\Throwable $e) {
                continue;
            }

            $tableMatches = 0;
            foreach ($rs as $record) {
                foreach ($columns as $col) {
                    $val = (int) $record->$col;
                    if ($isRange) {
                        $match = $val >= $tsFrom && $val <= $tsTo;
                    } else {
                        $match = $val === $tsFrom;
                    }

                    if ($match && $val > 0) {
                        $rows[] = [
                            $tableName,
                            $record->id,
                            $col,
                            $val,
                            date('Y-m-d H:i:s', $val),
                        ];
                        $tableMatches++;
                        $totalMatches++;
                    }
                }
            }
            $rs->close();

            if ($tableMatches > 0) {
                $verbose->info("$tableName: $tableMatches match(es)");
            }
        }

        $verbose->done("Found $totalMatches match(es) total");

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
