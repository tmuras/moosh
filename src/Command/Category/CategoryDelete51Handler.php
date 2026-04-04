<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Category;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CategoryDelete51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument(
                'categoryid',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'Category ID(s) to delete',
            )
            ->addOption(
                'move-to',
                null,
                InputOption::VALUE_REQUIRED,
                'Move courses and subcategories to this category ID instead of deleting them',
            );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $categoryIds = $input->getArgument('categoryid');
        $moveToId = $input->getOption('move-to');

        // Validate all IDs first
        $verbose->step('Validating categories');
        $categories = [];
        foreach ($categoryIds as $id) {
            $id = (int) $id;
            if ($id <= 0) {
                $output->writeln("<error>Invalid category ID: $id</error>");
                return Command::FAILURE;
            }
            if (!$DB->record_exists('course_categories', ['id' => $id])) {
                $output->writeln("<error>Category with ID $id not found.</error>");
                return Command::FAILURE;
            }
            $categories[$id] = \core_course_category::get($id);
        }

        // Validate move-to target
        $moveToCategory = null;
        if ($moveToId !== null) {
            $moveToId = (int) $moveToId;
            if ($moveToId <= 0) {
                $output->writeln("<error>Invalid move-to category ID: $moveToId</error>");
                return Command::FAILURE;
            }
            if (!$DB->record_exists('course_categories', ['id' => $moveToId])) {
                $output->writeln("<error>Move-to category with ID $moveToId not found.</error>");
                return Command::FAILURE;
            }
            // Cannot move to a category being deleted
            if (isset($categories[$moveToId])) {
                $output->writeln("<error>Cannot move contents to a category that is being deleted (ID $moveToId).</error>");
                return Command::FAILURE;
            }
            $moveToCategory = \core_course_category::get($moveToId);
        }

        // Dry run
        if (!$runMode) {
            $output->writeln('<info>Dry run — the following categories would be deleted (use --run to execute):</info>');
            foreach ($categories as $id => $cat) {
                $courseCount = $cat->get_courses_count();
                $childCount = $DB->count_records('course_categories', ['parent' => $id]);
                $output->writeln("  ID=$id, name=\"{$cat->name}\", courses=$courseCount, subcategories=$childCount");
            }
            if ($moveToCategory) {
                $output->writeln("  Courses and subcategories will be moved to: \"{$moveToCategory->name}\" (ID=$moveToId)");
            } else {
                $output->writeln('  All courses and subcategories will be permanently deleted.');
            }
            return Command::SUCCESS;
        }

        // Execute deletion
        foreach ($categories as $id => $cat) {
            $name = $cat->name;

            if ($moveToCategory) {
                $verbose->step("Moving contents of \"$name\" (ID=$id) to \"{$moveToCategory->name}\"");
                $cat->delete_move($moveToId);
                $verbose->done("Deleted category \"$name\" (contents moved)");
                $output->writeln("Deleted category \"$name\" (ID=$id). Courses moved to \"{$moveToCategory->name}\".");
            } else {
                $verbose->step("Deleting category \"$name\" (ID=$id) and all contents");
                $deletedCourses = $cat->delete_full(false);
                $count = count($deletedCourses);
                foreach ($deletedCourses as $course) {
                    $verbose->info("Deleted course: {$course->shortname}");
                }
                $verbose->done("Deleted category \"$name\" with $count course(s)");
                $output->writeln("Deleted category \"$name\" (ID=$id). $count course(s) deleted.");
            }
        }

        return Command::SUCCESS;
    }
}
