<?php
/**
 * Clean up orphaned H5P export files (mod_hvp/exports).
 *
 * An export is considered orphaned when:
 * - it belongs to component "mod_hvp" and filearea "exports";
 * - its filename follows the mod_hvp export naming convention;
 * - the HVP content ID extracted from the filename no longer exists
 *   in the {hvp} table.
 *
 * Default: dry-run (report only, no deletion).
 * With --execute: delete using Moodle File API.
 *
 * Usage:
 * moosh hvp-export-cleanup
 * moosh hvp-export-cleanup --execute
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @introduced 2026-08-17
 * @author     Ewa Soroka
 */

namespace Moosh\Command\Moodle45\H5P;

use Moosh\MooshCommand;

class HvpExportCleanup extends MooshCommand
{
    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct('export-cleanup', 'hvp');

        // No required arguments.
        $this->minArguments = 0;
        $this->maxArguments = 0;

        // --execute: actually delete files.
        // Without this option the command only reports what would be deleted.
        $this->addOption(
            'x|execute',
            'perform deletion (without this option only a report is shown)',
            null
        );
    }

    /**
     * Main command execution.
     *
     * Default behaviour: find and report orphaned mod_hvp exports.
     * With --execute: delete them using Moodle File API.
     */
    public function execute() {
        global $DB, $CFG;

        require_once($CFG->libdir . '/clilib.php');

        $options = $this->expandedOptions;
        $doexecute = !empty($options['execute']);

        /*
         * Get all real export files created by mod_hvp.
         *
         * The actual orphan detection is intentionally done in PHP rather
         * than SQL so that:
         *
         * - filename parsing is explicit;
         * - unexpected filenames are never deleted;
         * - we double-check every extracted ID against {hvp}.
         */
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
            WHERE f.component = 'mod_hvp'
              AND f.filearea = 'exports'
              AND f.filename <> '.'
            ORDER BY f.id
        ";

        try {
            $records = $DB->get_records_sql($sql);
        } catch (\Throwable $e) {
            cli_error(
                'Error while executing mod_hvp export lookup SQL: ' .
                $e->getMessage()
            );
        }

        if (empty($records)) {
            cli_writeln('No mod_hvp export files found.');
            return;
        }

        /*
         * Validate each candidate.
         *
         * mod_hvp generates export filenames using:
         *
         *     $slug = $content['slug'] ? $content['slug'] . '-' : '';
         *     $filename = "{$slug}{$content['id']}.h5p";
         *
         * Examples:
         *
         *     interactive-video-123.h5p
         *     course-presentation-456.h5p
         *     789.h5p
         *
         * The final numeric part before ".h5p" is the ID from {hvp}.
         */
        $orphans = [];
        $totalbytes = 0;
        $skipped = 0;
        $existing = 0;

        foreach ($records as $record) {
            $filename = $record->filename;

            /*
             * Accept filenames ending with a numeric HVP ID.
             *
             * Examples accepted:
             *
             *     interactive-video-123.h5p
             *     some-slug-456.h5p
             *     789.h5p
             *
             * Anything else is skipped and can never be deleted.
             */
            if (!preg_match('/(?:^|-)(\d+)\.h5p$/', $filename, $matches)) {
                $skipped++;

                cli_writeln(
                    "Skipping file id={$record->id} with unexpected filename pattern: {$filename}"
                );

                continue;
            }

            $hvpid = (int) $matches[1];

            /*
             * Safety check.
             *
             * If the corresponding HVP content still exists, this export
             * is valid and must not be removed.
             */
            if ($DB->record_exists('hvp', ['id' => $hvpid])) {
                $existing++;
                continue;
            }

            /*
             * The filename matches the expected mod_hvp convention and
             * the referenced HVP content no longer exists.
             */
            $record->hvpid = $hvpid;

            $orphans[] = $record;
            $totalbytes += (int) $record->filesize;
        }

        if (empty($orphans)) {
            cli_writeln('');
            cli_writeln('No orphaned mod_hvp export files found.');

            if ($existing > 0) {
                cli_writeln(
                    "{$existing} export file(s) belong to existing HVP content and were kept."
                );
            }

            if ($skipped > 0) {
                cli_writeln(
                    "{$skipped} file(s) were skipped because their filename did not match the expected pattern."
                );
            }

            return;
        }

        $numfiles = count($orphans);

        list($mb, $gb) = $this->format_sizes($totalbytes);

        cli_writeln('');
        cli_writeln(
            "Found {$numfiles} orphaned mod_hvp export file(s) in mod_hvp/exports."
        );
        cli_writeln(
            "Total size: {$mb} MiB ({$gb} GiB) that can potentially be reclaimed."
        );

        if ($existing > 0) {
            cli_writeln(
                "{$existing} export file(s) belong to existing HVP content and will be kept."
            );
        }

        if ($skipped > 0) {
            cli_writeln(
                "{$skipped} file(s) were skipped due to an unexpected filename pattern and will not be deleted."
            );
        }

        /*
         * Dry-run.
         */
        if (!$doexecute) {
            cli_writeln('');
            cli_writeln('Orphaned files:');

            foreach ($orphans as $record) {
                $created = userdate($record->timecreated);
                $sizeinfo = $this->format_sizes_short((int) $record->filesize);

                cli_writeln(sprintf(
                    "id=%d | hvp.id=%d | filename=%s | contenthash=%s | size=%s | contextid=%d | userid=%d | timecreated=%s",
                    $record->id,
                    $record->hvpid,
                    $record->filename,
                    $record->contenthash,
                    $sizeinfo,
                    $record->contextid,
                    $record->userid,
                    $created
                ));
            }

            cli_writeln('');
            cli_writeln('Dry run only - no files were deleted.');
            cli_writeln(
                'Re-run with --execute to actually remove these files using the Moodle File API.'
            );

            return;
        }

        /*
         * Deletion mode.
         *
         * Use Moodle File API rather than deleting directly from {files}.
         */
        $fs = get_file_storage();

        cli_writeln('');
        cli_writeln('Deletion mode enabled (--execute).');
        cli_writeln('Starting to delete orphaned mod_hvp export files...');
        cli_writeln('');

        $deleted = 0;
        $freedbytes = 0;
        $failed = 0;

        foreach ($orphans as $record) {
            /*
             * Final safety check immediately before deletion.
             *
             * This protects against the unlikely situation where HVP
             * content was created/restored between detection and deletion.
             */
            if ($DB->record_exists('hvp', ['id' => $record->hvpid])) {
                cli_writeln(
                    "WARNING: Skipping file id={$record->id}. " .
                    "hvp.id={$record->hvpid} now exists."
                );

                continue;
            }

            $file = $fs->get_file_by_id($record->id);

            if (!$file) {
                cli_writeln(
                    "WARNING: File with id={$record->id} not found in file storage " .
                    "(already deleted?)."
                );

                $failed++;
                continue;
            }

            /*
             * Verify that the file returned by File API is still the file
             * that was selected during detection.
             */
            if (
                $file->get_component() !== 'mod_hvp' ||
                $file->get_filearea() !== 'exports'
            ) {
                cli_writeln(
                    "WARNING: File id={$record->id} is no longer a mod_hvp/exports file. Skipping."
                );

                $failed++;
                continue;
            }

            $filesize = $file->get_filesize();
            $created = userdate($record->timecreated);
            $sizeinfo = $this->format_sizes_short($filesize);

            cli_writeln(sprintf(
                "Deleting id=%d | hvp.id=%d | filename=%s | contenthash=%s | size=%s | contextid=%d | userid=%d | timecreated=%s",
                $record->id,
                $record->hvpid,
                $file->get_filename(),
                $record->contenthash,
                $sizeinfo,
                $record->contextid,
                $record->userid,
                $created
            ));

            try {
                $file->delete();

                $freedbytes += $filesize;
                $deleted++;
            } catch (\Throwable $e) {
                $failed++;

                cli_writeln(
                    "ERROR deleting file id={$record->id}: " .
                    $e->getMessage()
                );
            }
        }

        list($freedmb, $freedgb) = $this->format_sizes($freedbytes);

        cli_writeln('');
        cli_writeln("Done. Deleted {$deleted} file(s).");
        cli_writeln(
            "Approximate freed space: {$freedmb} MiB ({$freedgb} GiB)."
        );

        if ($failed > 0) {
            cli_writeln(
                "WARNING: {$failed} file(s) could not be deleted or were skipped during final validation."
            );
        }
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