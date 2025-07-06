<?php
/**
 * moosh - Moodle Shell - Orphaned Files command
 *
 * This command finds files that are either in the database but not the filesystem,
 * or in the filesystem but not in the database.
 *
 * @example    Check for DB entries missing from the filesystem (default mode)
 *          $ php moosh.php orphaned-files
 *
 * @example    Check for filesystem entries missing from the DB
 *          $ php moosh.php orphaned-files --mode=fs
 *
 * @example    Output the results in JSON format
 *          $ php moosh.php orphaned-files --output=json
 *
 * @copyright  2012 onwards Tomasz Muras
 * @author     Andrej Vitez <contact@andrejvitez.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle40\OrphanedFiles;

use FilesystemIterator;
use Moosh\MooshCommand;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Throwable;

/**
 * Moosh command to find orphaned files in Moodle.
 * This command is useful for maintaining the integrity of the Moodle database and file system.
 *
 * This command helps administrators identify discrepancies between the Moodle
 * database (`mdl_files` table) and the physical file storage (`moodledata/filedir`).
 * It operates in two primary modes:
 *
 * 1.  **`db` mode (default):** Scans the `mdl_files` table and verifies that each
 *     file record corresponds to an existing physical file in the `filedir`. It
 *     reports database entries for which the physical file is missing.
 *
 * 2.  **`fs` mode:** Scans the `moodledata/filedir` directory and verifies that
 *     each physical file has a corresponding record in the `mdl_files` table.
 *     It reports physical files that are not referenced in the database.
 *
 * The output can be formatted as plain text, JSON, XML, or CSV, making it
 * easy to parse or use in other scripts. A summary of the total number and
 * size of orphaned files can also be displayed.
 *
 * If file option is given all output will be redirected to the specified file.
 *
 * @package    Moosh\Command\Moodle40\OrphanedFiles
 * @author     Andrej Vitez <contact@andrejvitez.com>
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see        MooshCommand
 *
 * @example    Check for DB entries missing from the filesystem (default mode).
 *             $ php moosh.php orphaned-files
 *
 * @example    Check for filesystem entries missing from the DB.
 *             $ php moosh.php orphaned-files --mode=fs
 *
 * @example    Output the results in JSON format to a file1 and show a summary.
 *             $ php moosh.php orphaned-files --output-format=json --file=file1.json --summary
 */
class OrphanedFiles extends MooshCommand {

    /**
     * @var string[] Valid modes for file comparison.
     */
    private const VALID_MODES = ['db', 'fs'];

    /**
     * @var string[] Valid output formats.
     */
    private const VALID_OUTPUTS = ['xml', 'json', 'csv', 'plain'];

    /**
     * @var string The selected output format.
     */
    private string $output_format;

    /**
     * @var bool Flag for JSON output to handle commas correctly.
     */
    private bool $is_first_json_item = true;

    /**
     * @var false|resource
     */
    private $output;

    public function __construct() {
        parent::__construct('orphaned', 'file');

        $this->addOption(
            'm|mode:',
            'File comparison mode: [' . implode('|', self::VALID_MODES) . ']. "db" to check for DB ' .
            'records missing from filesystem, "fs" to check for filesystem entries missing in DB. Default is "db".',
            'db'
        );
        $this->addOption(
            'o|output-format:',
            'Output format: [' . implode('|', self::VALID_OUTPUTS) . ']. Default is "plain".', 'plain'
        );
        $this->addOption('s|summary', 'Output a summary of the results to stdout.', false);
        $this->addOption('f|file:', 'Specify output file to redirect all output. Default is stdout.', 'php://stdout');
    }

    public function execute() {
        $mode = $this->expandedOptions['mode'];
        $this->output_format = $this->expandedOptions['output-format'];
        $summary = $this->expandedOptions['summary'];
        $outputfile = $this->expandedOptions['file'];

        if (!in_array($mode, self::VALID_MODES, true)) {
            $this->error_exit("Invalid mode '%s'. Valid modes are: %s", $mode, implode(', ', self::VALID_MODES));
        }

        if (!in_array($this->output_format, self::VALID_OUTPUTS, true)) {
            $this->error_exit(
                "Invalid output format '%s'. Valid formats are: %s", $this->output_format, implode(', ', self::VALID_OUTPUTS)
            );
        }

        if ($this->verbose) {
            $this->message('Starting orphaned files check in mode \'%s\' with output format \'%s\'', $mode, $this->output_format);
        }

        try {
            $this->output = fopen($outputfile, 'wb+');
            if (!is_resource($this->output)) {
                $this->error_exit("Could not open output file '%s' for writing.", $outputfile);
            }

            $this->start_output();
            [$totalitems, $totalbytes] = $mode === 'db' ? $this->scan_database() : $this->scan_filesystem();
            $this->close_output();

        } catch (Throwable $exception) {
            $this->error(sprintf('Error while scanning %s entries: %s', $mode, $exception->getMessage()));
            if ($this->verbose) {
                $this->error(PHP_EOL . 'Exception details: ' . $exception->getTraceAsString());
            }
            $this->error_exit('Terminating due to an error during the scan.');
        } finally {
            if (is_resource($this->output) && $outputfile !== 'php://stdout') {
                fclose($this->output);
            }
        }

        if ($this->verbose && $outputfile !== 'php://stdout') {
            $this->message('Output written to file: %s', $outputfile);
        }

        if ($summary) {
            $this->message(
                "\nSummary\nTotal files checked: %s\nTotal size of missing files: %s\n\n", $totalitems,
                $this->format_bytes($totalbytes)
            );
        }
    }

    private function error_exit(string $message, ...$args): void {
        $this->error($message, ...$args);
        exit(1);
    }

    private function error(string $message, ...$args): void {
        fwrite(STDERR, sprintf("Error: {$message}\n", ...$args));
    }

    private function message(string $message, ...$args): void {
        fwrite(STDERR, sprintf("{$message}\n", ...$args));
    }

    /**
     * Starts the output stream based on the selected format.
     * This could involve printing headers or opening tags.
     */
    private function start_output(): void {
        switch ($this->output_format) {
            case 'json':
                fwrite($this->output, '[' . PHP_EOL);
                $this->is_first_json_item = true;
                break;
            case 'xml':
                fwrite($this->output, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
                fwrite($this->output, '<orphaned_files>' . PHP_EOL);
                break;
            case 'csv':
                fwrite($this->output, '"type","path","original_filename","size_bytes"' . PHP_EOL);
                break;
        }
    }

    /**
     * This method scans the database for file records that do not have a corresponding file in the filesystem.
     *
     * @return array An array tuple containing the total number of items checked and the total size of missing files in bytes.
     */
    private function scan_database(): array {
        global $CFG, $DB;

        $totalbytes = 0;
        $totalitems = 0;

        try {
            $recordset =
                $DB->get_recordset_sql('select id, contenthash, filename, filesize from {files} where referencefileid is null');
            foreach ($recordset as $file) {
                $totalitems++;
                // Skip records with no contenthash, as they cannot be located.
                if (empty($file->contenthash)) {
                    continue;
                }

                $filename = $file->contenthash;
                $filepath = $CFG->dataroot
                    . '/filedir'
                    . '/' . substr($filename, 0, 2)
                    . '/' . substr($filename, 2, 2)
                    . '/' . $filename;

                if (!file_exists($filepath)) {
                    $filesize = (int) $file->filesize;
                    $this->print_item('db', $filepath, $filesize, $file->filename, $file->id);
                    $totalbytes += $filesize;
                }
            }
        } finally {
            $recordset->close();
        }

        return [$totalitems, $totalbytes];
    }

    /**
     * Prints a single orphaned item in the selected format.
     *
     * @param string      $type                'db' (missing from filesystem) or 'fs' (missing from database).
     * @param string      $path                The full path to the file in the filesystem.
     * @param int         $size                The file size in bytes.
     * @param string|null $internal_identifier The original filename from the Moodle database (if available).
     * @param string|null $identifier          The identifier for the item, either a file ID or a contenthash.
     */
    private function print_item(string $type, string $path, int $size, string $internal_identifier = null, string $identifier = null
    ): void {
        $data = [
            'type'       => $type === 'db' ? 'missing_from_fs' : 'missing_from_db',
            'path'       => $path,
            'size_bytes' => $size,
        ];
        if ($internal_identifier) {
            $data['internal_filename'] = $internal_identifier;
        }
        if ($identifier) {
            $data['identifier'] = $identifier;
        }

        switch ($this->output_format) {
            case 'json':
                if (!$this->is_first_json_item) {
                    fwrite($this->output, '    ,' . PHP_EOL);
                }
                foreach (explode(PHP_EOL, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) as $line) {
                    fwrite($this->output, '    ' . $line . PHP_EOL);
                }
                $this->is_first_json_item = false;
                break;
            case 'xml':
                fwrite($this->output, '  <file>' . PHP_EOL);
                foreach ($data as $key => $value) {
                    if ($value !== null) {
                        fwrite(
                            $this->output,
                            '    <' . $key . '>' . htmlspecialchars($value, ENT_XML1, 'UTF-8') . '</' . $key . '>' . PHP_EOL
                        );
                    }
                }
                fwrite($this->output, '  </file>' . PHP_EOL);
                break;
            case 'csv':
                $handle = fopen('php://stdout', 'wb');
                fputcsv($handle, array_values($data));
                fclose($handle);
                break;
            case 'plain':
            default:
                if ($type === 'db') {
                    fwrite(
                        $this->output,
                        sprintf(
                            "Missing in FS -> %s [name: %s, id: %s, size: %s]\n", $path, $internal_identifier, $identifier,
                            $this->format_bytes($size)
                        )
                    );
                } else {
                    fwrite(
                        $this->output,
                        sprintf(
                            "Missing in DB -> %s [contenthash: %s, size: %s]\n", $path, basename($path), $this->format_bytes($size)
                        )
                    );
                }
                break;
        }
    }

    /**
     * Formats a size in bytes into a human-readable string with the appropriate unit.
     *
     * @param int $bytes The number of bytes.
     * @return string The formatted size string (e.g., "1.23 MB").
     */
    private function format_bytes(int $bytes): string {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $power = floor(log($bytes, 1024));
        $value = $bytes / (1024 ** $power);

        return sprintf('%.2f %s', $value, $units[$power]);
    }

    /**
     * This method scans the filesystem for files that do not have a corresponding record in the database.
     *
     * @return array An array tuple containing the total number of items checked and the total size of missing files in bytes.
     */
    private function scan_filesystem(): array {
        global $CFG, $DB;
        $iterator = new RecursiveDirectoryIterator($CFG->dataroot . '/filedir', FilesystemIterator::SKIP_DOTS);
        $totalitems = 0;
        $totalbytes = 0;

        /** @var SplFileInfo $file */
        foreach (new RecursiveIteratorIterator($iterator) as $file) {
            // Allow only files with a valid contenthash (40 hex characters).
            if ($file->isDir() || !preg_match('/^[a-f0-9]{40}$/', $file->getBasename())) {
                continue;
            }
            $totalitems++;
            $filerecord = $DB->get_record('files', ['contenthash' => $file->getBasename()], '*', IGNORE_MULTIPLE);

            if (!$filerecord) {
                $filesize = $file->getSize();
                $this->print_item('fs', $file->getRealPath(), $filesize, null, $file->getBasename());
                $totalbytes += $filesize;
            }
        }

        return [$totalitems, $totalbytes];
    }

    /**
     * Closes the output stream, printing any necessary footers or closing tags.
     */
    private function close_output(): void {
        switch ($this->output_format) {
            case 'json':
                fwrite($this->output, PHP_EOL . ']' . PHP_EOL);
                break;
            case 'xml':
                fwrite($this->output, '</orphaned_files>' . PHP_EOL);
                break;
        }
    }
}