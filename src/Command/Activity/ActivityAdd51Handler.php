<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Activity;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * activity:add implementation for Moodle 5.1.
 */
class ActivityAdd51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('type', InputArgument::REQUIRED, 'Activity module type (e.g. assign, forum, quiz, resource, url, page)')
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Activity name')
            ->addOption('section', 's', InputOption::VALUE_REQUIRED, 'Section number', '1')
            ->addOption('idnumber', null, InputOption::VALUE_REQUIRED, 'Activity ID number')
            ->addOption('set', 'S', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Set module property: key=value (repeatable)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $runMode = $input->getOption('run');

        $type = $input->getArgument('type');
        $courseId = (int) $input->getArgument('courseid');
        $name = $input->getOption('name');
        $section = (int) $input->getOption('section');
        $idnumber = $input->getOption('idnumber');

        // Validate module type exists.
        $module = $DB->get_record('modules', ['name' => $type]);
        if (!$module) {
            $output->writeln("<error>Unknown activity type: $type</error>");
            return Command::FAILURE;
        }

        // Validate course exists.
        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        if (!$runMode) {
            $displayName = $name ?? "New $type";
            $output->writeln("<info>Dry run — would create $type activity \"$displayName\" in course $courseId section $section (use --run to execute).</info>");
            return Command::SUCCESS;
        }

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->dirroot . '/course/lib.php';
        require_once $CFG->dirroot . '/course/modlib.php';

        $verbose->step("Creating $type activity in course $courseId");

        $moduleRecord = $DB->get_record('modules', ['name' => $type], '*', MUST_EXIST);

        $moduleInfo = new \stdClass();
        $moduleInfo->modulename = $type;
        $moduleInfo->module = $moduleRecord->id;
        $moduleInfo->visible = 1;
        $moduleInfo->section = $section;
        $moduleInfo->name = $name ?? "New $type";

        if ($idnumber !== null) {
            $moduleInfo->cmidnumber = $idnumber;
        }

        // Provide intro fields expected by most activity types.
        $moduleInfo->introeditor = [
            'text' => '',
            'format' => FORMAT_HTML,
            'itemid' => 0,
        ];

        $result = $this->applySetOptions($input, $output, $moduleInfo);
        if ($result !== null) {
            return $result;
        }

        $instance = add_moduleinfo($moduleInfo, $course);

        $verbose->done("Created $type with course module ID {$instance->coursemodule}");

        $headers = ['cmid', 'module', 'instance', 'course', 'section'];
        $rows = [[$instance->coursemodule, $type, $instance->instance, $courseId, $section]];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }

    protected function applySetOptions(InputInterface $input, OutputInterface $output, \stdClass $moduleInfo): ?int
    {
        $setOptions = $input->getOption('set');

        foreach ($setOptions as $spec) {
            $parts = explode('=', $spec, 2);
            if (count($parts) !== 2) {
                $output->writeln("<error>Invalid --set format: '$spec'. Expected: key=value</error>");
                return Command::FAILURE;
            }
            [$key, $value] = $parts;
            if (is_numeric($value)) {
                $value = str_contains($value, '.') ? (float) $value : (int) $value;
            }
            $moduleInfo->$key = $value;
        }

        return null;
    }
}
