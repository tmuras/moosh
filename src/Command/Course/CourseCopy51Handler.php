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

class CourseCopy51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('courseid', InputArgument::REQUIRED, 'Source course ID')
            ->addArgument('fullname', InputArgument::REQUIRED, 'New course full name')
            ->addArgument('shortname', InputArgument::REQUIRED, 'New course shortname')
            ->addArgument('categoryid', InputArgument::REQUIRED, 'Destination category ID')
            ->addOption('visible', null, InputOption::VALUE_REQUIRED, 'Visibility (1/0, default: inherit from source)')
            ->addOption('idnumber', null, InputOption::VALUE_REQUIRED, 'ID number for the new course')
            ->addOption('userdata', null, InputOption::VALUE_REQUIRED, 'Copy user data (1/0, default: 0)', '0');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $courseId = (int) $input->getArgument('courseid');
        $fullname = $input->getArgument('fullname');
        $shortname = $input->getArgument('shortname');
        $categoryId = (int) $input->getArgument('categoryid');
        $visible = $input->getOption('visible');
        $idnumber = $input->getOption('idnumber');
        $userdata = (int) $input->getOption('userdata');

        require_once $CFG->dirroot . '/backup/util/helper/copy_helper.class.php';
        require_once $CFG->dirroot . '/backup/util/includes/backup_includes.php';

        // Validate source course
        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Source course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        // Validate shortname uniqueness
        if ($DB->record_exists('course', ['shortname' => $shortname])) {
            $output->writeln("<error>Shortname '$shortname' already exists.</error>");
            return Command::FAILURE;
        }

        // Validate category
        if (!$DB->record_exists('course_categories', ['id' => $categoryId])) {
            $output->writeln("<error>Category with ID $categoryId not found.</error>");
            return Command::FAILURE;
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — would create course copy (use --run to execute):</info>');
            $output->writeln("  Source: {$course->shortname} (ID={$course->id})");
            $output->writeln("  New name: $fullname");
            $output->writeln("  New shortname: $shortname");
            $output->writeln("  Category: $categoryId");
            $output->writeln("  User data: " . ($userdata ? 'yes' : 'no'));
            return Command::SUCCESS;
        }

        $verbose->step('Preparing course copy');

        $formdata = new \stdClass();
        $formdata->courseid = $courseId;
        $formdata->fullname = $fullname;
        $formdata->shortname = $shortname;
        $formdata->category = $categoryId;
        $formdata->visible = $visible !== null ? (int) $visible : $course->visible;
        $formdata->idnumber = $idnumber ?? $course->idnumber;
        $formdata->startdate = $course->startdate;
        $formdata->enddate = $course->enddate ?: 0;
        $formdata->userdata = $userdata;

        $copydata = \copy_helper::process_formdata($formdata);
        \copy_helper::create_copy($copydata);

        $verbose->done('Copy task queued');
        $output->writeln("Course copy ad-hoc task queued: \"{$course->shortname}\" -> \"$shortname\".");

        return Command::SUCCESS;
    }
}
