<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\GradeItem;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * gradeitem:list implementation for Moodle 5.1.
 */
class GradeItemList52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID')
            ->addOption('id-only', 'i', InputOption::VALUE_NONE, 'Display IDs only')
            ->addOption('itemtype', null, InputOption::VALUE_REQUIRED, 'Filter by item type (manual, mod, course, category)')
            ->addOption('category', null, InputOption::VALUE_REQUIRED, 'Filter by grade category ID');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $idOnly = $input->getOption('id-only');

        $courseId = (int) $input->getArgument('courseid');
        $filterType = $input->getOption('itemtype');
        $filterCategory = $input->getOption('category');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/gradelib.php';

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        $verbose->step('Fetching grade items');

        $conditions = ['courseid' => $courseId];
        $params = [$courseId];
        $sql = "SELECT * FROM {grade_items} WHERE courseid = ?";

        if ($filterType !== null) {
            $sql .= " AND itemtype = ?";
            $params[] = $filterType;
        }
        if ($filterCategory !== null) {
            $sql .= " AND categoryid = ?";
            $params[] = (int) $filterCategory;
        }

        $sql .= " ORDER BY sortorder, id";
        $items = $DB->get_records_sql($sql, $params);

        if (empty($items)) {
            $output->writeln('No grade items found.');
            return Command::SUCCESS;
        }

        if ($idOnly) {
            $ids = array_column((array) $items, 'id');
            $output->writeln(implode(' ', $ids));
            return Command::SUCCESS;
        }

        $headers = ['id', 'itemname', 'itemtype', 'itemmodule', 'categoryid', 'gradetype', 'grademax', 'grademin', 'gradepass', 'hidden', 'locked'];
        $rows = [];
        foreach ($items as $item) {
            $rows[] = [
                $item->id,
                $item->itemname ?? '',
                $item->itemtype,
                $item->itemmodule ?? '',
                $item->categoryid ?? 0,
                $item->gradetype,
                $item->grademax,
                $item->grademin,
                $item->gradepass,
                $item->hidden,
                $item->locked,
            ];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
