<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Course;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CourseFindBigImages51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('size', 's', InputOption::VALUE_REQUIRED, 'Minimum image size in KB', '1024')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Maximum results', '100');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $sizeKb = (int) $input->getOption('size');
        $limit = (int) $input->getOption('limit');
        $sizeBytes = $sizeKb * 1024;

        $verbose->step("Searching for course overview images > {$sizeKb} KB");

        $sql = "SELECT f.id AS fileid,
                       f.filesize,
                       f.filename,
                       f.mimetype,
                       f.contextid,
                       c.id AS courseid,
                       c.shortname,
                       c.fullname
                  FROM {files} f
                  JOIN {context} ctx ON ctx.id = f.contextid
                  JOIN {course} c ON c.id = ctx.instanceid
                 WHERE f.component = 'course'
                   AND f.filearea = 'overviewfiles'
                   AND ctx.contextlevel = 50
                   AND f.filename <> '.'
                   AND f.mimetype LIKE 'image/%'
                   AND f.filesize > ?
                 ORDER BY f.filesize DESC";

        $records = $DB->get_records_sql($sql, [$sizeBytes], 0, $limit);

        $verbose->done('Found ' . count($records) . ' oversized image(s)');

        $siteUrl = rtrim($CFG->wwwroot, '/');

        $headers = ['courseid', 'shortname', 'fullname', 'filename', 'size_kb', 'mimetype', 'image_url'];
        $rows = [];

        foreach ($records as $r) {
            $imageUrl = "$siteUrl/pluginfile.php/{$r->contextid}/course/overviewfiles/{$r->filename}";
            $rows[] = [
                $r->courseid,
                $r->shortname,
                $r->fullname,
                $r->filename,
                round($r->filesize / 1024),
                $r->mimetype,
                $imageUrl,
            ];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
