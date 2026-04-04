<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\QuestionCategory;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class QuestionCategoryList51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID')
            ->addOption('id-only', 'i', InputOption::VALUE_NONE, 'Display IDs only');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $idOnly = $input->getOption('id-only');

        $courseId = (int) $input->getArgument('courseid');

        require_once $CFG->libdir . '/questionlib.php';

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        $context = \context_course::instance($courseId);

        $sql = "SELECT qc.*,
                       (SELECT COUNT(*)
                          FROM {question_bank_entries} qbe
                         WHERE qbe.questioncategoryid = qc.id) AS questions
                  FROM {question_categories} qc
                 WHERE qc.contextid = ?
                 ORDER BY qc.parent, qc.sortorder, qc.id";
        $categories = $DB->get_records_sql($sql, [$context->id]);

        if (empty($categories)) {
            $output->writeln('No question categories found.');
            return Command::SUCCESS;
        }

        if ($idOnly) {
            $ids = array_column((array) $categories, 'id');
            $output->writeln(implode(' ', $ids));
            return Command::SUCCESS;
        }

        $headers = ['id', 'name', 'parent', 'idnumber', 'questions'];
        $rows = [];
        foreach ($categories as $cat) {
            $rows[] = [$cat->id, $cat->name, $cat->parent, $cat->idnumber ?? '', $cat->questions];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
