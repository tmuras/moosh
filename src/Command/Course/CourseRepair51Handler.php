<?php
namespace Moosh2\Command\Course;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CourseRepair51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('courseid', InputArgument::OPTIONAL, 'Course ID to check/repair')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Check all courses');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $courseId = $input->getArgument('courseid');
        $all = $input->getOption('all');

        require_once $CFG->dirroot . '/course/lib.php';

        if ($courseId === null && !$all) {
            $output->writeln('<error>Specify a course ID or use --all.</error>');
            return Command::FAILURE;
        }

        if ($all) {
            $courseIds = $DB->get_fieldset_select('course', 'id', '1=1', [], 'id');
        } else {
            $course = $DB->get_record('course', ['id' => (int) $courseId]);
            if (!$course) {
                $output->writeln("<error>Course $courseId not found.</error>");
                return Command::FAILURE;
            }
            $courseIds = [(int) $courseId];
        }

        $verbose->step('Checking course integrity for ' . count($courseIds) . ' course(s)');

        $totalIssues = 0;
        $totalFixed = 0;

        foreach ($courseIds as $cid) {
            $problems = course_integrity_check($cid, null, null, true, !$runMode);

            if (!empty($problems)) {
                $issueCount = count($problems);
                $totalIssues += $issueCount;
                $course = $DB->get_record('course', ['id' => $cid], 'shortname');
                $label = $course ? $course->shortname : "ID=$cid";

                $output->writeln("Course '$label' (ID=$cid): $issueCount issue(s)");
                foreach ($problems as $problem) {
                    $output->writeln("  - $problem");
                }

                if ($runMode) {
                    $totalFixed += $issueCount;
                }
            }
        }

        if ($totalIssues === 0) {
            $output->writeln('No integrity issues found.');
            return Command::SUCCESS;
        }

        if ($runMode) {
            $output->writeln("Fixed $totalFixed issue(s) across " . count($courseIds) . " course(s).");
        } else {
            $output->writeln("Found $totalIssues issue(s). Use --run to repair.");
        }

        return Command::SUCCESS;
    }
}
