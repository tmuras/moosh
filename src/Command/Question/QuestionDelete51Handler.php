<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Question;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class QuestionDelete51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('questionid', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Question ID(s) to delete')
            ->addOption('orphaned', null, InputOption::VALUE_NONE, 'Find and delete orphaned questions (missing type records)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');

        $questionIds = $input->getArgument('questionid');
        $orphaned = $input->getOption('orphaned');

        require_once $CFG->libdir . '/questionlib.php';

        if (empty($questionIds) && !$orphaned) {
            $output->writeln('<error>Specify question ID(s) or use --orphaned.</error>');
            return Command::FAILURE;
        }

        if ($orphaned) {
            return $this->handleOrphaned($runMode, $output, $verbose);
        }

        return $this->handleDelete($questionIds, $runMode, $output, $verbose);
    }

    private function handleDelete(array $questionIds, bool $runMode, OutputInterface $output, VerboseLogger $verbose): int
    {
        global $DB;

        // Validate all IDs.
        foreach ($questionIds as $id) {
            $q = $DB->get_record('question', ['id' => (int) $id]);
            if (!$q) {
                $output->writeln("<error>Question with ID $id not found.</error>");
                return Command::FAILURE;
            }
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following question(s) would be deleted (use --run to execute):</info>');
            foreach ($questionIds as $id) {
                $q = $DB->get_record('question', ['id' => (int) $id]);
                $output->writeln("  ID=$id ({$q->name}, type={$q->qtype})");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Deleting ' . count($questionIds) . ' question(s)');
        foreach ($questionIds as $id) {
            $q = $DB->get_record('question', ['id' => (int) $id]);
            question_delete_question((int) $id);
            $output->writeln("Deleted question {$id} ({$q->name}).");
        }

        return Command::SUCCESS;
    }

    private function handleOrphaned(bool $runMode, OutputInterface $output, VerboseLogger $verbose): int
    {
        global $DB;

        $verbose->step('Searching for orphaned questions');

        // Find questions whose qtype plugin table has no matching record.
        $qtypes = $DB->get_records_sql("SELECT DISTINCT qtype FROM {question}");
        $orphanedIds = [];

        foreach ($qtypes as $row) {
            $qtype = $row->qtype;
            $table = "question_{$qtype}";

            // Skip if the table doesn't exist (the qtype itself may be uninstalled).
            $dbman = $DB->get_manager();
            if (!$dbman->table_exists($table)) {
                // All questions of this type are orphaned.
                $questions = $DB->get_records('question', ['qtype' => $qtype], '', 'id, name');
                foreach ($questions as $q) {
                    $orphanedIds[] = $q->id;
                }
                continue;
            }

            // Find questions with no matching record in the type table.
            $sql = "SELECT q.id, q.name
                      FROM {question} q
                     WHERE q.qtype = ?
                       AND NOT EXISTS (SELECT 1 FROM {{$table}} t WHERE t.questionid = q.id)";
            $orphans = $DB->get_records_sql($sql, [$qtype]);
            foreach ($orphans as $q) {
                $orphanedIds[] = $q->id;
            }
        }

        if (empty($orphanedIds)) {
            $output->writeln('No orphaned questions found.');
            return Command::SUCCESS;
        }

        $output->writeln('Found ' . count($orphanedIds) . ' orphaned question(s).');

        if (!$runMode) {
            $output->writeln('<info>Dry run — use --run to delete them.</info>');
            return Command::SUCCESS;
        }

        $verbose->step('Deleting orphaned questions');
        foreach ($orphanedIds as $id) {
            question_delete_question($id);
        }
        $output->writeln('Deleted ' . count($orphanedIds) . ' orphaned question(s).');

        return Command::SUCCESS;
    }
}
