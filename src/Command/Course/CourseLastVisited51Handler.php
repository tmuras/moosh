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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CourseLastVisited51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'courseid',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Course ID(s) to check',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $courseIds = $input->getArgument('courseid');

        $headers = ['courseid', 'shortname', 'last_access', 'hours_ago'];
        $rows = [];

        foreach ($courseIds as $courseId) {
            $courseId = (int) $courseId;

            $course = $DB->get_record('course', ['id' => $courseId], 'id, shortname');
            if (!$course) {
                $output->writeln("<error>Course with ID $courseId not found.</error>");
                return Command::FAILURE;
            }

            $lastAccess = $DB->get_record_sql(
                "SELECT MAX(timeaccess) AS lasttime FROM {user_lastaccess} WHERE courseid = ?",
                [$courseId],
            );

            $lastTime = $lastAccess && $lastAccess->lasttime ? (int) $lastAccess->lasttime : 0;

            if ($lastTime > 0) {
                $hoursAgo = (int) ((time() - $lastTime) / 3600);
                $rows[] = [$courseId, $course->shortname, date('Y-m-d H:i:s', $lastTime), $hoursAgo];
            } else {
                $rows[] = [$courseId, $course->shortname, 'never', ''];
            }
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
