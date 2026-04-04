<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Log;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * log:export implementation for Moodle 5.1.
 */
class LogExport52Handler extends BaseHandler
{
    private const ORIGIN_MAP = [
        'web' => 1,
        'cli' => 2,
        'ws' => 3,
        'cron' => 4,
        'restore' => 5,
        '' => 6,
    ];

    private const ACTION_MAP = [
        'abandoned' => 1, 'accepted' => 2, 'added' => 3, 'answered' => 4,
        'approved' => 5, 'archived' => 6, 'assessed' => 7, 'assigned' => 8,
        'autosaved' => 9, 'awarded' => 10, 'blocked' => 11, 'called' => 12,
        'cancelled' => 13, 'completed' => 14, 'created' => 15, 'deleted' => 16,
        'detected' => 17, 'disabled' => 18, 'disapproved' => 19, 'downloaded' => 20,
        'duplicated' => 21, 'edited' => 22, 'enabled' => 23, 'ended' => 24,
        'evaluated' => 25, 'exported' => 26, 'failed' => 27, 'flagged' => 28,
        'graded' => 29, 'granted' => 30, 'imported' => 31, 'indexed' => 32,
        'joined' => 33, 'launched' => 34, 'left' => 35, 'listed' => 36,
        'loaded' => 37, 'locked' => 38, 'logged' => 39, 'loggedin' => 40,
        'loggedinas' => 41, 'loggedout' => 42, 'met' => 43, 'moved' => 44,
        'pinned' => 45, 'prevented' => 46, 'previewed' => 47, 'printed' => 48,
        'protected' => 49, 'published' => 50, 'rated' => 51, 'reassessed' => 52,
        'received' => 53, 'reevaluated' => 54, 'regraded' => 55, 'removed' => 56,
        'reopened' => 57, 'reordered' => 58, 'repaginated' => 59, 'replaced' => 60,
        'requested' => 61, 'reset' => 62, 'restarted' => 63, 'restored' => 64,
        'resumed' => 65, 'revealed' => 66, 'reverted' => 67, 'reviewed' => 68,
        'revoked' => 69, 'searched' => 70, 'sent' => 71, 'shown' => 72,
        'started' => 73, 'stopped' => 74, 'submitted' => 75, 'switched' => 76,
        'unapproved' => 77, 'unassigned' => 78, 'unblocked' => 79, 'unflagged' => 80,
        'unlinked' => 81, 'unlocked' => 82, 'unpinned' => 83, 'unprotected' => 84,
        'unpublished' => 85, 'updated' => 86, 'uploaded' => 87, 'viewed' => 88,
        'becameoverdue' => 89, 'remind' => 90, 'error' => 91, 'give' => 92,
    ];

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the output CSV file')
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'From date (YYYY-MM-DD or YYYY-MM-DD HH:MM:SS)')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'To date (YYYY-MM-DD or YYYY-MM-DD HH:MM:SS)')
            ->addOption('compact', null, InputOption::VALUE_NONE, 'Omit id column if IDs are consecutive; write metadata.json with first_id');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);

        $file = $input->getArgument('file');
        $fromOpt = $input->getOption('from');
        $toOpt = $input->getOption('to');

        if ($fromOpt === null || $toOpt === null) {
            $output->writeln('<error>Both --from and --to options are required.</error>');
            return Command::FAILURE;
        }

        $fromDate = $this->parseDate($fromOpt, true);
        $toDate = $this->parseDate($toOpt, false);

        if ($fromDate === null) {
            $output->writeln('<error>Invalid --from date format. Use YYYY-MM-DD or YYYY-MM-DD HH:MM:SS.</error>');
            return Command::FAILURE;
        }

        if ($toDate === null) {
            $output->writeln('<error>Invalid --to date format. Use YYYY-MM-DD or YYYY-MM-DD HH:MM:SS.</error>');
            return Command::FAILURE;
        }

        $tsFrom = $fromDate->getTimestamp();
        $tsTo = $toDate->getTimestamp();

        if ($tsTo < $tsFrom) {
            $output->writeln('<error>"to" date must be later than "from" date.</error>');
            return Command::FAILURE;
        }

        $verbose->detail('From', $fromDate->format('Y-m-d H:i:s') . " (timestamp: $tsFrom)");
        $verbose->detail('To', $toDate->format('Y-m-d H:i:s') . " (timestamp: $tsTo)");
        $verbose->detail('Output file', $file);

        $compact = $input->getOption('compact');

        $sql = "SELECT *
                FROM {logstore_standard_log}
                WHERE timecreated >= ? AND timecreated <= ?
                ORDER BY id ASC";
        $params = [$tsFrom, $tsTo];

        // With --compact, check if IDs are consecutive using MIN/MAX/COUNT.
        $compactApplied = false;
        $firstId = null;
        if ($compact) {
            $verbose->step('Checking if IDs are consecutive');
            $stats = $DB->get_record_sql(
                "SELECT MIN(id) AS minid, MAX(id) AS maxid, COUNT(id) AS cnt
                   FROM {logstore_standard_log}
                  WHERE timecreated >= ? AND timecreated <= ?",
                $params,
            );

            if ((int) $stats->cnt === 0) {
                $output->writeln('No log entries found in the specified date range.');
                return Command::SUCCESS;
            }

            $firstId = (int) $stats->minid;
            $consecutive = ((int) $stats->maxid - $firstId + 1) === (int) $stats->cnt;

            if ($consecutive) {
                $compactApplied = true;
                $verbose->done('IDs are consecutive — compact mode enabled');
            } else {
                $verbose->warn('IDs are not consecutive — falling back to normal export');
            }
        }

        // Analyze origin values for compact mode.
        $defaultOrigin = null;
        if ($compactApplied) {
            $verbose->step('Analyzing origin values');
            $originCounts = $DB->get_records_sql(
                "SELECT COALESCE(origin, '') AS origin, COUNT(*) AS cnt
                   FROM {logstore_standard_log}
                  WHERE timecreated >= ? AND timecreated <= ?
                  GROUP BY origin
                  ORDER BY cnt DESC",
                $params,
            );
            $topOrigin = reset($originCounts);
            if ($topOrigin !== false) {
                $defaultOrigin = $topOrigin->origin;
                $verbose->done('Default origin: "' . $defaultOrigin . '" (' . $topOrigin->cnt . ' entries)');
            }
        }

        // Load event map for compact mode.
        $eventMap = null;
        $eventNameIndex = null;
        if ($compactApplied) {
            $eventMapPath = dirname(__DIR__, 2) . '/Data/event_map.php';
            if (file_exists($eventMapPath)) {
                $eventMap = require $eventMapPath;
                $verbose->detail('Event map loaded', count($eventMap) . ' entries');
            } else {
                $verbose->warn('Event map not found — event names will not be compressed');
            }
        }

        // Second pass (or only pass without --compact): stream records to CSV.
        $verbose->step($compactApplied ? 'Writing compact CSV file (without id column)' : 'Writing CSV file');

        $fp = fopen($file, 'w');
        if ($fp === false) {
            $output->writeln('<error>Could not open file for writing: ' . $file . '</error>');
            return Command::FAILURE;
        }

        $rs = $DB->get_recordset_sql($sql, $params);
        $headersWritten = false;
        $count = 0;

        foreach ($rs as $record) {
            if (!$headersWritten) {
                $headers = array_keys((array) $record);
                if ($compactApplied) {
                    array_shift($headers);
                }
                // Build header index for O(1) column lookup.
                if ($compactApplied) {
                    $headerIndex = array_flip($headers);
                    if ($eventMap !== null) {
                        $eventNameIndex = $headerIndex['eventname'] ?? false;
                    }
                    $originIndex = $headerIndex['origin'] ?? false;
                    $actionIndex = $headerIndex['action'] ?? false;
                    $timecreatedIndex = $headerIndex['timecreated'] ?? false;
                }
                fputcsv($fp, $headers);
                $headersWritten = true;
            }

            $row = array_values((array) $record);
            if ($compactApplied) {
                array_shift($row);
            }

            // Replace eventname with numeric ID if mapped.
            if ($eventNameIndex !== false && $eventNameIndex !== null && isset($row[$eventNameIndex])) {
                $eventName = $row[$eventNameIndex];
                if (isset($eventMap[$eventName])) {
                    $row[$eventNameIndex] = $eventMap[$eventName];
                }
            }

            // Replace origin: default → empty, others → numeric ID.
            if ($compactApplied && $originIndex !== false && $defaultOrigin !== null) {
                $originValue = $row[$originIndex] ?? '';
                if ($originValue === $defaultOrigin) {
                    $row[$originIndex] = '';
                } else {
                    $row[$originIndex] = self::ORIGIN_MAP[$originValue] ?? $originValue;
                }
            }

            // Replace action with numeric ID.
            if ($compactApplied && $actionIndex !== false) {
                $actionValue = $row[$actionIndex] ?? '';
                $row[$actionIndex] = self::ACTION_MAP[$actionValue] ?? $actionValue;
            }

            // Replace timecreated with delta from previous record.
            if ($compactApplied && $timecreatedIndex !== false) {
                $currentTime = (int) $row[$timecreatedIndex];
                if (!isset($prevTimecreated)) {
                    $prevTimecreated = $currentTime;
                } else {
                    $row[$timecreatedIndex] = $currentTime - $prevTimecreated;
                    $prevTimecreated = $currentTime;
                }
            }

            fputcsv($fp, $row);
            $count++;
        }
        $rs->close();
        fclose($fp);

        if ($count === 0) {
            unlink($file);
            $output->writeln('No log entries found in the specified date range.');
            return Command::SUCCESS;
        }

        if ($compactApplied) {
            $metadataPath = dirname($file) . '/metadata.json';
            $metadata = ['first_id' => $firstId];
            if ($eventMap !== null) {
                $metadata['event_map'] = true;
            }
            if ($defaultOrigin !== null) {
                $metadata['default_origin'] = $defaultOrigin;
                $metadata['origin_map'] = self::ORIGIN_MAP;
            }
            $metadata['action_map'] = self::ACTION_MAP;
            $metadata['timecreated_delta'] = true;
            file_put_contents($metadataPath, json_encode($metadata, JSON_PRETTY_PRINT) . "\n");
            $verbose->done('Wrote metadata.json with first_id=' . $firstId);
        }

        $verbose->done('Wrote ' . $count . ' entries to ' . $file);
        $output->writeln('Exported ' . $count . ' log entries to ' . $file);

        return Command::SUCCESS;
    }

    private function parseDate(string $value, bool $isFrom): ?\DateTime
    {
        // Try full datetime first.
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
        if ($date !== false) {
            return $date;
        }

        // Try date-only.
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        if ($date !== false) {
            if ($isFrom) {
                $date->setTime(0, 0, 0);
            } else {
                $date->setTime(23, 59, 59);
            }
            return $date;
        }

        return null;
    }
}
