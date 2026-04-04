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

class FileStats52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('by-component', null, InputOption::VALUE_NONE, 'Break down by component')
            ->addOption('top', null, InputOption::VALUE_REQUIRED, 'Show top N largest files', '0');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $byComponent = $input->getOption('by-component');
        $topN = (int) $input->getOption('top');

        $verbose->step('Gathering file statistics');

        // Overall stats.
        $totalFiles = $DB->count_records_select('files', "filename != '.'");
        $totalSize = $DB->get_field_sql("SELECT SUM(filesize) FROM {files} WHERE filename != '.'");
        $uniqueHashes = $DB->count_records_sql("SELECT COUNT(DISTINCT contenthash) FROM {files} WHERE filename != '.' AND filesize > 0");
        $uniqueSize = $DB->get_field_sql("SELECT SUM(s.size) FROM (SELECT DISTINCT contenthash, filesize AS size FROM {files} WHERE filename != '.' AND filesize > 0) s");
        $duplicateWaste = ($totalSize ?? 0) - ($uniqueSize ?? 0);

        $output->writeln('<info>=== File Storage Statistics ===</info>');
        $headers = ['Metric', 'Value'];
        $rows = [
            ['Total file records', number_format($totalFiles)],
            ['Total size (all records)', $this->formatSize($totalSize ?? 0)],
            ['Unique content hashes', number_format($uniqueHashes)],
            ['Unique content size', $this->formatSize($uniqueSize ?? 0)],
            ['Duplicate space (logical)', $this->formatSize($duplicateWaste)],
        ];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        // By component.
        if ($byComponent) {
            $output->writeln('');
            $output->writeln('<info>=== By Component ===</info>');

            $sql = "SELECT component, COUNT(*) AS file_count, SUM(filesize) AS total_size
                      FROM {files}
                     WHERE filename != '.'
                     GROUP BY component
                     ORDER BY total_size DESC";
            $components = $DB->get_records_sql($sql);

            $headers = ['component', 'files', 'total_size'];
            $rows = [];
            foreach ($components as $c) {
                $rows[] = [$c->component, number_format($c->file_count), $this->formatSize($c->total_size)];
            }
            $formatter->display($headers, $rows);
        }

        // Top N largest files.
        if ($topN > 0) {
            $output->writeln('');
            $output->writeln("<info>=== Top $topN Largest Files ===</info>");

            $sql = "SELECT id, filename, component, filearea, filesize, contenthash
                      FROM {files}
                     WHERE filename != '.'
                     ORDER BY filesize DESC
                     LIMIT $topN";
            $largest = $DB->get_records_sql($sql);

            $headers = ['id', 'filename', 'component', 'filearea', 'size'];
            $rows = [];
            foreach ($largest as $f) {
                $rows[] = [$f->id, $f->filename, $f->component, $f->filearea, $this->formatSize($f->filesize)];
            }
            $formatter->display($headers, $rows);
        }

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
