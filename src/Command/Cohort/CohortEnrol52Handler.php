<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Cohort;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CohortEnrol52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('cohortid', InputArgument::REQUIRED, 'Cohort ID')
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID')
            ->addOption('role', 'r', InputOption::VALUE_REQUIRED, 'Role shortname for enrolled users', 'student');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $cohortId = (int) $input->getArgument('cohortid');
        $courseId = (int) $input->getArgument('courseid');
        $roleName = $input->getOption('role');

        require_once $CFG->dirroot . '/cohort/lib.php';
        require_once $CFG->libdir . '/enrollib.php';
        require_once $CFG->dirroot . '/enrol/cohort/locallib.php';

        $cohort = $DB->get_record('cohort', ['id' => $cohortId]);
        if (!$cohort) {
            $output->writeln("<error>Cohort with ID $cohortId not found.</error>");
            return Command::FAILURE;
        }

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        $role = $DB->get_record('role', ['shortname' => $roleName]);
        if (!$role) {
            $output->writeln("<error>Role '$roleName' not found.</error>");
            return Command::FAILURE;
        }

        // Check if cohort enrol instance already exists.
        $existing = $DB->get_record('enrol', [
            'enrol' => 'cohort',
            'courseid' => $courseId,
            'customint1' => $cohortId,
            'roleid' => $role->id,
        ]);

        if ($existing) {
            $output->writeln("<error>Cohort '{$cohort->name}' is already synced to course '{$course->shortname}' with role '$roleName'.</error>");
            return Command::FAILURE;
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would sync cohort '{$cohort->name}' to course '{$course->shortname}' with role '$roleName' (use --run to execute).</info>");
            return Command::SUCCESS;
        }

        $verbose->step("Syncing cohort '{$cohort->name}' to course '{$course->shortname}'");

        $plugin = enrol_get_plugin('cohort');
        $fields = [
            'customint1' => $cohortId,
            'roleid' => $role->id,
            'customint2' => COHORT_NOGROUP,
        ];
        $plugin->add_instance($course, $fields);

        // Run sync to enrol existing members.
        $trace = new \null_progress_trace();
        enrol_cohort_sync($trace, $courseId);

        $verbose->done('Cohort synced');

        $memberCount = $DB->count_records('cohort_members', ['cohortid' => $cohortId]);
        $headers = ['cohort_id', 'cohort_name', 'course_id', 'course_shortname', 'role', 'members'];
        $rows = [[$cohortId, $cohort->name, $courseId, $course->shortname, $roleName, $memberCount]];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
