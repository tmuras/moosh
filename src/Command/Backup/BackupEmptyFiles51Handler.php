<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Backup;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * backup:empty-files implementation for Moodle 5.1.
 *
 * Does not require Moodle bootstrap — operates on .mbz files directly.
 */
class BackupEmptyFiles51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the .mbz backup file')
            ->addOption('output-file', null, InputOption::VALUE_REQUIRED, 'Write to a different file instead of overwriting the original');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $file = $input->getArgument('file');
        $outputFile = $input->getOption('output-file');

        if (!file_exists($file)) {
            $output->writeln("<error>File not found: $file</error>");
            return Command::FAILURE;
        }

        $verbose->step('Detecting backup format');
        $archiveType = $this->detectFormat($file);
        if ($archiveType === null) {
            $output->writeln('<error>Unknown archive format. Expected gzip or zip.</error>');
            return Command::FAILURE;
        }

        // Count data files that would be truncated.
        $verbose->step('Listing data files');
        $dataFiles = $this->listDataFiles($file, $archiveType);

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following data files would be emptied (use --run to execute):</info>');
            $output->writeln("  Archive: $file ($archiveType)");
            $output->writeln('  Data files: ' . count($dataFiles));
            $totalSize = 0;
            foreach ($dataFiles as $df) {
                $output->writeln("    {$df['path']} ({$df['size']} bytes)");
                $totalSize += $df['size'];
            }
            $output->writeln("  Total data size to remove: $totalSize bytes");
            return Command::SUCCESS;
        }

        if (empty($dataFiles)) {
            $output->writeln('<info>No data files found in backup — nothing to do.</info>');
            return Command::SUCCESS;
        }

        // Work in a temporary directory.
        $tmpDir = sys_get_temp_dir() . '/moosh_backup_' . uniqid();
        mkdir($tmpDir, 0755, true);

        try {
            // Extract.
            $verbose->step('Extracting backup');
            $this->extractArchive($file, $archiveType, $tmpDir);

            // Truncate data files.
            $verbose->step('Truncating data files');
            $truncated = 0;
            $bytesRemoved = 0;
            $filesDir = $tmpDir . '/files';
            if (is_dir($filesDir)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($filesDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                );
                foreach ($iterator as $fileInfo) {
                    if ($fileInfo->isFile() && $fileInfo->getSize() > 0) {
                        $bytesRemoved += $fileInfo->getSize();
                        file_put_contents($fileInfo->getPathname(), '');
                        $truncated++;
                    }
                }
            }
            $verbose->done("Truncated $truncated file(s), removed $bytesRemoved bytes");

            // Repack.
            $verbose->step('Repacking backup');
            $target = $outputFile ?? $file;
            $this->repackArchive($tmpDir, $archiveType, $target);
            $verbose->done("Written to $target");

            $newSize = filesize($target);
            $output->writeln("Truncated $truncated data file(s), removed $bytesRemoved bytes.");
            $output->writeln("New backup size: $newSize bytes.");
        } finally {
            // Cleanup.
            $this->removeDir($tmpDir);
        }

        return Command::SUCCESS;
    }

    private function detectFormat(string $file): ?string
    {
        $fh = fopen($file, 'rb');
        $bytes = fread($fh, 4);
        fclose($fh);

        if (substr($bytes, 0, 2) === "\x1f\x8b") {
            return 'gzip';
        }
        if (substr($bytes, 0, 4) === "PK\x03\x04") {
            return 'zip';
        }

        return null;
    }

    /**
     * @return array<int, array{path: string, size: int}>
     */
    private function listDataFiles(string $archive, string $type): array
    {
        $files = [];

        if ($type === 'gzip') {
            $lines = shell_exec('tar -tzvf ' . escapeshellarg($archive) . ' 2>/dev/null');
        } else {
            $lines = shell_exec('unzip -l ' . escapeshellarg($archive) . ' 2>/dev/null');
        }

        if ($lines === null) {
            return $files;
        }

        foreach (explode("\n", $lines) as $line) {
            if ($type === 'gzip') {
                // Format: -rw-r--r-- 0/0   33 2026-03-29 16:24 files/52/523358f...
                if (preg_match('/\s+(\d+)\s+\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}\s+(files\/.+)$/', $line, $m)) {
                    $size = (int) $m[1];
                    $path = $m[2];
                    // Skip directories.
                    if (!str_ends_with($path, '/') && $size > 0) {
                        $files[] = ['path' => $path, 'size' => $size];
                    }
                }
            } else {
                // Format:    33  2026-03-29 16:24   files/52/523358f...
                if (preg_match('/^\s*(\d+)\s+\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}\s+(files\/.+)$/', $line, $m)) {
                    $size = (int) $m[1];
                    $path = $m[2];
                    if ($size > 0) {
                        $files[] = ['path' => $path, 'size' => $size];
                    }
                }
            }
        }

        return $files;
    }

    private function extractArchive(string $archive, string $type, string $targetDir): void
    {
        if ($type === 'gzip') {
            $cmd = 'tar -xzf ' . escapeshellarg($archive) . ' -C ' . escapeshellarg($targetDir);
        } else {
            $cmd = 'unzip -q ' . escapeshellarg($archive) . ' -d ' . escapeshellarg($targetDir);
        }

        exec($cmd, $out, $code);
        if ($code !== 0) {
            throw new \RuntimeException("Failed to extract archive: exit code $code");
        }
    }

    private function repackArchive(string $sourceDir, string $type, string $target): void
    {
        // Use absolute path for target.
        if (!str_starts_with($target, '/')) {
            $target = getcwd() . '/' . $target;
        }

        if ($type === 'gzip') {
            $cmd = 'cd ' . escapeshellarg($sourceDir) . ' && tar -czf ' . escapeshellarg($target) . ' .';
        } else {
            // Remove target first if it exists (zip updates in-place).
            if (file_exists($target)) {
                unlink($target);
            }
            $cmd = 'cd ' . escapeshellarg($sourceDir) . ' && zip -qr ' . escapeshellarg($target) . ' .';
        }

        exec($cmd, $out, $code);
        if ($code !== 0) {
            throw new \RuntimeException("Failed to repack archive: exit code $code");
        }
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDir()) {
                rmdir($fileInfo->getPathname());
            } else {
                unlink($fileInfo->getPathname());
            }
        }

        rmdir($dir);
    }
}
