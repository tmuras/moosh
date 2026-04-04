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

class QuestionCategoryCreate52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED, 'Category name')
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID')
            ->addOption('parent', null, InputOption::VALUE_REQUIRED, 'Parent category ID')
            ->addOption('info', null, InputOption::VALUE_REQUIRED, 'Category description', '')
            ->addOption('idnumber', null, InputOption::VALUE_REQUIRED, 'ID number');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $name = $input->getArgument('name');
        $courseId = (int) $input->getArgument('courseid');
        $parentId = $input->getOption('parent');
        $info = $input->getOption('info');
        $idnumber = $input->getOption('idnumber');

        require_once $CFG->libdir . '/questionlib.php';

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        $context = \context_course::instance($courseId);

        // Determine parent.
        if ($parentId !== null) {
            $parent = $DB->get_record('question_categories', ['id' => (int) $parentId]);
            if (!$parent) {
                $output->writeln("<error>Parent category $parentId not found.</error>");
                return Command::FAILURE;
            }
            $newParent = "{$parent->id},{$parent->contextid}";
        } else {
            // Get or create the top category for course context directly.
            $top = $DB->get_record('question_categories', [
                'contextid' => $context->id,
                'parent' => 0,
            ]);
            if (!$top) {
                $top = new \stdClass();
                $top->name = 'top';
                $top->info = '';
                $top->contextid = $context->id;
                $top->parent = 0;
                $top->sortorder = 0;
                $top->stamp = make_unique_id_code();
                $top->id = $DB->insert_record('question_categories', $top);
            }
            $newParent = "{$top->id},{$context->id}";
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would create question category \"$name\" in course $courseId (use --run to execute).</info>");
            return Command::SUCCESS;
        }

        $verbose->step("Creating question category '$name'");

        $manager = new \core_question\category_manager();
        $catId = $manager->add_category($newParent, $name, $info, FORMAT_HTML, $idnumber ?? '');

        $cat = $DB->get_record('question_categories', ['id' => $catId]);

        $headers = ['id', 'name', 'contextid', 'parent', 'idnumber'];
        $rows = [[$cat->id, $cat->name, $cat->contextid, $cat->parent, $cat->idnumber ?? '']];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
