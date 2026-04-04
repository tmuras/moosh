<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\GradeCategory;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * gradecategory:list implementation for Moodle 5.1.
 */
class GradeCategoryList52Handler extends BaseHandler
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

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/gradelib.php';

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        $verbose->step('Fetching grade categories');
        $categories = $DB->get_records('grade_categories', ['courseid' => $courseId], 'depth, id');

        if (empty($categories)) {
            $output->writeln('No grade categories found.');
            return Command::SUCCESS;
        }

        if ($idOnly) {
            $ids = array_column((array) $categories, 'id');
            $output->writeln(implode(' ', $ids));
            return Command::SUCCESS;
        }

        $headers = ['id', 'fullname', 'parent', 'depth', 'path', 'aggregation', 'keephigh', 'droplow', 'hidden'];
        $rows = [];
        foreach ($categories as $cat) {
            $rows[] = [
                $cat->id,
                $cat->fullname ?: '(course total)',
                $cat->parent ?? 0,
                $cat->depth,
                $cat->path,
                $cat->aggregation,
                $cat->keephigh,
                $cat->droplow,
                $cat->hidden,
            ];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
