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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FileInfo52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('fileid', InputArgument::OPTIONAL, 'File ID')
            ->addOption('hash', null, InputOption::VALUE_REQUIRED, 'Look up by content hash');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');

        $fileId = $input->getArgument('fileid');
        $hash = $input->getOption('hash');

        if ($fileId === null && $hash === null) {
            $output->writeln('<error>Specify a file ID or --hash.</error>');
            return Command::FAILURE;
        }

        $fs = get_file_storage();

        if ($hash !== null) {
            $files = $DB->get_records('files', ['contenthash' => $hash, 'filename' => $DB->sql_like_escape('') ? null : null]);
            // Get all files with this hash (excluding directories).
            $files = $DB->get_records_select('files', "contenthash = ? AND filename != '.'", [$hash]);
            if (empty($files)) {
                $output->writeln("<error>No files found with hash '$hash'.</error>");
                return Command::FAILURE;
            }
        } else {
            $file = $DB->get_record('files', ['id' => (int) $fileId]);
            if (!$file) {
                $output->writeln("<error>File with ID $fileId not found.</error>");
                return Command::FAILURE;
            }
            $files = [$file];
        }

        $headers = ['Metric', 'Value'];
        $allRows = [];

        foreach ($files as $file) {
            $storedFile = $fs->get_file_by_id($file->id);

            // Build physical path.
            $l1 = substr($file->contenthash, 0, 2);
            $l2 = substr($file->contenthash, 2, 2);
            $physicalPath = $CFG->dataroot . "/filedir/$l1/$l2/{$file->contenthash}";
            $exists = file_exists($physicalPath) ? 'yes' : 'NO (MISSING)';

            $rows = [
                ['File ID', $file->id],
                ['Content hash', $file->contenthash],
                ['Pathname hash', $file->pathnamehash],
                ['Physical path', $physicalPath],
                ['Exists on disk', $exists],
                ['Context ID', $file->contextid],
                ['Component', $file->component],
                ['File area', $file->filearea],
                ['Item ID', $file->itemid],
                ['File path', $file->filepath],
                ['Filename', $file->filename],
                ['File size', $this->formatSize($file->filesize)],
                ['MIME type', $file->mimetype ?? '(none)'],
                ['Author', $file->author ?? '(none)'],
                ['License', $file->license ?? '(none)'],
                ['Created', date('Y-m-d H:i:s', $file->timecreated)],
                ['Modified', date('Y-m-d H:i:s', $file->timemodified)],
            ];

            $allRows = array_merge($allRows, $rows);

            if (count($files) > 1) {
                $allRows[] = ['---', '---'];
            }
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $allRows);

        return Command::SUCCESS;
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
