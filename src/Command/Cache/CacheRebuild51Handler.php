<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Cache;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * cache:rebuild implementation for Moodle 5.1.
 */
class CacheRebuild51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('courseid', InputArgument::OPTIONAL, 'Course ID to rebuild')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Rebuild all courses');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $courseId = $input->getArgument('courseid');
        $all = $input->getOption('all');

        require_once $CFG->dirroot . '/course/lib.php';

        if ($all) {
            $verbose->step('Rebuilding course cache for all courses');
            rebuild_course_cache(0, true);
            $output->writeln('Rebuilt course cache for all courses.');
            return Command::SUCCESS;
        }

        if ($courseId !== null) {
            $courseId = (int) $courseId;
            $course = $DB->get_record('course', ['id' => $courseId]);
            if (!$course) {
                $output->writeln("<error>Course with ID $courseId not found.</error>");
                return Command::FAILURE;
            }
            $verbose->step("Rebuilding course cache for course $courseId");
            rebuild_course_cache($courseId, true);
            $output->writeln("Rebuilt course cache for course '{$course->shortname}' (ID=$courseId).");
            return Command::SUCCESS;
        }

        $output->writeln('<error>Specify a course ID or use --all.</error>');
        return Command::FAILURE;
    }
}
