<?php
namespace Moosh2\Command\Completion;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CompletionReset52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID')
            ->addOption('cmid', null, InputOption::VALUE_REQUIRED, 'Reset only this activity (course module ID)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');

        $courseId = (int) $input->getArgument('courseid');
        $cmId = $input->getOption('cmid');

        require_once $CFG->libdir . '/completionlib.php';

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course $courseId not found.</error>");
            return Command::FAILURE;
        }

        $completion = new \completion_info($course);

        if ($cmId !== null) {
            $cm = get_coursemodule_from_id('', (int) $cmId);
            if (!$cm) {
                $output->writeln("<error>Course module $cmId not found.</error>");
                return Command::FAILURE;
            }

            if (!$runMode) {
                $count = $completion->count_user_data($cm);
                $output->writeln("<info>Dry run — would reset completion for activity '{$cm->name}' ($count record(s)) (use --run to execute).</info>");
                return Command::SUCCESS;
            }

            $verbose->step("Resetting completion for activity '{$cm->name}'");
            $completion->delete_all_state($cm);
            $output->writeln("Reset completion for activity '{$cm->name}'.");
            return Command::SUCCESS;
        }

        // Reset entire course.
        $activityCount = count($completion->get_activities());
        $courseRecords = $completion->count_course_user_data();

        if (!$runMode) {
            $output->writeln("<info>Dry run — would reset all completion data for course '{$course->shortname}' ($activityCount activities, $courseRecords course records) (use --run to execute).</info>");
            return Command::SUCCESS;
        }

        $verbose->step("Resetting all completion for course '{$course->shortname}'");
        $completion->delete_all_completion_data();
        $output->writeln("Reset all completion data for course '{$course->shortname}'.");

        return Command::SUCCESS;
    }
}
