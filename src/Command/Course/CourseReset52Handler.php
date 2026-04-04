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

class CourseReset52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID to reset')
            ->addOption('settings', 's', InputOption::VALUE_REQUIRED, 'Space-separated key=value pairs (e.g. "reset_events=1 reset_roles_local=1")');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $courseId = (int) $input->getArgument('courseid');
        $settingsOpt = $input->getOption('settings');

        require_once $CFG->dirroot . '/course/lib.php';

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        // Load default reset settings from all modules
        $verbose->step('Loading default reset settings');
        $defaults = [
            'reset_events' => 1,
            'reset_roles_local' => 1,
            'reset_gradebook_grades' => 1,
            'reset_notes' => 1,
        ];

        $allmods = $DB->get_records('modules');
        if ($allmods) {
            foreach ($allmods as $mod) {
                $modFile = $CFG->dirroot . "/mod/{$mod->name}/lib.php";
                $fn = "{$mod->name}_reset_course_form_defaults";
                if (file_exists($modFile)) {
                    include_once $modFile;
                    if (function_exists($fn)) {
                        $moddefs = $fn($course);
                        if ($moddefs) {
                            $defaults = array_merge($defaults, $moddefs);
                        }
                    }
                }
            }
        }

        $defaults = (object) $defaults;
        $defaults->id = $course->id;

        // Apply custom settings
        if ($settingsOpt) {
            $settings = explode(' ', $settingsOpt);
            foreach ($settings as $setting) {
                $kv = explode('=', $setting, 2);
                if (count($kv) !== 2) {
                    $output->writeln("<error>Invalid setting format: $setting (expected key=value)</error>");
                    return Command::FAILURE;
                }
                [$key, $value] = $kv;
                if (str_contains($value, ',')) {
                    $value = array_filter(explode(',', $value), fn($v) => trim($v) !== '');
                }
                $defaults->$key = $value;

                if ($key === 'reset_start_date') {
                    $defaults->reset_start_date_old = $course->startdate;
                }
                if ($key === 'reset_end_date') {
                    $defaults->reset_end_date_old = $course->enddate;
                }
            }
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following reset settings would be applied (use --run to execute):</info>');
            $output->writeln("  Course: {$course->shortname} (ID={$course->id})");
            foreach ($defaults as $k => $v) {
                if ($k === 'id') {
                    continue;
                }
                $display = is_array($v) ? implode(',', $v) : $v;
                $output->writeln("  $k = $display");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Resetting course data');
        $status = reset_course_userdata($defaults);

        $verbose->done('Course reset complete');
        $output->writeln("Course \"{$course->shortname}\" (ID={$course->id}) has been reset.");

        return Command::SUCCESS;
    }
}
