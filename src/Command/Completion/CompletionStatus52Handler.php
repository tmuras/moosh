<?php
namespace Moosh2\Command\Completion;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CompletionStatus52Handler extends BaseHandler
{
    private const STATE_NAMES = [
        0 => 'incomplete',
        1 => 'complete',
        2 => 'complete-pass',
        3 => 'complete-fail',
    ];

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID')
            ->addOption('userid', null, InputOption::VALUE_REQUIRED, 'User ID (show single user)')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Show all enrolled users');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');

        $courseId = (int) $input->getArgument('courseid');
        $userId = $input->getOption('userid');
        $showAll = $input->getOption('all');

        require_once $CFG->libdir . '/completionlib.php';

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course $courseId not found.</error>");
            return Command::FAILURE;
        }

        $completion = new \completion_info($course);

        if (!$completion->is_enabled()) {
            $output->writeln('<error>Completion tracking is not enabled for this course.</error>');
            return Command::FAILURE;
        }

        if ($userId !== null) {
            return $this->showUserStatus($completion, $course, (int) $userId, $format, $output);
        }

        if ($showAll) {
            return $this->showAllUsers($completion, $course, $format, $output, $verbose);
        }

        $output->writeln('<error>Specify --userid or --all.</error>');
        return Command::FAILURE;
    }

    private function showUserStatus(\completion_info $completion, object $course, int $userId, string $format, OutputInterface $output): int
    {
        global $DB;

        $user = $DB->get_record('user', ['id' => $userId]);
        if (!$user) {
            $output->writeln("<error>User $userId not found.</error>");
            return Command::FAILURE;
        }

        // Get course completion status.
        $courseComplete = $completion->is_course_complete($userId);
        $progress = \core_completion\progress::get_course_progress_percentage($course, $userId);

        $output->writeln("<info>Completion for {$user->username} in course {$course->shortname}:</info>");
        $output->writeln("  Course complete: " . ($courseComplete ? 'yes' : 'no'));
        $output->writeln("  Progress: " . ($progress !== null ? round($progress, 1) . '%' : 'n/a'));
        $output->writeln('');

        // Show activity completion states.
        $activities = $completion->get_activities();
        if (empty($activities)) {
            $output->writeln('No activities with completion tracking.');
            return Command::SUCCESS;
        }

        $headers = ['cmid', 'activity', 'type', 'state', 'timemodified'];
        $rows = [];
        foreach ($activities as $cm) {
            $data = $completion->get_data($cm, false, $userId);
            $stateName = self::STATE_NAMES[$data->completionstate] ?? 'unknown';
            $time = $data->timemodified ? date('Y-m-d H:i', $data->timemodified) : '';
            $rows[] = [$cm->id, $cm->name, $cm->modname, $stateName, $time];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }

    private function showAllUsers(\completion_info $completion, object $course, string $format, OutputInterface $output, VerboseLogger $verbose): int
    {
        $verbose->step('Getting completion data for all users');

        $users = $completion->get_tracked_users();
        if (empty($users)) {
            $output->writeln('No tracked users.');
            return Command::SUCCESS;
        }

        $headers = ['userid', 'username', 'progress', 'complete'];
        $rows = [];
        foreach ($users as $user) {
            $courseComplete = $completion->is_course_complete($user->id);
            $progress = \core_completion\progress::get_course_progress_percentage($course, $user->id);
            $rows[] = [
                $user->id,
                $user->username ?? $user->firstname . ' ' . $user->lastname,
                $progress !== null ? round($progress, 1) . '%' : 'n/a',
                $courseComplete ? 'yes' : 'no',
            ];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
