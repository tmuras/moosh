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
 * gradecategory:create implementation for Moodle 5.1.
 */
class GradeCategoryCreate52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED, 'Grade category name')
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID')
            ->addOption('parent', null, InputOption::VALUE_REQUIRED, 'Parent category ID')
            ->addOption('aggregation', null, InputOption::VALUE_REQUIRED, 'Aggregation type (0=mean, 2=median, 4=min, 6=max, 10=weighted, 13=sum)', '13')
            ->addOption('keephigh', null, InputOption::VALUE_REQUIRED, 'Keep only N highest grades', '0')
            ->addOption('droplow', null, InputOption::VALUE_REQUIRED, 'Drop N lowest grades', '0');
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
        $aggregation = (int) $input->getOption('aggregation');
        $keephigh = (int) $input->getOption('keephigh');
        $droplow = (int) $input->getOption('droplow');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/gradelib.php';
        require_once $CFG->libdir . '/grade/grade_category.php';
        require_once $CFG->libdir . '/grade/grade_item.php';

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        // If parent is specified, validate it exists.
        if ($parentId !== null) {
            $parentCat = \grade_category::fetch(['id' => (int) $parentId, 'courseid' => $courseId]);
            if (!$parentCat) {
                $output->writeln("<error>Parent grade category $parentId not found in course $courseId.</error>");
                return Command::FAILURE;
            }
        }

        if (!$runMode) {
            $parentLabel = $parentId !== null ? "parent=$parentId" : 'top-level';
            $output->writeln("<info>Dry run — would create grade category \"$name\" in course $courseId ($parentLabel, aggregation=$aggregation) (use --run to execute).</info>");
            return Command::SUCCESS;
        }

        $verbose->step("Creating grade category '$name'");

        $gc = new \grade_category(['courseid' => $courseId], false);
        $gc->fullname = $name;
        $gc->courseid = $courseId;
        $gc->aggregation = $aggregation;
        $gc->keephigh = $keephigh;
        $gc->droplow = $droplow;

        if ($parentId !== null) {
            $gc->parent = (int) $parentId;
        }

        $gc->insert('moosh');

        // Reload to get computed fields (path, depth).
        $gc = \grade_category::fetch(['id' => $gc->id]);

        $verbose->done("Created grade category ID={$gc->id}");

        $headers = ['id', 'fullname', 'courseid', 'parent', 'depth', 'aggregation'];
        $rows = [[$gc->id, $gc->fullname, $gc->courseid, $gc->parent ?? 0, $gc->depth, $gc->aggregation]];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
