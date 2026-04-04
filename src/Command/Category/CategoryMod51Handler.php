<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Category;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CategoryMod51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('categoryid', InputArgument::REQUIRED, 'Category ID')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Set category name')
            ->addOption('description', 'd', InputOption::VALUE_REQUIRED, 'Set description')
            ->addOption('idnumber', null, InputOption::VALUE_REQUIRED, 'Set ID number')
            ->addOption('visible', null, InputOption::VALUE_REQUIRED, 'Set visibility (1 or 0)')
            ->addOption('parent', null, InputOption::VALUE_REQUIRED, 'Move to parent category ID (0=top level)')
            ->addOption('sortorder', null, InputOption::VALUE_REQUIRED, 'Set position: first, last, up, down')
            ->addOption('resort', null, InputOption::VALUE_REQUIRED, 'Sort subcategories by: name, idnumber')
            ->addOption('resort-courses', null, InputOption::VALUE_REQUIRED, 'Sort courses by: fullname, shortname, idnumber, timecreated')
            ->addOption('move-courses', null, InputOption::VALUE_REQUIRED, 'Move all courses to target category ID')
            ->addOption('recursive', null, InputOption::VALUE_NONE, 'Apply resort recursively');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $catId = (int) $input->getArgument('categoryid');
        $newName = $input->getOption('name');
        $newDesc = $input->getOption('description');
        $newIdnumber = $input->getOption('idnumber');
        $newVisible = $input->getOption('visible');
        $newParent = $input->getOption('parent');
        $sortOrder = $input->getOption('sortorder');
        $resort = $input->getOption('resort');
        $resortCourses = $input->getOption('resort-courses');
        $moveCourses = $input->getOption('move-courses');
        $recursive = $input->getOption('recursive');

        require_once $CFG->dirroot . '/course/lib.php';

        $cat = \core_course_category::get($catId, MUST_EXIST, true);

        $hasChanges = $newName !== null || $newDesc !== null || $newIdnumber !== null
            || $newVisible !== null || $newParent !== null || $sortOrder !== null
            || $resort !== null || $resortCourses !== null || $moveCourses !== null;

        if (!$hasChanges) {
            $output->writeln('<error>No modifications specified.</error>');
            return Command::FAILURE;
        }

        // Build dry-run summary.
        if (!$runMode) {
            $output->writeln("<info>Dry run — would modify category '{$cat->name}' (ID=$catId) (use --run to execute):</info>");
            if ($newName !== null) { $output->writeln("  name → \"$newName\""); }
            if ($newDesc !== null) { $output->writeln("  description → (updated)"); }
            if ($newIdnumber !== null) { $output->writeln("  idnumber → \"$newIdnumber\""); }
            if ($newVisible !== null) { $output->writeln("  visible → $newVisible"); }
            if ($newParent !== null) { $output->writeln("  parent → $newParent"); }
            if ($sortOrder !== null) { $output->writeln("  sortorder → $sortOrder"); }
            if ($resort !== null) { $output->writeln("  resort subcategories by $resort" . ($recursive ? ' (recursive)' : '')); }
            if ($resortCourses !== null) { $output->writeln("  resort courses by $resortCourses" . ($recursive ? ' (recursive)' : '')); }
            if ($moveCourses !== null) { $output->writeln("  move all courses to category $moveCourses"); }
            return Command::SUCCESS;
        }

        $verbose->step("Modifying category '{$cat->name}' (ID=$catId)");

        // Apply property changes.
        $updateData = [];
        if ($newName !== null) { $updateData['name'] = $newName; }
        if ($newDesc !== null) { $updateData['description'] = $newDesc; }
        if ($newIdnumber !== null) { $updateData['idnumber'] = $newIdnumber; }
        if ($newVisible !== null) { $updateData['visible'] = (int) $newVisible; }

        if (!empty($updateData)) {
            $cat->update($updateData);
            $verbose->info('Updated category properties');
        }

        // Move to new parent.
        if ($newParent !== null) {
            $parentId = (int) $newParent;
            if ($parentId > 0) {
                \core_course_category::get($parentId, MUST_EXIST, true);
            }
            $cat->change_parent($parentId);
            $verbose->info("Moved to parent $parentId");
            // Reload after move.
            $cat = \core_course_category::get($catId, MUST_EXIST, true);
        }

        // Change sort order.
        if ($sortOrder !== null) {
            $this->applySortOrder($cat, $sortOrder, $verbose);
            $cat = \core_course_category::get($catId, MUST_EXIST, true);
        }

        // Resort subcategories.
        if ($resort !== null) {
            if ($recursive) {
                $this->resortRecursive($cat, $resort, false, $verbose);
            } else {
                $cat->resort_subcategories($resort);
                $verbose->info("Resorted subcategories by $resort");
            }
        }

        // Resort courses.
        if ($resortCourses !== null) {
            if ($recursive) {
                $this->resortRecursive($cat, null, $resortCourses, $verbose);
            } else {
                $cat->resort_courses($resortCourses);
                $verbose->info("Resorted courses by $resortCourses");
            }
        }

        // Move courses to another category.
        if ($moveCourses !== null) {
            $targetId = (int) $moveCourses;
            $targetCat = \core_course_category::get($targetId, MUST_EXIST, true);
            $courses = $cat->get_courses();
            if (!empty($courses)) {
                $courseIds = array_keys($courses);
                move_courses($courseIds, $targetId);
                $output->writeln("Moved " . count($courseIds) . " course(s) to category '{$targetCat->name}' (ID=$targetId).");
            } else {
                $output->writeln('No courses to move.');
            }
        }

        // Output updated state.
        $cat = \core_course_category::get($catId, MUST_EXIST, true);
        $courseCount = $cat->get_courses_count();
        $childCount = count($cat->get_children());

        $headers = ['id', 'name', 'idnumber', 'parent', 'visible', 'depth', 'courses', 'subcategories'];
        $rows = [[$cat->id, $cat->name, $cat->idnumber, $cat->parent, $cat->visible, $cat->depth, $courseCount, $childCount]];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }

    private function applySortOrder(\core_course_category $cat, string $direction, VerboseLogger $verbose): void
    {
        switch ($direction) {
            case 'up':
                $cat->change_sortorder_by_one(true);
                $verbose->info('Moved up');
                break;
            case 'down':
                $cat->change_sortorder_by_one(false);
                $verbose->info('Moved down');
                break;
            case 'first':
                // Move up until it can't move further.
                while ($cat->change_sortorder_by_one(true)) {
                    $cat = \core_course_category::get($cat->id, MUST_EXIST, true);
                }
                $verbose->info('Moved to first position');
                break;
            case 'last':
                while ($cat->change_sortorder_by_one(false)) {
                    $cat = \core_course_category::get($cat->id, MUST_EXIST, true);
                }
                $verbose->info('Moved to last position');
                break;
        }
    }

    private function resortRecursive(\core_course_category $cat, ?string $subcatField, ?string $courseField, VerboseLogger $verbose): void
    {
        if ($subcatField !== null) {
            $cat->resort_subcategories($subcatField);
        }
        if ($courseField !== null) {
            $cat->resort_courses($courseField);
        }

        foreach ($cat->get_children() as $child) {
            $this->resortRecursive($child, $subcatField, $courseField, $verbose);
        }

        $verbose->info("Resorted category '{$cat->name}' recursively");
    }
}
