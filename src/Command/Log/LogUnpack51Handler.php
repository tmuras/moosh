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
use Symfony\Component\Console\Output\OutputInterface;

/**
 * log:unpack implementation for Moodle 5.1.
 */
class LogUnpack51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the compact CSV file (without id column)')
            ->addArgument('output', InputArgument::REQUIRED, 'Path to the output CSV file (with restored IDs)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $verbose = new VerboseLogger($output);

        $file = $input->getArgument('file');
        $outputFile = $input->getArgument('output');

        if (!file_exists($file)) {
            $output->writeln('<error>Input CSV file not found: ' . $file . '</error>');
            return Command::FAILURE;
        }

        $metadataPath = dirname($file) . '/metadata.json';
        if (!file_exists($metadataPath)) {
            $output->writeln('<error>metadata.json not found in ' . dirname($file) . '</error>');
            return Command::FAILURE;
        }

        $verbose->step('Reading metadata.json');
        $metadataJson = file_get_contents($metadataPath);
        $metadata = json_decode($metadataJson, true);

        if (!is_array($metadata) || !isset($metadata['first_id'])) {
            $output->writeln('<error>Invalid metadata.json: missing first_id.</error>');
            return Command::FAILURE;
        }

        $firstId = (int) $metadata['first_id'];
        $verbose->detail('First ID', (string) $firstId);

        // Load origin restore info.
        $defaultOrigin = $metadata['default_origin'] ?? null;
        $reverseOriginMap = null;
        if ($defaultOrigin !== null && isset($metadata['origin_map'])) {
            $reverseOriginMap = array_flip($metadata['origin_map']);
            $verbose->detail('Default origin', $defaultOrigin);
        }

        // Load reverse event map if compact export used event compression.
        $reverseEventMap = null;
        if (!empty($metadata['event_map'])) {
            $eventMapPath = dirname(__DIR__, 2) . '/Data/event_map.php';
            if (file_exists($eventMapPath)) {
                $eventMap = require $eventMapPath;
                $reverseEventMap = array_flip($eventMap);
                $verbose->detail('Event map loaded', count($reverseEventMap) . ' entries');
            } else {
                $verbose->warn('Event map not found — event IDs will not be restored');
            }
        }

        $verbose->step('Reading compact CSV');
        $fhIn = fopen($file, 'r');
        if ($fhIn === false) {
            $output->writeln('<error>Could not open input file: ' . $file . '</error>');
            return Command::FAILURE;
        }

        $headers = fgetcsv($fhIn);
        if ($headers === false) {
            fclose($fhIn);
            $output->writeln('<error>Could not read CSV headers from ' . $file . '</error>');
            return Command::FAILURE;
        }

        $verbose->step('Writing restored CSV');
        $fhOut = fopen($outputFile, 'w');
        if ($fhOut === false) {
            fclose($fhIn);
            $output->writeln('<error>Could not open output file for writing: ' . $outputFile . '</error>');
            return Command::FAILURE;
        }

        // Write header with id prepended.
        fputcsv($fhOut, array_merge(['id'], $headers));

        // Build header index for O(1) column lookup.
        $headerIndex = array_flip($headers);

        $eventNameIndex = false;
        if ($reverseEventMap !== null) {
            $eventNameIndex = $headerIndex['eventname'] ?? false;
        }

        $originIndex = false;
        if ($defaultOrigin !== null) {
            $originIndex = $headerIndex['origin'] ?? false;
        }

        $reverseActionMap = null;
        if (isset($metadata['action_map'])) {
            $reverseActionMap = array_flip($metadata['action_map']);
            $verbose->detail('Action map loaded', count($reverseActionMap) . ' entries');
        }
        $actionIndex = $reverseActionMap !== null
            ? ($headerIndex['action'] ?? false)
            : false;

        $timecreatedDelta = !empty($metadata['timecreated_delta']);
        $timecreatedIndex = $timecreatedDelta
            ? ($headerIndex['timecreated'] ?? false)
            : false;
        $prevTimecreated = 0;

        $rowCount = 0;
        $currentId = $firstId;
        while (($row = fgetcsv($fhIn)) !== false) {
            // Restore eventname from numeric ID.
            if ($eventNameIndex !== false && isset($row[$eventNameIndex])) {
                $eventId = (int) $row[$eventNameIndex];
                if (isset($reverseEventMap[$eventId])) {
                    $row[$eventNameIndex] = $reverseEventMap[$eventId];
                }
            }

            // Restore origin: empty → default, numeric ID → string.
            if ($originIndex !== false) {
                $originValue = $row[$originIndex] ?? '';
                if ($originValue === '') {
                    $row[$originIndex] = $defaultOrigin;
                } else {
                    $numericOrigin = (int) $originValue;
                    if (isset($reverseOriginMap[$numericOrigin])) {
                        $row[$originIndex] = $reverseOriginMap[$numericOrigin];
                    }
                }
            }

            // Restore action from numeric ID.
            if ($actionIndex !== false && isset($row[$actionIndex])) {
                $actionId = (int) $row[$actionIndex];
                if (isset($reverseActionMap[$actionId])) {
                    $row[$actionIndex] = $reverseActionMap[$actionId];
                }
            }

            // Restore timecreated from delta.
            if ($timecreatedIndex !== false) {
                $delta = (int) $row[$timecreatedIndex];
                if ($rowCount === 0) {
                    // First row: value is the full timestamp.
                    $prevTimecreated = $delta;
                } else {
                    $prevTimecreated += $delta;
                    $row[$timecreatedIndex] = $prevTimecreated;
                }
            }

            fputcsv($fhOut, array_merge([$currentId], $row));
            $currentId++;
            $rowCount++;
        }

        fclose($fhIn);
        fclose($fhOut);

        $verbose->done('Restored ' . $rowCount . ' entries with IDs starting from ' . $firstId);
        $output->writeln('Unpacked ' . $rowCount . ' log entries to ' . $outputFile);

        return Command::SUCCESS;
    }
}
