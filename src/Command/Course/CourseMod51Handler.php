<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Course;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * course:mod implementation for Moodle 5.1.
 */
class CourseMod51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID to modify')
            ->addOption('fullname', null, InputOption::VALUE_REQUIRED, 'Set full name')
            ->addOption('shortname', null, InputOption::VALUE_REQUIRED, 'Set short name')
            ->addOption('idnumber', null, InputOption::VALUE_REQUIRED, 'Set ID number')
            ->addOption('visible', null, InputOption::VALUE_REQUIRED, 'Set visibility (1 or 0)')
            ->addOption('category', null, InputOption::VALUE_REQUIRED, 'Move to category ID')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'Set course format (topics, weeks, etc.)')
            ->addOption('startdate', null, InputOption::VALUE_REQUIRED, 'Set start date (strtotime-parseable)')
            ->addOption('enddate', null, InputOption::VALUE_REQUIRED, 'Set end date (strtotime-parseable)')
            ->addOption('summary', null, InputOption::VALUE_REQUIRED, 'Set course summary')
            ->addOption('lang', null, InputOption::VALUE_REQUIRED, 'Force language')
            ->addOption('guest', null, InputOption::VALUE_REQUIRED, 'Enable/disable guest access (1 or 0)')
            ->addOption('selfenrol', null, InputOption::VALUE_REQUIRED, 'Enable/disable self-enrolment (1 or 0)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $courseId = (int) $input->getArgument('courseid');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->dirroot . '/course/lib.php';
        require_once $CFG->libdir . '/enrollib.php';

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        // Collect all requested changes.
        $courseChanges = [];
        $enrolChanges = [];

        $fieldMap = [
            'fullname' => 'fullname',
            'shortname' => 'shortname',
            'idnumber' => 'idnumber',
            'visible' => 'visible',
            'format' => 'format',
            'summary' => 'summary',
            'lang' => 'lang',
        ];

        foreach ($fieldMap as $option => $field) {
            $value = $input->getOption($option);
            if ($value !== null) {
                $courseChanges[$field] = $value;
            }
        }

        // Parse dates.
        $startdate = $input->getOption('startdate');
        if ($startdate !== null) {
            $ts = strtotime($startdate);
            if ($ts === false) {
                $output->writeln("<error>Invalid start date: $startdate</error>");
                return Command::FAILURE;
            }
            $courseChanges['startdate'] = $ts;
        }

        $enddate = $input->getOption('enddate');
        if ($enddate !== null) {
            $ts = strtotime($enddate);
            if ($ts === false) {
                $output->writeln("<error>Invalid end date: $enddate</error>");
                return Command::FAILURE;
            }
            $courseChanges['enddate'] = $ts;
        }

        $newCategory = $input->getOption('category');
        $guest = $input->getOption('guest');
        $selfenrol = $input->getOption('selfenrol');

        if ($guest !== null) {
            $enrolChanges['guest'] = (int) $guest;
        }
        if ($selfenrol !== null) {
            $enrolChanges['self'] = (int) $selfenrol;
        }

        if (empty($courseChanges) && $newCategory === null && empty($enrolChanges)) {
            $output->writeln('<error>No modifications specified.</error>');
            return Command::FAILURE;
        }

        // Validate category if specified.
        if ($newCategory !== null) {
            $cat = $DB->get_record('course_categories', ['id' => (int) $newCategory]);
            if (!$cat) {
                $output->writeln("<error>Category $newCategory not found.</error>");
                return Command::FAILURE;
            }
        }

        // Build dry-run summary.
        if (!$runMode) {
            $output->writeln("<info>Dry run — would modify course '{$course->shortname}' (ID=$courseId) (use --run to execute):</info>");
            foreach ($courseChanges as $field => $value) {
                $old = $course->$field ?? '';
                $output->writeln("  $field: \"$old\" → \"$value\"");
            }
            if ($newCategory !== null) {
                $output->writeln("  category: {$course->category} → $newCategory");
            }
            foreach ($enrolChanges as $type => $enabled) {
                $label = $enabled ? 'enable' : 'disable';
                $output->writeln("  $type enrolment: $label");
            }
            return Command::SUCCESS;
        }

        $verbose->step("Modifying course '{$course->shortname}' (ID=$courseId)");

        // Apply course field changes.
        if (!empty($courseChanges)) {
            $updateData = (object) $courseChanges;
            $updateData->id = $courseId;
            // update_course needs the full course object merged with changes.
            $fullCourse = clone $course;
            foreach ($courseChanges as $k => $v) {
                $fullCourse->$k = $v;
            }
            update_course($fullCourse);
            $verbose->info('Updated course fields: ' . implode(', ', array_keys($courseChanges)));
        }

        // Move to category.
        if ($newCategory !== null) {
            move_courses([$courseId], (int) $newCategory);
            $verbose->info("Moved to category $newCategory");
        }

        // Toggle enrolment methods.
        foreach ($enrolChanges as $enrolType => $enabled) {
            $this->toggleEnrolment($courseId, $enrolType, $enabled, $verbose);
        }

        // Reload and output.
        $course = $DB->get_record('course', ['id' => $courseId]);
        $headers = ['id', 'shortname', 'fullname', 'category', 'visible', 'format', 'startdate', 'enddate'];
        $rows = [[
            $course->id,
            $course->shortname,
            $course->fullname,
            $course->category,
            $course->visible,
            $course->format,
            $course->startdate ? date('Y-m-d', $course->startdate) : '',
            $course->enddate ? date('Y-m-d', $course->enddate) : '',
        ]];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }

    private function toggleEnrolment(int $courseId, string $enrolType, int $enabled, VerboseLogger $verbose): void
    {
        global $DB;

        $instance = $DB->get_record('enrol', ['courseid' => $courseId, 'enrol' => $enrolType]);
        $plugin = enrol_get_plugin($enrolType);

        if (!$plugin) {
            return;
        }

        if ($enabled) {
            if (!$instance) {
                // Create a new enrolment instance.
                $plugin->add_default_instance($DB->get_record('course', ['id' => $courseId]));
                $verbose->info("Created $enrolType enrolment instance");
            } else {
                $plugin->update_status($instance, ENROL_INSTANCE_ENABLED);
                $verbose->info("Enabled $enrolType enrolment");
            }
        } else {
            if ($instance) {
                $plugin->update_status($instance, ENROL_INSTANCE_DISABLED);
                $verbose->info("Disabled $enrolType enrolment");
            }
        }
    }
}
