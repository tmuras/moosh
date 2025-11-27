<?php
/**
 * Clean up orphaned H5P export files (core_h5p/export) left in system context.
 *
 * - Default: dry-run (report only, no deletion).
 * - With --execute: delete using Moodle File API.
 *
 * Usage:
 * moosh h5p-export-cleanup
 * moosh h5p-export-cleanup --execute
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @introduced 2025-11-25
 * @author     Ewa Soroka
 */
namespace Moosh\Command\Moodle45\H5P;

use Moosh\MooshCommand;

class H5pExportCleanup extends MooshCommand
{
    public function __construct() {
        parent::__construct('export-cleanup', 'h5p');

        // No required arguments.
        $this->minArguments = 0;
        $this->maxArguments = 0;

        // Options:
        // --execute : actually delete records/files (otherwise dry-run)
        $this->addOption(
            'x|execute',
            'perform deletion (without this option only a report is shown)',
            null
        );
    }

    /**
     * Main command execution.
     *
     * Default behaviour: find and report orphaned H5P exports.
     * With --execute: delete them using Moodle’s File API.
     */
    public function execute() {
        global $DB, $CFG;

        require_once($CFG->libdir . '/clilib.php');

        $options   = $this->expandedOptions;
        $doexecute = !empty($options['execute']);

        // SQL to find orphaned H5P export files
        $sql = "
            SELECT
                f.id,
                f.contenthash,
                f.filesize,
                f.filename,
                f.component,
                f.filearea,
                f.contextid,
                f.userid,
                f.timecreated
            FROM {files} f
            WHERE f.filearea = 'export'
              AND f.component = 'core_h5p'
              AND f.filename <> '.'
              AND REVERSE(
                    SUBSTR(
                      SUBSTR(REVERSE(f.filename),
                             LOCATE('.h5p', REVERSE(f.filename)) + 5),
                      1,
                      LOCATE(
                        '-',
                        SUBSTR(REVERSE(f.filename),
                               LOCATE('.h5p', REVERSE(f.filename)) + 5)
                      ) - 1
                    )
                  ) NOT IN (SELECT id FROM {h5p})
        ";

        try {
            $records = $DB->get_records_sql($sql);
        } catch (\Throwable $e) {
            cli_error('Error while executing orphan-detection SQL: ' . $e->getMessage());
        }

        if (empty($records)) {
            cli_writeln('No orphaned H5P export files found (core_h5p/export).');
            return;
        }

        // Extra PHP-level validation:
        // - We only keep records whose filename clearly matches "...<id>.h5p"
        // - and where that <id> really does NOT exist in {h5p}.
        $orphans = [];
        $totalbytes = 0;
        $skipped = 0;

        foreach ($records as $record) {
            $filename = $record->filename;

            //Verify filename follows the expected pattern "...<id>.h5p".
            //as it originally is: $filename = "{$slug}{$content['id']}.h5p";
            if (!preg_match('/(\d+)\.h5p$/', $filename, $matches)) {
                $skipped++;
                cli_writeln("Skipping file id={$record->id} with unexpected filename pattern: {$filename}");
                continue;
            }

            $h5pid = (int)$matches[1];
            cli_writeln("{$filename} - {$h5pid}");

            //Double-check in DB that this id really does NOT exist in {h5p}.
            if ($DB->record_exists('h5p', ['id' => $h5pid])) {
                $skipped++;
                cli_writeln("Skipping file id={$record->id} – filename maps to existing h5p.id={$h5pid}.");
                continue;
            }

            $orphans[] = $record;
            $totalbytes += $record->filesize;
        }

        if (empty($orphans)) {
            cli_writeln('No orphaned H5P export files left after PHP-level validation.');
            if ($skipped > 0) {
                cli_writeln("Note: {$skipped} file(s) were skipped due to unexpected filename pattern or existing h5p.id.");
            }
            return;
        }

        $numfiles = count($orphans);

        list($mb, $gb) = $this->format_sizes($totalbytes);

        cli_writeln("Found {$numfiles} orphaned H5P export file(s) in core_h5p/export.");
        cli_writeln("Total size: {$mb} MiB ({$gb} GiB) that can potentially be reclaimed.");
        if ($skipped > 0) {
            cli_writeln("Note: {$skipped} file(s) were skipped and will not be deleted.");
        }

        // Dry-run mode: only report, do not delete anything.
        if (!$doexecute) {
            cli_writeln('');
            cli_writeln('Dry run only – no files were deleted.');
            cli_writeln('Re-run with --execute to actually remove these files using the Moodle File API.');
            return;
        }

        //delete via Moodle file API.
        $fs = get_file_storage();

        cli_writeln('');
        cli_writeln('Deletion mode enabled (--execute).');
        cli_writeln('Starting to delete orphaned H5P export files...');
        cli_writeln('');

        $deleted   = 0;
        $freedbytes = 0;

        foreach ($orphans as $record) {
            $file = $fs->get_file_by_id($record->id);

            if (!$file) {
                // Record exists in SQL result but file not found via File API.
                cli_writeln("WARNING: File with id={$record->id} not found in file storage (already deleted?).");
                continue;
            }

            $filesize = $file->get_filesize();
            $freedbytes += $filesize;
            $deleted++;

            $created = userdate($record->timecreated);
            $sizeinfo = $this->format_sizes_short($filesize);

            // Log exactly what is being deleted, including DB id.
            cli_writeln(sprintf(
                "Deleting id=%d | filename=%s | contenthash=%s | size=%s | contextid=%d | userid=%d | timecreated=%s",
                $record->id,
                $file->get_filename(),
                $record->contenthash,
                $sizeinfo,
                $record->contextid,
                $record->userid,
                $created
            ));

            // This uses Moodle's File API – removes DB record and
            // physical file (when no other references exist).
            $file->delete();
        }

        list($freedmb, $freedgb) = $this->format_sizes($freedbytes);

        cli_writeln('');
        cli_writeln("Done. Deleted {$deleted} file(s).");
        cli_writeln("Approximate freed space: {$freedmb} MiB ({$freedgb} GiB).");
    }

    /**
     * Return [MiB, GiB] nicely rounded.
     *
     * @param int $bytes
     * @return array
     */
    protected function format_sizes(int $bytes): array
    {
        if ($bytes <= 0) {
            return [0, 0];
        }

        $mb = round($bytes / 1024 / 1024, 2);
        $gb = round($bytes / 1024 / 1024 / 1024, 2);

        return [$mb, $gb];
    }

    /**
     * Short one-line size for logs, e.g. "123.45 MiB".
     *
     * @param int $bytes
     * @return string
     */
    protected function format_sizes_short(int $bytes): string
    {
        list($mb, $gb) = $this->format_sizes($bytes);

        if ($gb >= 0.1) {
            return $gb . ' GiB';
        }

        return $mb . ' MiB';
    }
}

