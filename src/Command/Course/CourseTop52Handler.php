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

class CourseTop52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Number of top courses to show', '10')
            ->addOption('days', null, InputOption::VALUE_REQUIRED, 'Look back this many days', '30');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $limit = (int) $input->getOption('limit');
        $days = (int) $input->getOption('days');

        $cutoff = time() - ($days * 86400);

        $verbose->step("Querying top courses (last $days days)");

        $sql = "SELECT l.courseid, c.shortname, c.fullname, COUNT(*) AS hits
                  FROM {logstore_standard_log} l
                  JOIN {course} c ON c.id = l.courseid
                 WHERE l.timecreated > ?
                   AND l.courseid > 1
                   AND l.eventname = ?
                 GROUP BY l.courseid, c.shortname, c.fullname
                 ORDER BY hits DESC";

        $records = $DB->get_records_sql($sql, [$cutoff, '\\core\\event\\course_viewed'], 0, $limit);

        $verbose->done('Found ' . count($records) . ' course(s)');

        $headers = ['courseid', 'shortname', 'fullname', 'hits'];
        $rows = [];
        foreach ($records as $r) {
            $rows[] = [$r->courseid, $r->shortname, $r->fullname, $r->hits];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
