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

class QuestionImport51Handler extends BaseHandler
{
    private const VALID_FORMATS = ['xml', 'gift', 'aiken'];

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('file', InputArgument::REQUIRED, 'Path to import file')
            ->addArgument('categoryid', InputArgument::REQUIRED, 'Target question category ID')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Import format: xml, gift, or aiken', 'xml');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB, $USER;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');

        $filePath = $input->getArgument('file');
        $categoryId = (int) $input->getArgument('categoryid');
        $format = $input->getOption('format');

        if (!in_array($format, self::VALID_FORMATS, true)) {
            $output->writeln("<error>Invalid format '$format'. Use: " . implode(', ', self::VALID_FORMATS) . "</error>");
            return Command::FAILURE;
        }

        if (!file_exists($filePath)) {
            $output->writeln("<error>File not found: $filePath</error>");
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

        $context = \context::instance_by_id($category->contextid);

        if (!$runMode) {
            $output->writeln("<info>Dry run — would import questions from '$filePath' ($format format) into category '{$category->name}' (use --run to execute).</info>");
            return Command::SUCCESS;
        }

        $verbose->step("Importing questions from '$filePath' into category '{$category->name}'");

        $formatClass = "qformat_{$format}";
        $qformat = new $formatClass();

        // Set properties directly to bypass CONTEXT_MODULE enforcement in setCategory().
        $qformat->setContexts([$context]);
        $qformat->category = $category;
        $ref = new \ReflectionProperty($qformat, 'importcontext');
        $ref->setValue($qformat, $context);
        $qformat->setCourse($DB->get_record('course', ['id' => SITEID]));
        $qformat->setFilename($filePath);

        ob_start();
        if (!$qformat->importpreprocess()) {
            ob_end_clean();
            $output->writeln('<error>Import preprocessing failed.</error>');
            return Command::FAILURE;
        }

        if (!$qformat->importprocess()) {
            ob_end_clean();
            $output->writeln('<error>Import processing failed.</error>');
            return Command::FAILURE;
        }

        $qformat->importpostprocess();
        ob_end_clean();

        // Count questions imported by checking bank entries in the category.
        $countAfter = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {question_bank_entries} WHERE questioncategoryid = ?",
            [$categoryId]
        );
        $output->writeln("Imported question(s) into category '{$category->name}'. Category now has $countAfter question(s).");

        return Command::SUCCESS;
    }
}
