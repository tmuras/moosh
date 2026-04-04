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

class QuestionExport52Handler extends BaseHandler
{
    private const VALID_FORMATS = ['xml', 'gift'];

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('categoryid', InputArgument::REQUIRED, 'Question category ID to export')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Export format: xml or gift', 'xml');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);

        $categoryId = (int) $input->getArgument('categoryid');
        $format = $input->getOption('format');

        if (!in_array($format, self::VALID_FORMATS, true)) {
            $output->writeln("<error>Invalid format '$format'. Use: " . implode(', ', self::VALID_FORMATS) . "</error>");
            return Command::FAILURE;
        }

        require_once $CFG->libdir . '/questionlib.php';
        require_once $CFG->dirroot . '/question/format.php';
        require_once $CFG->dirroot . "/question/format/{$format}/format.php";

        $category = $DB->get_record('question_categories', ['id' => $categoryId]);
        if (!$category) {
            $output->writeln("<error>Question category with ID $categoryId not found.</error>");
            return Command::FAILURE;
        }

        $verbose->step("Exporting questions from category '{$category->name}' in $format format");

        // Get questions in this category.
        $sql = "SELECT q.id
                  FROM {question} q
                  JOIN {question_versions} qv ON qv.questionid = q.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                 WHERE qbe.questioncategoryid = ?
                   AND qv.status = 'ready'
                 ORDER BY q.name";
        $questionRecords = $DB->get_records_sql($sql, [$categoryId]);

        if (empty($questionRecords)) {
            $output->writeln('No questions found in this category.');
            return Command::SUCCESS;
        }

        $questionIds = array_keys($questionRecords);
        $verbose->info('Found ' . count($questionIds) . ' question(s)');

        // Load full question objects and export using format's writequestion().
        $formatClass = "qformat_{$format}";
        $qformat = new $formatClass();

        $context = \context::instance_by_id($category->contextid);
        $qformat->setContexts([$context]);
        $qformat->category = $category;

        $exportContent = '';
        if ($format === 'xml') {
            $exportContent .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<quiz>\n";
        }

        foreach ($questionIds as $qid) {
            $questionData = \question_bank::load_question_data($qid);
            $line = $qformat->writequestion($questionData);
            if ($line !== null) {
                $exportContent .= $line;
            }
        }

        if ($format === 'xml') {
            $exportContent .= "</quiz>\n";
        }

        echo $exportContent;

        return Command::SUCCESS;
    }
}
