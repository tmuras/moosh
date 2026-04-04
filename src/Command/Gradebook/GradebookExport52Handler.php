<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Gradebook;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * gradebook:export implementation for Moodle 5.1.
 */
class GradebookExport52Handler extends BaseHandler
{
    private const VALID_FORMATS = ['txt', 'ods', 'xls', 'xml'];

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID to export grades from')
            ->addArgument('gradeitemids', InputArgument::OPTIONAL, 'Comma-separated grade item IDs (all if omitted)')
            ->addOption('groupid', null, InputOption::VALUE_REQUIRED, 'Group ID to filter by', '0')
            ->addOption('export-feedback', null, InputOption::VALUE_NONE, 'Include feedback in export')
            ->addOption('only-active', null, InputOption::VALUE_NONE, 'Export only active enrolments')
            ->addOption('display-type', null, InputOption::VALUE_REQUIRED, 'Grade display: 1=real, 2=percentage, 3=letter', '1')
            ->addOption('decimal-points', null, InputOption::VALUE_REQUIRED, 'Number of decimal places', '2')
            ->addOption('separator', null, InputOption::VALUE_REQUIRED, 'CSV separator: comma, tab, semicolon', 'comma')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Export format: txt, ods, xls, xml', 'txt');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);

        $courseId = (int) $input->getArgument('courseid');
        $itemIdsArg = $input->getArgument('gradeitemids');
        $groupId = (int) $input->getOption('groupid');
        $exportFeedback = $input->getOption('export-feedback') ? 1 : 0;
        $onlyActive = $input->getOption('only-active') ? 1 : 0;
        $displayType = $input->getOption('display-type');
        $decimalPoints = (int) $input->getOption('decimal-points');
        $separator = $input->getOption('separator');
        $format = $input->getOption('format');

        if (!in_array($format, self::VALID_FORMATS, true)) {
            $output->writeln("<error>Invalid format '$format'. Use one of: " . implode(', ', self::VALID_FORMATS) . "</error>");
            return Command::FAILURE;
        }

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->dirroot . '/grade/export/lib.php';
        require_once $CFG->dirroot . "/grade/export/{$format}/grade_export_{$format}.php";
        require_once $CFG->libdir . '/grade/grade_item.php';
        require_once $CFG->libdir . '/csvlib.class.php';

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        $verbose->step("Exporting gradebook for course '{$course->shortname}' (ID={$courseId})");

        // Regrade if needed before export.
        require_once $CFG->libdir . '/gradelib.php';
        if (grade_needs_regrade_final_grades($courseId)) {
            $verbose->step('Regrading final grades');
            grade_regrade_final_grades($courseId);
        }

        // Build item IDs string.
        if ($itemIdsArg !== null) {
            $itemIds = $itemIdsArg;
        } else {
            $gradeItems = \grade_item::fetch_all(['courseid' => $courseId]);
            if (!$gradeItems) {
                $output->writeln("<error>No grade items found for course $courseId.</error>");
                return Command::FAILURE;
            }
            $ids = [];
            foreach ($gradeItems as $item) {
                $ids[] = $item->id;
            }
            $itemIds = implode(',', $ids);
        }

        $verbose->step('Building export data');
        $formdata = \grade_export::export_bulk_export_data($courseId, $itemIds, $exportFeedback, $onlyActive, $displayType,
                $decimalPoints, null, $separator);

        $exportClass = "grade_export_{$format}";
        $export = new $exportClass($course, $groupId, $formdata);

        $verbose->step("Generating $format output");
        ob_start();
        $export->print_grades();
        $content = ob_get_clean();

        echo $content;

        return Command::SUCCESS;
    }
}
