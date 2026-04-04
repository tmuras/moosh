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

class CourseRestore52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('file', InputArgument::REQUIRED, 'Path to .mbz backup file')
            ->addArgument('categoryid', InputArgument::REQUIRED, 'Target category ID (or course ID with --existing)')
            ->addOption('existing', 'e', InputOption::VALUE_NONE, 'Restore into existing course (second arg is course ID)')
            ->addOption('overwrite', null, InputOption::VALUE_NONE, 'Overwrite existing course content (implies --existing)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB, $USER;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $filePath = $input->getArgument('file');
        $targetId = (int) $input->getArgument('categoryid');
        $existing = $input->getOption('existing');
        $overwrite = $input->getOption('overwrite');

        if ($overwrite) {
            $existing = true;
        }

        require_once $CFG->dirroot . '/backup/util/includes/backup_includes.php';
        require_once $CFG->dirroot . '/backup/util/includes/restore_includes.php';

        // Resolve absolute path
        if ($filePath[0] !== '/') {
            $filePath = getcwd() . '/' . $filePath;
        }

        if (!file_exists($filePath)) {
            $output->writeln("<error>Backup file not found: $filePath</error>");
            return Command::FAILURE;
        }

        if (!is_readable($filePath)) {
            $output->writeln("<error>Backup file not readable: $filePath</error>");
            return Command::FAILURE;
        }

        // Validate target
        if ($existing) {
            $course = $DB->get_record('course', ['id' => $targetId]);
            if (!$course) {
                $output->writeln("<error>Course with ID $targetId not found.</error>");
                return Command::FAILURE;
            }
            $categoryId = $course->category;
        } else {
            $categoryId = $targetId;
            if ($categoryId > 0 && !$DB->record_exists('course_categories', ['id' => $categoryId])) {
                $output->writeln("<error>Category with ID $categoryId not found.</error>");
                return Command::FAILURE;
            }
        }

        // Extract backup to temp directory
        $verbose->step('Extracting backup');
        if (empty($CFG->tempdir)) {
            $CFG->tempdir = $CFG->dataroot . DIRECTORY_SEPARATOR . 'temp';
        }

        $backupDir = 'moosh_restore_' . uniqid();
        $path = $CFG->tempdir . '/backup/' . $backupDir;

        $fp = get_file_packer('application/vnd.moodle.backup');
        $fp->extract_to_pathname($filePath, $path);

        // Parse course info from backup
        $xmlFile = $path . '/course/course.xml';
        if (!file_exists($xmlFile)) {
            $xmlFile = $path . '/moodle_backup.xml';
        }

        $shortname = 'restored_' . date('Ymd_His');
        $fullname = $shortname;

        if (file_exists($xmlFile)) {
            $xml = simplexml_load_file($xmlFile);
            $fn = $xml->xpath('/course/fullname') ?: $xml->xpath('/moodle_backup/information/original_course_fullname');
            $sn = $xml->xpath('/course/shortname') ?: $xml->xpath('/moodle_backup/information/original_course_shortname');
            if ($fn) {
                $fullname = (string) $fn[0];
            }
            if ($sn) {
                $shortname = (string) $sn[0];
            }
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — would restore course (use --run to execute):</info>');
            $output->writeln("  Source: $filePath");
            $output->writeln("  Course name: $fullname");
            $output->writeln("  Short name: $shortname");
            if ($existing) {
                $mode = $overwrite ? 'overwrite existing' : 'add to existing';
                $output->writeln("  Target: $mode course ID=$targetId");
            } else {
                $output->writeln("  Target: new course in category ID=$categoryId");
            }
            // Cleanup temp
            \fulldelete($path);
            return Command::SUCCESS;
        }

        // Generate unique shortname for new courses
        if (!$existing && $DB->get_record('course', ['shortname' => $shortname])) {
            $base = preg_match('/(.*)_(\d+)$/', $shortname, $m) ? $m[1] : $shortname;
            $num = isset($m[2]) ? (int) $m[2] : 1;
            $shortname = $base . '_' . $num;
            while ($DB->get_record('course', ['shortname' => $shortname])) {
                $num++;
                $shortname = $base . '_' . $num;
            }
        }

        if ($existing) {
            $courseId = $targetId;
            if ($overwrite) {
                $verbose->step('Overwriting existing course content');
                $target = \backup::TARGET_CURRENT_DELETING;
            } else {
                $verbose->step('Adding to existing course');
                $target = \backup::TARGET_CURRENT_ADDING;
            }
            $rc = new \restore_controller($backupDir, $courseId, \backup::INTERACTIVE_NO,
                \backup::MODE_GENERAL, $USER->id, $target);
        } else {
            $verbose->step('Creating new course');
            $courseId = \restore_dbops::create_new_course($fullname, $shortname, $categoryId);
            $rc = new \restore_controller($backupDir, $courseId, \backup::INTERACTIVE_NO,
                \backup::MODE_GENERAL, $USER->id, \backup::TARGET_NEW_COURSE);
        }

        if ($rc->get_status() == \backup::STATUS_REQUIRE_CONV) {
            $rc->convert();
        }

        $verbose->step('Running pre-check');
        if (!$rc->execute_precheck()) {
            $check = $rc->get_precheck_results();
            if (isset($check['errors']) && !empty($check['errors'])) {
                $output->writeln('<error>Restore pre-check failed with errors:</error>');
                foreach ($check['errors'] as $error) {
                    $output->writeln("  $error");
                }
                $rc->destroy();
                \fulldelete($path);
                return Command::FAILURE;
            }
        }

        if ($existing && $overwrite) {
            \restore_dbops::delete_course_content($courseId, [
                'keep_roles_and_enrolments' => 0,
                'keep_groups_and_groupings' => 0,
            ]);
        }

        $verbose->step('Executing restore');
        $rc->execute_plan();
        $rc->destroy();

        $verbose->done("Course restored (ID=$courseId, shortname=$shortname)");
        $output->writeln("Restored course ID=$courseId, shortname=\"$shortname\" in category $categoryId.");

        return Command::SUCCESS;
    }
}
