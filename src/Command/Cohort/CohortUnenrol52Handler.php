<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Cohort;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CohortUnenrol52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('cohortid', InputArgument::REQUIRED, 'Cohort ID')
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID')
            ->addOption('role', 'r', InputOption::VALUE_REQUIRED, 'Role shortname (match specific sync)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');

        $cohortId = (int) $input->getArgument('cohortid');
        $courseId = (int) $input->getArgument('courseid');
        $roleName = $input->getOption('role');

        require_once $CFG->libdir . '/enrollib.php';

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

        // Find matching enrol instances.
        $conditions = [
            'enrol' => 'cohort',
            'courseid' => $courseId,
            'customint1' => $cohortId,
        ];

        if ($roleName !== null) {
            $role = $DB->get_record('role', ['shortname' => $roleName]);
            if (!$role) {
                $output->writeln("<error>Role '$roleName' not found.</error>");
                return Command::FAILURE;
            }
            $conditions['roleid'] = $role->id;
        }

        $instances = $DB->get_records('enrol', $conditions);

        if (empty($instances)) {
            $output->writeln("<error>No cohort enrolment found for cohort '{$cohort->name}' in course '{$course->shortname}'.</error>");
            return Command::FAILURE;
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would remove " . count($instances) . " cohort enrolment instance(s) (use --run to execute).</info>");
            return Command::SUCCESS;
        }

        $verbose->step("Removing cohort sync from course '{$course->shortname}'");
        $plugin = enrol_get_plugin('cohort');

        foreach ($instances as $instance) {
            $plugin->delete_instance($instance);
            $verbose->info("Deleted enrol instance {$instance->id}");
        }

        $output->writeln("Removed cohort '{$cohort->name}' sync from course '{$course->shortname}' (" . count($instances) . " instance(s)).");

        return Command::SUCCESS;
    }
}
