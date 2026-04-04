<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Question;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class QuestionList52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID')
            ->addOption('id-only', 'i', InputOption::VALUE_NONE, 'Display IDs only')
            ->addOption('category', null, InputOption::VALUE_REQUIRED, 'Filter by question category ID')
            ->addOption('qtype', null, InputOption::VALUE_REQUIRED, 'Filter by question type (multichoice, truefalse, etc.)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $idOnly = $input->getOption('id-only');

        $courseId = (int) $input->getArgument('courseid');
        $filterCategory = $input->getOption('category');
        $filterQtype = $input->getOption('qtype');

        require_once $CFG->libdir . '/questionlib.php';

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        $context = \context_course::instance($courseId);

        $params = [$context->id];
        $sql = "SELECT q.id, q.name, q.qtype, q.defaultmark, q.timecreated, q.timemodified,
                       qbe.questioncategoryid AS categoryid, qc.name AS categoryname,
                       qv.version, qv.status
                  FROM {question} q
                  JOIN {question_versions} qv ON qv.questionid = q.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                  JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
                 WHERE qc.contextid = ?";

        if ($filterCategory !== null) {
            $sql .= " AND qbe.questioncategoryid = ?";
            $params[] = (int) $filterCategory;
        }
        if ($filterQtype !== null) {
            $sql .= " AND q.qtype = ?";
            $params[] = $filterQtype;
        }

        $sql .= " ORDER BY qc.name, q.name, qv.version DESC";
        $questions = $DB->get_records_sql($sql, $params);

        if (empty($questions)) {
            $output->writeln('No questions found.');
            return Command::SUCCESS;
        }

        if ($idOnly) {
            $ids = array_column((array) $questions, 'id');
            $output->writeln(implode(' ', $ids));
            return Command::SUCCESS;
        }

        $headers = ['id', 'name', 'qtype', 'defaultmark', 'categoryid', 'categoryname', 'version', 'status'];
        $rows = [];
        foreach ($questions as $q) {
            $rows[] = [$q->id, $q->name, $q->qtype, $q->defaultmark, $q->categoryid, $q->categoryname, $q->version, $q->status];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
