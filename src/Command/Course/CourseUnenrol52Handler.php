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

class CourseUnenrol52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID')
            ->addArgument('userid', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'User ID(s) to unenrol')
            ->addOption('plugin', null, InputOption::VALUE_REQUIRED, 'Only unenrol from this enrolment plugin (e.g. manual)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB, $PAGE;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $courseId = (int) $input->getArgument('courseid');
        $userIds = $input->getArgument('userid');
        $pluginFilter = $input->getOption('plugin');

        require_once $CFG->dirroot . '/enrol/locallib.php';
        require_once $CFG->dirroot . '/group/lib.php';

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        $manager = new \course_enrolment_manager($PAGE, $course);

        // Collect all unenrolment actions first
        $verbose->step('Checking enrolments');
        $actions = [];
        foreach ($userIds as $uid) {
            $uid = (int) $uid;
            $user = $DB->get_record('user', ['id' => $uid, 'deleted' => 0]);
            if (!$user) {
                $output->writeln("<error>User with ID $uid not found.</error>");
                return Command::FAILURE;
            }

            $enrolments = $manager->get_user_enrolments($uid);
            if (empty($enrolments)) {
                $output->writeln("<comment>User {$user->username} (ID=$uid) has no enrolments in this course.</comment>");
                continue;
            }

            foreach ($enrolments as $enrolment) {
                [$instance, $plugin] = $manager->get_user_enrolment_components($enrolment);
                if (!$instance || !$plugin || !$plugin->allow_unenrol_user($instance, $enrolment)) {
                    continue;
                }
                if ($pluginFilter && $instance->enrol !== $pluginFilter) {
                    continue;
                }
                $actions[] = [
                    'user' => $user,
                    'instance' => $instance,
                    'plugin' => $plugin,
                    'enrolment' => $enrolment,
                ];
            }
        }

        if (empty($actions)) {
            $output->writeln('<info>No enrolments to remove.</info>');
            return Command::SUCCESS;
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following enrolments would be removed (use --run to execute):</info>');
            foreach ($actions as $a) {
                $output->writeln("  User: {$a['user']->username} (ID={$a['user']->id}), plugin: {$a['instance']->enrol}");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Unenrolling ' . count($actions) . ' enrolment(s)');
        foreach ($actions as $a) {
            $a['plugin']->unenrol_user($a['instance'], $a['user']->id);
            $output->writeln("Unenrolled \"{$a['user']->username}\" (ID={$a['user']->id}) from \"{$course->shortname}\" ({$a['instance']->enrol}).");
        }

        return Command::SUCCESS;
    }
}
