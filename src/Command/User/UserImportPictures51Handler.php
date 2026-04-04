<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\User;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * user:import-pictures implementation for Moodle 5.1.
 */
class UserImportPictures51Handler extends BaseHandler
{
    private const SUPPORTED_EXTENSIONS = ['jpg', 'jpeg', 'gif', 'png', 'webp'];

    private const VALID_MATCH_FIELDS = ['username', 'id', 'idnumber', 'email'];

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('directory', InputArgument::OPTIONAL, 'Path to directory containing images')
            ->addOption('match', null, InputOption::VALUE_REQUIRED, 'Field to match filenames against: username (default), id, idnumber, email', 'username')
            ->addOption('overwrite', null, InputOption::VALUE_NONE, 'Replace existing user pictures')
            ->addOption('no-recursive', null, InputOption::VALUE_NONE, 'Do not descend into subdirectories')
            ->addOption('csv', null, InputOption::VALUE_REQUIRED, 'CSV file mapping filenames to user identifiers')
            ->addOption('report', null, InputOption::VALUE_NONE, 'List all users and their picture status')
            ->addOption('report-missing', null, InputOption::VALUE_NONE, 'List only users without a profile picture');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $report = $input->getOption('report');
        $reportMissing = $input->getOption('report-missing');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/gdlib.php';

        if ($report || $reportMissing) {
            return $this->handleReport($input, $output, $verbose, $reportMissing);
        }

        return $this->handleImport($input, $output, $verbose);
    }

    private function handleReport(
        InputInterface $input,
        OutputInterface $output,
        VerboseLogger $verbose,
        bool $missingOnly,
    ): int {
        global $DB;

        $format = $input->getOption('output');

        $verbose->step('Querying users');

        $conditions = 'deleted = 0 AND username != ?';
        $params = ['guest'];

        if ($missingOnly) {
            $conditions .= ' AND picture = 0';
        }

        $users = $DB->get_records_select(
            'user',
            $conditions,
            $params,
            'username ASC',
            'id, username, email, firstname, lastname, picture',
        );

        $verbose->done('Found ' . count($users) . ' user(s)');

        $headers = ['id', 'username', 'email', 'firstname', 'lastname', 'has_picture'];
        $rows = [];
        foreach ($users as $user) {
            $rows[] = [
                $user->id,
                $user->username,
                $user->email,
                $user->firstname,
                $user->lastname,
                $user->picture ? 'yes' : 'no',
            ];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }

    private function handleImport(
        InputInterface $input,
        OutputInterface $output,
        VerboseLogger $verbose,
    ): int {
        global $DB;

        $runMode = $input->getOption('run');
        $directory = $input->getArgument('directory');
        $matchField = $input->getOption('match');
        $overwrite = $input->getOption('overwrite');
        $noRecursive = $input->getOption('no-recursive');
        $csvPath = $input->getOption('csv');

        if (!$directory) {
            $output->writeln('<error>Directory argument is required for import mode. Use --report to list users instead.</error>');
            return Command::FAILURE;
        }

        if (!is_dir($directory)) {
            $output->writeln("<error>Directory '$directory' does not exist or is not a directory.</error>");
            return Command::FAILURE;
        }

        if (!in_array($matchField, self::VALID_MATCH_FIELDS, true)) {
            $valid = implode(', ', self::VALID_MATCH_FIELDS);
            $output->writeln("<error>Invalid --match field '$matchField'. Valid: $valid</error>");
            return Command::FAILURE;
        }

        // Load CSV mapping if provided.
        $csvMapping = null;
        if ($csvPath !== null) {
            $csvMapping = $this->loadCsvMapping($csvPath, $output);
            if ($csvMapping === null) {
                return Command::FAILURE;
            }
            $verbose->done('Loaded ' . count($csvMapping) . ' mapping(s) from CSV');
        }

        // Scan for image files.
        $verbose->step('Scanning directory for images');
        $files = $this->scanDirectory($directory, !$noRecursive);
        $verbose->done('Found ' . count($files) . ' image file(s)');

        if (empty($files)) {
            $output->writeln('No supported image files found in ' . $directory);
            return Command::SUCCESS;
        }

        // Process each file.
        $stats = ['total' => count($files), 'matched' => 0, 'imported' => 0, 'skipped' => 0, 'errors' => 0, 'not_found' => 0];

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following pictures would be imported (use --run to execute):</info>');
        }

        foreach ($files as $filePath) {
            $pathInfo = pathinfo($filePath);
            $basename = $pathInfo['filename'];

            // Determine match value.
            if ($csvMapping !== null) {
                $fullFilename = $pathInfo['basename'];
                // Try full filename first, then basename without extension.
                $matchValue = $csvMapping[$fullFilename] ?? $csvMapping[$basename] ?? null;
                if ($matchValue === null) {
                    $verbose->info("No CSV mapping for file: $fullFilename");
                    $stats['not_found']++;
                    continue;
                }
            } else {
                $matchValue = $basename;
            }

            // Look up user.
            $user = $DB->get_record('user', [$matchField => $matchValue, 'deleted' => 0]);
            if (!$user) {
                $verbose->info("No user found with $matchField='$matchValue' for file: " . basename($filePath));
                $stats['not_found']++;
                continue;
            }

            $stats['matched']++;

            // Check existing picture.
            $hasPicture = $DB->get_field('user', 'picture', ['id' => $user->id]);
            if ($hasPicture && !$overwrite) {
                $verbose->info("Skipping {$user->username} (ID:{$user->id}) — already has picture");
                $stats['skipped']++;
                continue;
            }

            $action = $hasPicture ? 'replace' : 'set';

            if (!$runMode) {
                $relPath = $this->relativePath($directory, $filePath);
                $output->writeln("  $relPath -> {$user->username} (ID:{$user->id}) [$action]");
                $stats['imported']++;
                continue;
            }

            // Process and import the image.
            $context = \context_user::instance($user->id);
            $fileId = process_new_icon($context, 'user', 'icon', 0, $filePath);

            if ($fileId) {
                $DB->set_field('user', 'picture', $fileId, ['id' => $user->id]);
                $verbose->done("Imported picture for {$user->username} (ID:{$user->id})");
                $output->writeln("Imported: {$user->username} (ID:{$user->id}) <- " . basename($filePath));
                $stats['imported']++;
            } else {
                $output->writeln("<error>Failed to process image for {$user->username}: " . basename($filePath) . "</error>");
                $stats['errors']++;
            }
        }

        // Summary.
        $output->writeln('');
        $output->writeln('<info>Summary:</info>');
        $output->writeln("  Total image files: {$stats['total']}");
        $output->writeln("  Matched to users:  {$stats['matched']}");
        $output->writeln("  Imported:          {$stats['imported']}");
        $output->writeln("  Skipped (exists):  {$stats['skipped']}");
        $output->writeln("  Not found:         {$stats['not_found']}");
        if ($stats['errors'] > 0) {
            $output->writeln("  Errors:            {$stats['errors']}");
        }

        return $stats['errors'] > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Scan a directory for supported image files.
     *
     * @return string[] Absolute paths to image files.
     */
    private function scanDirectory(string $directory, bool $recursive): array
    {
        $files = [];

        if ($recursive) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            );
        } else {
            $iterator = new \DirectoryIterator($directory);
        }

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }
            $ext = strtolower($file->getExtension());
            if (in_array($ext, self::SUPPORTED_EXTENSIONS, true)) {
                $files[] = $file->getRealPath();
            }
        }

        sort($files);
        return $files;
    }

    /**
     * Load a CSV file mapping filenames to user identifiers.
     *
     * @return array<string, string>|null Mapping of filename -> match value, or null on error.
     */
    private function loadCsvMapping(string $path, OutputInterface $output): ?array
    {
        if (!is_readable($path)) {
            $output->writeln("<error>Cannot read CSV file '$path'.</error>");
            return null;
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            $output->writeln("<error>Cannot open CSV file '$path'.</error>");
            return null;
        }

        $mapping = [];
        $lineNum = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $lineNum++;
            if (count($row) < 2) {
                continue;
            }

            $filename = trim($row[0]);
            $matchValue = trim($row[1]);

            if ($filename === '' || $matchValue === '') {
                continue;
            }

            // Skip header row if detected.
            if ($lineNum === 1 && preg_match('/^(filename|file|image)/i', $filename)) {
                continue;
            }

            $mapping[$filename] = $matchValue;
        }

        fclose($handle);

        if (empty($mapping)) {
            $output->writeln('<error>CSV file contains no valid mappings.</error>');
            return null;
        }

        return $mapping;
    }

    /**
     * Get a relative path from a base directory.
     */
    private function relativePath(string $base, string $path): string
    {
        $base = rtrim(realpath($base), '/') . '/';
        $real = realpath($path);
        if (str_starts_with($real, $base)) {
            return substr($real, strlen($base));
        }
        return $path;
    }
}
