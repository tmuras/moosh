<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\File;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FileCheck51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('missing', null, InputOption::VALUE_NONE, 'Find DB records with missing files on disk')
            ->addOption('orphaned', null, InputOption::VALUE_NONE, 'Find files on disk not in database')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Limit results', '100');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');

        $checkMissing = $input->getOption('missing');
        $checkOrphaned = $input->getOption('orphaned');
        $limit = (int) $input->getOption('limit');

        // Default: check missing.
        if (!$checkMissing && !$checkOrphaned) {
            $checkMissing = true;
        }

        if ($checkMissing) {
            $result = $this->checkMissing($limit, $format, $output, $verbose);
            if ($result !== Command::SUCCESS) {
                return $result;
            }
        }

        if ($checkOrphaned) {
            if ($checkMissing) {
                $output->writeln('');
            }
            return $this->checkOrphaned($limit, $format, $output, $verbose);
        }

        return Command::SUCCESS;
    }

    private function checkMissing(int $limit, string $format, OutputInterface $output, VerboseLogger $verbose): int
    {
        global $CFG, $DB;

        $verbose->step('Checking for files missing from disk');

        $sql = "SELECT DISTINCT contenthash, filesize
                  FROM {files}
                 WHERE filename != '.'
                   AND filesize > 0
                 ORDER BY contenthash";
        $hashes = $DB->get_records_sql($sql);

        $missing = [];
        $checked = 0;

        foreach ($hashes as $record) {
            $hash = $record->contenthash;
            $l1 = substr($hash, 0, 2);
            $l2 = substr($hash, 2, 2);
            $path = $CFG->dataroot . "/filedir/$l1/$l2/$hash";

            if (!file_exists($path)) {
                $missing[] = $hash;
                if (count($missing) >= $limit) {
                    break;
                }
            }
            $checked++;
        }

        $output->writeln("<info>=== Missing Files (DB records without files on disk) ===</info>");
        $output->writeln("Checked $checked unique content hashes.");

        if (empty($missing)) {
            $output->writeln('No missing files found.');
            return Command::SUCCESS;
        }

        $output->writeln('Found ' . count($missing) . ' missing content hash(es):');

        $headers = ['contenthash', 'files_count', 'components'];
        $rows = [];
        foreach ($missing as $hash) {
            $fileCount = $DB->count_records_select('files', "contenthash = ? AND filename != '.'", [$hash]);
            $components = $DB->get_fieldset_select('files', 'DISTINCT component', "contenthash = ? AND filename != '.'", [$hash]);
            $rows[] = [$hash, $fileCount, implode(', ', $components)];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }

    private function checkOrphaned(int $limit, string $format, OutputInterface $output, VerboseLogger $verbose): int
    {
        global $CFG, $DB;

        $verbose->step('Checking for orphaned files on disk');

        $filedir = $CFG->dataroot . '/filedir';
        if (!is_dir($filedir)) {
            $output->writeln('<error>Filedir not found.</error>');
            return Command::FAILURE;
        }

        $orphaned = [];
        $checked = 0;

        // Scan the two-level directory structure.
        $l1dirs = scandir($filedir);
        foreach ($l1dirs as $l1) {
            if ($l1 === '.' || $l1 === '..' || !is_dir("$filedir/$l1")) {
                continue;
            }
            $l2dirs = scandir("$filedir/$l1");
            foreach ($l2dirs as $l2) {
                if ($l2 === '.' || $l2 === '..' || !is_dir("$filedir/$l1/$l2")) {
                    continue;
                }
                $files = scandir("$filedir/$l1/$l2");
                foreach ($files as $file) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }
                    $checked++;
                    if (!$DB->record_exists('files', ['contenthash' => $file])) {
                        $orphaned[] = "$l1/$l2/$file";
                        if (count($orphaned) >= $limit) {
                            break 3;
                        }
                    }
                }
            }
        }

        $output->writeln("<info>=== Orphaned Files (on disk but not in database) ===</info>");
        $output->writeln("Checked $checked files on disk.");

        if (empty($orphaned)) {
            $output->writeln('No orphaned files found.');
            return Command::SUCCESS;
        }

        $output->writeln('Found ' . count($orphaned) . ' orphaned file(s).');
        foreach ($orphaned as $path) {
            $output->writeln("  $filedir/$path");
        }

        return Command::SUCCESS;
    }
}
