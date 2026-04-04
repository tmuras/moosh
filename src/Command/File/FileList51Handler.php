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

class FileList51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('contextid', null, InputOption::VALUE_REQUIRED, 'Filter by context ID')
            ->addOption('component', null, InputOption::VALUE_REQUIRED, 'Filter by component (e.g. mod_resource)')
            ->addOption('filearea', null, InputOption::VALUE_REQUIRED, 'Filter by file area (e.g. content)')
            ->addOption('courseid', null, InputOption::VALUE_REQUIRED, 'Filter by course ID (lists all files in course context)')
            ->addOption('id-only', 'i', InputOption::VALUE_NONE, 'Display file IDs only')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Limit results', '100');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $idOnly = $input->getOption('id-only');

        $contextId = $input->getOption('contextid');
        $component = $input->getOption('component');
        $filearea = $input->getOption('filearea');
        $courseId = $input->getOption('courseid');
        $limit = (int) $input->getOption('limit');

        $verbose->step('Querying files');

        $conditions = ["f.filename != '.'"];
        $params = [];

        if ($contextId !== null) {
            $conditions[] = "f.contextid = ?";
            $params[] = (int) $contextId;
        }
        if ($component !== null) {
            $conditions[] = "f.component = ?";
            $params[] = $component;
        }
        if ($filearea !== null) {
            $conditions[] = "f.filearea = ?";
            $params[] = $filearea;
        }
        if ($courseId !== null) {
            $ctx = \context_course::instance((int) $courseId);
            $conditions[] = "(f.contextid = ? OR f.contextid IN (SELECT id FROM {context} WHERE path LIKE ?))";
            $params[] = $ctx->id;
            $params[] = $ctx->path . '/%';
        }

        if (empty($params)) {
            $output->writeln('<error>Specify at least one filter: --contextid, --component, --filearea, or --courseid.</error>');
            return Command::FAILURE;
        }

        $where = implode(' AND ', $conditions);
        $sql = "SELECT f.id, f.contenthash, f.contextid, f.component, f.filearea, f.itemid,
                       f.filepath, f.filename, f.filesize, f.mimetype, f.timecreated
                  FROM {files} f
                 WHERE $where
                 ORDER BY f.component, f.filearea, f.filename
                 LIMIT $limit";

        $files = $DB->get_records_sql($sql, $params);

        if (empty($files)) {
            $output->writeln('No files found.');
            return Command::SUCCESS;
        }

        if ($idOnly) {
            $ids = array_column((array) $files, 'id');
            $output->writeln(implode(' ', $ids));
            return Command::SUCCESS;
        }

        $headers = ['id', 'filename', 'component', 'filearea', 'filesize', 'mimetype', 'contenthash'];
        $rows = [];
        foreach ($files as $f) {
            $rows[] = [$f->id, $f->filename, $f->component, $f->filearea, $f->filesize, $f->mimetype ?? '', substr($f->contenthash, 0, 12) . '...'];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
