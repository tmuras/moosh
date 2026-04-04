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
 * gradeitem:create implementation for Moodle 5.1.
 */
class GradeItemCreate52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED, 'Grade item name')
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID')
            ->addOption('category', null, InputOption::VALUE_REQUIRED, 'Grade category ID (default: course root category)')
            ->addOption('gradetype', null, InputOption::VALUE_REQUIRED, 'Grade type: 0=none, 1=value, 2=scale, 3=text', '1')
            ->addOption('grademax', null, InputOption::VALUE_REQUIRED, 'Maximum grade', '100')
            ->addOption('grademin', null, InputOption::VALUE_REQUIRED, 'Minimum grade', '0')
            ->addOption('gradepass', null, InputOption::VALUE_REQUIRED, 'Grade to pass', '0')
            ->addOption('scaleid', null, InputOption::VALUE_REQUIRED, 'Scale ID (when gradetype=2)')
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
        $categoryId = $input->getOption('category');
        $gradeType = (int) $input->getOption('gradetype');
        $gradeMax = (float) $input->getOption('grademax');
        $gradeMin = (float) $input->getOption('grademin');
        $gradePass = (float) $input->getOption('gradepass');
        $scaleId = $input->getOption('scaleid');
        $idNumber = $input->getOption('idnumber');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/gradelib.php';
        require_once $CFG->libdir . '/grade/grade_item.php';
        require_once $CFG->libdir . '/grade/grade_category.php';

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        // Resolve category.
        if ($categoryId !== null) {
            $cat = \grade_category::fetch(['id' => (int) $categoryId, 'courseid' => $courseId]);
            if (!$cat) {
                $output->writeln("<error>Grade category $categoryId not found in course $courseId.</error>");
                return Command::FAILURE;
            }
            $resolvedCatId = (int) $categoryId;
        } else {
            // Use the course root category.
            $rootCat = \grade_category::fetch_course_category($courseId);
            $resolvedCatId = $rootCat->id;
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would create grade item \"$name\" in course $courseId, category $resolvedCatId (use --run to execute).</info>");
            return Command::SUCCESS;
        }

        $verbose->step("Creating grade item '$name'");

        $gi = new \grade_item([
            'courseid' => $courseId,
            'categoryid' => $resolvedCatId,
            'itemtype' => 'manual',
            'itemname' => $name,
            'gradetype' => $gradeType,
            'grademax' => $gradeMax,
            'grademin' => $gradeMin,
            'gradepass' => $gradePass,
        ], false);

        if ($scaleId !== null) {
            $gi->scaleid = (int) $scaleId;
        }
        if ($idNumber !== null) {
            $gi->idnumber = $idNumber;
        }

        $gi->insert('moosh');

        $verbose->done("Created grade item ID={$gi->id}");

        $headers = ['id', 'itemname', 'itemtype', 'courseid', 'categoryid', 'gradetype', 'grademax', 'grademin'];
        $rows = [[$gi->id, $gi->itemname, $gi->itemtype, $gi->courseid, $gi->categoryid, $gi->gradetype, $gi->grademax, $gi->grademin]];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
