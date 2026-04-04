<?php
namespace Moosh2\Command\Completion;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CompletionMark52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID')
            ->addOption('userid', null, InputOption::VALUE_REQUIRED, 'User ID')
            ->addOption('cmid', null, InputOption::VALUE_REQUIRED, 'Course module ID (for activity completion)')
            ->addOption('state', null, InputOption::VALUE_REQUIRED, 'State: complete or incomplete', 'complete');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');

        $courseId = (int) $input->getArgument('courseid');
        $userId = $input->getOption('userid');
        $cmId = $input->getOption('cmid');
        $state = $input->getOption('state');

        if ($userId === null) {
            $output->writeln('<error>--userid is required.</error>');
            return Command::FAILURE;
        }

        require_once $CFG->libdir . '/completionlib.php';

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course $courseId not found.</error>");
            return Command::FAILURE;
        }

        $user = $DB->get_record('user', ['id' => (int) $userId]);
        if (!$user) {
            $output->writeln("<error>User $userId not found.</error>");
            return Command::FAILURE;
        }

        $stateValue = $state === 'complete' ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;

        if ($cmId !== null) {
            return $this->markActivity($course, $user, (int) $cmId, $stateValue, $state, $runMode, $output, $verbose);
        }

        return $this->markCourse($course, $user, $stateValue, $state, $runMode, $output, $verbose);
    }

    private function markActivity(object $course, object $user, int $cmId, int $stateValue, string $stateName, bool $runMode, OutputInterface $output, VerboseLogger $verbose): int
    {
        $completion = new \completion_info($course);
        $cm = get_coursemodule_from_id('', $cmId);

        if (!$cm) {
            $output->writeln("<error>Course module $cmId not found.</error>");
            return Command::FAILURE;
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would mark activity '{$cm->name}' (cmid=$cmId) as $stateName for user {$user->username} (use --run to execute).</info>");
            return Command::SUCCESS;
        }

        $verbose->step("Marking activity '{$cm->name}' as $stateName for user {$user->username}");
        $completion->update_state($cm, $stateValue, $user->id, true);
        $output->writeln("Marked activity '{$cm->name}' as $stateName for user {$user->username}.");

        return Command::SUCCESS;
    }

    private function markCourse(object $course, object $user, int $stateValue, string $stateName, bool $runMode, OutputInterface $output, VerboseLogger $verbose): int
    {
        if (!$runMode) {
            $output->writeln("<info>Dry run — would mark course '{$course->shortname}' as $stateName for user {$user->username} (use --run to execute).</info>");
            return Command::SUCCESS;
        }

        $verbose->step("Marking course '{$course->shortname}' as $stateName for user {$user->username}");

        $params = ['userid' => $user->id, 'course' => $course->id];
        $ccompletion = new \completion_completion($params);

        if ($stateValue === COMPLETION_COMPLETE) {
            $ccompletion->mark_complete();
            $output->writeln("Marked course '{$course->shortname}' as complete for user {$user->username}.");
        } else {
            // To mark incomplete, delete the completion record.
            global $DB;
            $DB->delete_records('course_completions', ['userid' => $user->id, 'course' => $course->id]);
            $output->writeln("Marked course '{$course->shortname}' as incomplete for user {$user->username}.");
        }

        return Command::SUCCESS;
    }
}
