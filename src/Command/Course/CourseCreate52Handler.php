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
 * course:create implementation for Moodle 5.1.
 */
class CourseCreate52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('shortname', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Course shortname(s) to create')
            ->addOption('category', 'c', InputOption::VALUE_REQUIRED, 'Category ID', '1')
            ->addOption('fullname', null, InputOption::VALUE_REQUIRED, 'Full course name (defaults to shortname)')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'Course format', 'topics')
            ->addOption('numsections', null, InputOption::VALUE_REQUIRED, 'Number of sections')
            ->addOption('idnumber', null, InputOption::VALUE_REQUIRED, 'Course ID number')
            ->addOption('visible', null, InputOption::VALUE_REQUIRED, 'Visibility (1 or 0)', '1');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $runMode = $input->getOption('run');
        $shortnames = $input->getArgument('shortname');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->dirroot . '/course/lib.php';

        $categoryId = (int) $input->getOption('category');
        $fullname = $input->getOption('fullname');
        $courseFormat = $input->getOption('format');
        $numsections = $input->getOption('numsections');
        $idnumber = $input->getOption('idnumber');
        $visible = (int) $input->getOption('visible');

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following courses would be created (use --run to execute):</info>');
            foreach ($shortnames as $shortname) {
                $output->writeln("  $shortname (category: $categoryId, format: $courseFormat)");
            }
            return Command::SUCCESS;
        }

        // Load Moodle course defaults.
        $defaults = get_config('moodlecourse');

        $verbose->step('Creating ' . count($shortnames) . ' course(s)');

        $headers = ['id', 'shortname', 'fullname', 'category'];
        $rows = [];

        foreach ($shortnames as $shortname) {
            $course = new \stdClass();
            $course->shortname = $shortname;
            $course->fullname = $fullname ?? $shortname;
            $course->category = $categoryId;
            $course->format = $courseFormat;
            $course->visible = $visible;
            $course->summary = '';
            $course->summaryformat = FORMAT_HTML;
            $course->startdate = time();
            $course->enablecompletion = $defaults->enablecompletion ?? 1;

            if ($numsections !== null) {
                $course->numsections = (int) $numsections;
            } elseif (isset($defaults->numsections)) {
                $course->numsections = $defaults->numsections;
            }

            if (isset($defaults->courseduration) && $defaults->courseduration > 0) {
                $course->enddate = $course->startdate + $defaults->courseduration;
            }

            if ($idnumber !== null) {
                $course->idnumber = $idnumber;
            }

            $verbose->info("Creating course: $shortname");
            $created = create_course($course);
            $verbose->done("Created course $shortname with ID {$created->id}");

            $rows[] = [$created->id, $created->shortname, $created->fullname, $created->category];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
