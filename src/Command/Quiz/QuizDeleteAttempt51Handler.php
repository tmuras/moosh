<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Quiz;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * quiz:delete-attempt implementation for Moodle 5.1.
 */
class QuizDeleteAttempt51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'cmid',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Quiz course module ID(s)',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $cmids = $input->getArgument('cmid');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->dirroot . '/mod/quiz/locallib.php';

        // Validate all cmids and collect quiz data.
        $quizzes = [];
        foreach ($cmids as $cmid) {
            $cmid = (int) $cmid;
            if ($cmid <= 0) {
                $output->writeln("<error>Invalid course module ID: $cmid</error>");
                return Command::FAILURE;
            }

            $cm = get_coursemodule_from_id('quiz', $cmid);
            if (!$cm) {
                $output->writeln("<error>Quiz course module with ID $cmid not found.</error>");
                return Command::FAILURE;
            }

            $quiz = $DB->get_record('quiz', ['id' => $cm->instance]);
            if (!$quiz) {
                $output->writeln("<error>Quiz instance for course module $cmid not found.</error>");
                return Command::FAILURE;
            }

            $attempts = $DB->get_records('quiz_attempts', ['quiz' => $quiz->id]);
            $quizzes[] = [
                'cmid' => $cmid,
                'quiz' => $quiz,
                'attempts' => $attempts,
            ];
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following attempts would be deleted (use --run to execute):</info>');
            foreach ($quizzes as $data) {
                $count = count($data['attempts']);
                $output->writeln("  Quiz '{$data['quiz']->name}' (cmid={$data['cmid']}): $count attempt(s)");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Deleting quiz attempts');
        $totalDeleted = 0;

        foreach ($quizzes as $data) {
            $count = count($data['attempts']);
            foreach ($data['attempts'] as $attempt) {
                quiz_delete_attempt($attempt, $data['quiz']);
                $verbose->info("Deleted attempt {$attempt->id}");
            }
            $totalDeleted += $count;
            $output->writeln("Deleted $count attempt(s) for quiz '{$data['quiz']->name}' (cmid={$data['cmid']}).");
        }

        $output->writeln("Total: $totalDeleted attempt(s) deleted.");

        return Command::SUCCESS;
    }
}
