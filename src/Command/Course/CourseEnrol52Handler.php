<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Course;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CourseEnrol52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID')
            ->addArgument('userid', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Username(s) or user ID(s) to enrol')
            ->addOption('id', null, InputOption::VALUE_NONE, 'Treat user arguments as numeric IDs instead of usernames')
            ->addOption('role', 'r', InputOption::VALUE_REQUIRED, 'Role shortname (default: student)', 'student')
            ->addOption('start-date', null, InputOption::VALUE_REQUIRED, 'Enrolment start date (strtotime-parseable)')
            ->addOption('end-date', null, InputOption::VALUE_REQUIRED, 'Enrolment end date (strtotime-parseable or duration in days)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB, $PAGE;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $courseId = (int) $input->getArgument('courseid');
        $users = $input->getArgument('userid');
        $byId = $input->getOption('id');
        $roleName = $input->getOption('role');
        $startDateOpt = $input->getOption('start-date');
        $endDateOpt = $input->getOption('end-date');

        require_once $CFG->dirroot . '/enrol/locallib.php';
        require_once $CFG->dirroot . '/group/lib.php';

        // Validate course
        if ($courseId === 1) {
            $output->writeln('<error>Cannot enrol into the site course (ID=1).</error>');
            return Command::FAILURE;
        }
        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        // Validate role
        $role = $DB->get_record('role', ['shortname' => $roleName]);
        if (!$role) {
            $output->writeln("<error>Role '$roleName' not found.</error>");
            return Command::FAILURE;
        }

        // Find manual enrolment plugin
        $verbose->step('Finding manual enrolment instance');
        $context = \context_course::instance($course->id);
        $manager = new \course_enrolment_manager($PAGE, $course);
        $instances = $manager->get_enrolment_instances();

        $manualInstance = null;
        foreach ($instances as $instance) {
            if ($instance->enrol === 'manual') {
                $manualInstance = $instance;
                break;
            }
        }
        if (!$manualInstance) {
            $output->writeln('<error>No manual enrolment instance found for this course.</error>');
            return Command::FAILURE;
        }

        $plugins = $manager->get_enrolment_plugins();
        if (!isset($plugins['manual'])) {
            $output->writeln('<error>Manual enrolment plugin not available.</error>');
            return Command::FAILURE;
        }
        $plugin = $plugins['manual'];

        // Parse dates
        $startDate = 0;
        if ($startDateOpt !== null) {
            $startDate = strtotime($startDateOpt);
            if ($startDate === false) {
                $output->writeln("<error>Invalid start date: $startDateOpt</error>");
                return Command::FAILURE;
            }
        }

        $endDate = 0;
        if ($endDateOpt !== null) {
            $parsed = strtotime($endDateOpt);
            if ($parsed !== false) {
                $endDate = $parsed;
            } elseif (preg_match('/^[1-9]\d*$/', $endDateOpt)) {
                // Duration in days
                $endDate = ($startDate ?: time()) + ((int) $endDateOpt * 86400);
            } else {
                $output->writeln("<error>Invalid end date: $endDateOpt</error>");
                return Command::FAILURE;
            }
        }

        if ($endDate && $startDate && $endDate < $startDate) {
            $output->writeln('<error>End date must be after start date.</error>');
            return Command::FAILURE;
        }

        // Validate users
        $verbose->step('Validating users');
        $userRecords = [];
        foreach ($users as $identifier) {
            if ($byId) {
                $record = $DB->get_record('user', ['id' => (int) $identifier, 'deleted' => 0]);
            } else {
                $record = $DB->get_record('user', ['username' => $identifier, 'deleted' => 0]);
            }
            if (!$record) {
                $output->writeln("<error>User '$identifier' not found.</error>");
                return Command::FAILURE;
            }
            $userRecords[] = $record;
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following enrolments would be created (use --run to execute):</info>');
            $output->writeln("  Course: {$course->shortname} (ID={$course->id})");
            $output->writeln("  Role: {$role->shortname}");
            if ($startDate) {
                $output->writeln('  Start: ' . userdate($startDate));
            }
            if ($endDate) {
                $output->writeln('  End: ' . userdate($endDate));
            }
            foreach ($userRecords as $u) {
                $output->writeln("  User: {$u->username} (ID={$u->id})");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Enrolling ' . count($userRecords) . ' user(s)');
        foreach ($userRecords as $u) {
            $plugin->enrol_user($manualInstance, $u->id, $role->id, $startDate, $endDate);
            $output->writeln("Enrolled \"{$u->username}\" (ID={$u->id}) as {$role->shortname} in \"{$course->shortname}\".");
        }

        return Command::SUCCESS;
    }
}
