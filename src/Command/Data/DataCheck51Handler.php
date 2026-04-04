<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Data;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * data:check implementation for Moodle 5.1.
 *
 * Combines four moosh1 commands into one:
 * - file-datacheck: checksum verification
 * - chkdatadir: writable check
 * - file-check: DB entries with missing files on disk
 * - file-dbcheck: files on disk without DB entries
 */
class DataCheck51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument(
                'check',
                InputArgument::OPTIONAL,
                'Check to run: checksum, writable, db-to-disk, disk-to-db, all',
                'all',
            )
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Stop after this many issues found (0 = no limit)', '100');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $check = $input->getArgument('check');
        $limit = (int) $input->getOption('limit');

        $allowedChecks = ['all', 'checksum', 'writable', 'db-to-disk', 'disk-to-db'];
        if (!in_array($check, $allowedChecks, true)) {
            $output->writeln("<error>Unknown check '$check'. Allowed: " . implode(', ', $allowedChecks) . '</error>');
            return Command::FAILURE;
        }

        $filedir = $CFG->dataroot . DIRECTORY_SEPARATOR . 'filedir';
        if (!is_dir($filedir)) {
            $output->writeln("<error>Filedir not found: $filedir</error>");
            return Command::FAILURE;
        }

        $headers = ['check', 'status', 'path', 'detail'];
        $rows = [];
        $totalIssues = 0;

        if ($check === 'all' || $check === 'checksum') {
            $verbose->step('Running checksum check');
            $issues = $this->runChecksumCheck($filedir, $limit, $verbose);
            $totalIssues += count($issues);
            foreach ($issues as $issue) {
                $rows[] = ['checksum', 'FAIL', $issue, 'SHA1 does not match filename'];
            }
            if (empty($issues)) {
                $rows[] = ['checksum', 'OK', '', 'All file checksums valid'];
            }
        }

        if ($check === 'all' || $check === 'writable') {
            $verbose->step('Running writable check');
            $issues = $this->runWritableCheck($CFG->dataroot, $limit, $verbose);
            $totalIssues += count($issues);
            foreach ($issues as $issue) {
                $rows[] = ['writable', 'FAIL', $issue, 'Not writable by current user'];
            }
            if (empty($issues)) {
                $rows[] = ['writable', 'OK', '', 'All files writable'];
            }
        }

        if ($check === 'all' || $check === 'db-to-disk') {
            $verbose->step('Running db-to-disk check');
            $issues = $this->runDbToDiskCheck($filedir, $limit, $verbose);
            $totalIssues += count($issues);
            foreach ($issues as $issue) {
                $rows[] = ['db-to-disk', 'FAIL', $issue['path'], $issue['detail']];
            }
            if (empty($issues)) {
                $rows[] = ['db-to-disk', 'OK', '', 'All DB files found on disk'];
            }
        }

        if ($check === 'all' || $check === 'disk-to-db') {
            $verbose->step('Running disk-to-db check');
            $issues = $this->runDiskToDbCheck($filedir, $limit, $verbose);
            $totalIssues += count($issues);
            foreach ($issues as $issue) {
                $rows[] = ['disk-to-db', 'FAIL', $issue, 'File on disk but not in DB'];
            }
            if (empty($issues)) {
                $rows[] = ['disk-to-db', 'OK', '', 'All disk files found in DB'];
            }
        }

        $verbose->done("Checks complete. $totalIssues issue(s) found.");

        if ($format === 'table') {
            $table = new Table($output);
            $table->setHeaders($headers);
            $table->setRows($rows);
            $table->render();
        } else {
            $formatter = new ResultFormatter($output, $format);
            $formatter->display($headers, $rows);
        }

        return $totalIssues > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Check SHA1 checksums of files in filedir match their filenames.
     *
     * @return string[] Paths of corrupted files.
     */
    private function runChecksumCheck(string $filedir, int $limit, VerboseLogger $verbose): array
    {
        $errors = [];
        $checked = 0;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($filedir, \RecursiveDirectoryIterator::SKIP_DOTS),
        );

        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }

            $filename = $fileInfo->getFilename();
            if ($filename === 'warning.txt') {
                continue;
            }

            $checked++;
            if ($checked % 1000 === 0) {
                $verbose->info("Checksum: checked $checked files...");
            }

            $hash = sha1_file($fileInfo->getPathname());
            if ($hash !== $filename) {
                $errors[] = $fileInfo->getPathname();
                if ($limit > 0 && count($errors) >= $limit) {
                    $verbose->info("Checksum: reached limit of $limit issues");
                    break;
                }
            }
        }

        $verbose->done("Checksum: checked $checked files, " . count($errors) . ' issue(s)');
        return $errors;
    }

    /**
     * Check all files in dataroot are writable.
     *
     * @return string[] Paths of non-writable files.
     */
    private function runWritableCheck(string $dataroot, int $limit, VerboseLogger $verbose): array
    {
        $errors = [];
        $checked = 0;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dataroot, \RecursiveDirectoryIterator::SKIP_DOTS),
        );

        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }

            $checked++;
            if (!is_writable($fileInfo->getPathname())) {
                $errors[] = $fileInfo->getPathname();
                if ($limit > 0 && count($errors) >= $limit) {
                    break;
                }
            }
        }

        $verbose->done("Writable: checked $checked files, " . count($errors) . ' issue(s)');
        return $errors;
    }

    /**
     * Check every file record in DB has a corresponding file on disk.
     *
     * @return array<int, array{path: string, detail: string}> Missing file details.
     */
    private function runDbToDiskCheck(string $filedir, int $limit, VerboseLogger $verbose): array
    {
        global $DB;

        $errors = [];
        $checked = 0;

        $fs = get_file_storage();
        $filesystem = $fs->get_file_system();

        $rs = $DB->get_recordset_sql(
            "SELECT MAX(id) AS id, contenthash FROM {files} WHERE referencefileid IS NULL GROUP BY contenthash",
        );

        foreach ($rs as $record) {
            $checked++;
            if ($checked % 1000 === 0) {
                $verbose->info("DB-to-disk: checked $checked records...");
            }

            $fileObject = $fs->get_file_by_id($record->id);
            if (!$fileObject) {
                continue;
            }

            $readable = $filesystem->is_file_readable_locally_by_hash($record->contenthash);
            if (!$readable) {
                $hash = $record->contenthash;
                $l1 = substr($hash, 0, 2);
                $l2 = substr($hash, 2, 2);
                $path = $filedir . "/$l1/$l2/$hash";

                $detail = $fileObject->get_component() . ' / ' . $fileObject->get_filearea()
                    . ' "' . $fileObject->get_filename() . '"';

                $errors[] = ['path' => $path, 'detail' => $detail];

                if ($limit > 0 && count($errors) >= $limit) {
                    $verbose->info("DB-to-disk: reached limit of $limit issues");
                    break;
                }
            }
        }

        $rs->close();
        $verbose->done("DB-to-disk: checked $checked records, " . count($errors) . ' issue(s)');
        return $errors;
    }

    /**
     * Check every file on disk has a corresponding entry in the DB.
     *
     * @return string[] Paths of orphaned files.
     */
    private function runDiskToDbCheck(string $filedir, int $limit, VerboseLogger $verbose): array
    {
        global $DB;

        $errors = [];
        $checked = 0;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($filedir, \RecursiveDirectoryIterator::SKIP_DOTS),
        );

        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }

            $filename = $fileInfo->getFilename();
            if ($filename === 'warning.txt') {
                continue;
            }

            $checked++;
            if ($checked % 1000 === 0) {
                $verbose->info("Disk-to-DB: checked $checked files...");
            }

            $count = $DB->count_records('files', ['contenthash' => $filename]);
            if ($count === 0) {
                $errors[] = $fileInfo->getPathname();
                if ($limit > 0 && count($errors) >= $limit) {
                    $verbose->info("Disk-to-DB: reached limit of $limit issues");
                    break;
                }
            }
        }

        $verbose->done("Disk-to-DB: checked $checked files, " . count($errors) . ' issue(s)');
        return $errors;
    }
}
