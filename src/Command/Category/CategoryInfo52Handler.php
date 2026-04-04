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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * category:info implementation for Moodle 5.1.
 */
class CategoryInfo52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'categoryid',
            InputArgument::REQUIRED,
            'The ID of the category to inspect',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $categoryId = (int) $input->getArgument('categoryid');

        $category = $DB->get_record('course_categories', ['id' => $categoryId]);
        if (!$category) {
            $output->writeln("<error>Category with ID $categoryId not found.</error>");
            return Command::FAILURE;
        }

        $data = [];

        // --- Basic info ---
        $verbose->step('Collecting category information');
        $data['Category ID'] = $category->id;
        $data['Name'] = $category->name;
        $data['ID number'] = $category->idnumber ?: '';
        $data['Description length'] = strlen($category->description ?? '');
        $data['Visible'] = (int) $category->visible;
        $data['Depth'] = (int) $category->depth;

        // Resolve path to names.
        $pathIds = array_filter(array_map('intval', explode('/', $category->path)));
        $pathNames = [];
        foreach ($pathIds as $id) {
            $cat = $DB->get_record('course_categories', ['id' => $id], 'name');
            $pathNames[] = $cat ? $cat->name : "($id)";
        }
        $data['Path'] = implode(' / ', $pathNames);

        // Parent.
        if ($category->parent > 0) {
            $parent = $DB->get_record('course_categories', ['id' => $category->parent], 'name');
            $data['Parent'] = $parent ? $parent->name : "(ID {$category->parent})";
        } else {
            $data['Parent'] = 'none (top-level)';
        }

        $data['Time modified'] = $category->timemodified ? date('Y-m-d H:i:s', $category->timemodified) : '';

        // --- Direct courses ---
        $verbose->step('Counting direct courses');
        $directCourses = $DB->count_records('course', ['category' => $categoryId]);
        $data['Direct courses'] = $directCourses;

        // Visible/hidden courses.
        $visibleCourses = $DB->count_records('course', ['category' => $categoryId, 'visible' => 1]);
        $data['Visible courses'] = $visibleCourses;
        $data['Hidden courses'] = $directCourses - $visibleCourses;

        // --- Subcategories ---
        $verbose->step('Counting subcategories');
        $directSubs = $DB->count_records('course_categories', ['parent' => $categoryId]);
        $data['Direct subcategories'] = $directSubs;

        // Total subcategories (all descendants) using path.
        $totalSubs = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {course_categories} WHERE path LIKE ? AND id <> ?",
            [$category->path . '/%', $categoryId],
        );
        $data['Total subcategories (recursive)'] = $totalSubs;

        // --- Recursive courses (including subcategories) ---
        $verbose->step('Counting recursive courses');
        $allCatIds = $DB->get_fieldset_sql(
            "SELECT id FROM {course_categories} WHERE path LIKE ? OR id = ?",
            [$category->path . '/%', $categoryId],
        );
        if ($allCatIds) {
            [$inSql, $inParams] = $DB->get_in_or_equal($allCatIds);
            $totalCourses = $DB->count_records_sql(
                "SELECT COUNT(*) FROM {course} WHERE category $inSql",
                $inParams,
            );
        } else {
            $totalCourses = $directCourses;
        }
        $data['Total courses (recursive)'] = $totalCourses;

        // --- Enrolled users across all courses in category ---
        $verbose->step('Counting enrolled users');
        if ($allCatIds) {
            [$inSql, $inParams] = $DB->get_in_or_equal($allCatIds);
            $enrolledUsers = $DB->get_record_sql(
                "SELECT COUNT(DISTINCT ue.userid) AS c
                   FROM {user_enrolments} ue
                   JOIN {enrol} e ON e.id = ue.enrolid
                   JOIN {course} co ON co.id = e.courseid
                  WHERE co.category $inSql",
                $inParams,
            );
            $data['Unique enrolled users'] = (int) $enrolledUsers->c;

            $totalEnrolments = $DB->get_record_sql(
                "SELECT COUNT(ue.id) AS c
                   FROM {user_enrolments} ue
                   JOIN {enrol} e ON e.id = ue.enrolid
                   JOIN {course} co ON co.id = e.courseid
                  WHERE co.category $inSql",
                $inParams,
            );
            $data['Total enrolments'] = (int) $totalEnrolments->c;
        } else {
            $data['Unique enrolled users'] = 0;
            $data['Total enrolments'] = 0;
        }

        // --- Activities ---
        $verbose->step('Counting activities');
        if ($allCatIds) {
            [$inSql, $inParams] = $DB->get_in_or_equal($allCatIds);
            $activities = $DB->get_record_sql(
                "SELECT COUNT(cm.id) AS c
                   FROM {course_modules} cm
                   JOIN {course} co ON co.id = cm.course
                  WHERE co.category $inSql",
                $inParams,
            );
            $data['Total activities'] = (int) $activities->c;
        } else {
            $data['Total activities'] = 0;
        }

        // --- Files ---
        $verbose->step('Counting files');
        $categoryContext = \context_coursecat::instance($categoryId, MUST_EXIST);

        // Collect all context IDs under this category (course and module contexts).
        if ($allCatIds) {
            [$inSql, $inParams] = $DB->get_in_or_equal($allCatIds);
            $courseIds = $DB->get_fieldset_sql(
                "SELECT id FROM {course} WHERE category $inSql",
                $inParams,
            );
        } else {
            $courseIds = [];
        }

        if ($courseIds) {
            // Get all context paths for courses in this category.
            [$courseInSql, $courseInParams] = $DB->get_in_or_equal($courseIds);
            $courseContextPaths = $DB->get_fieldset_sql(
                "SELECT path FROM {context} WHERE contextlevel = 50 AND instanceid $courseInSql",
                $courseInParams,
            );

            if ($courseContextPaths) {
                $pathConditions = [];
                $pathParams = [];
                foreach ($courseContextPaths as $cpath) {
                    $pathConditions[] = 'ctx.path LIKE ?';
                    $pathParams[] = $cpath . '/%';
                    $pathConditions[] = 'ctx.path = ?';
                    $pathParams[] = $cpath;
                }
                $pathWhere = '(' . implode(' OR ', $pathConditions) . ')';

                $fileCount = $DB->get_record_sql(
                    "SELECT COUNT(*) AS c FROM {files} f
                       JOIN {context} ctx ON ctx.id = f.contextid
                      WHERE f.filename <> '.' AND $pathWhere",
                    $pathParams,
                );
                $data['Total files'] = (int) $fileCount->c;

                $fileSize = $DB->get_record_sql(
                    "SELECT COALESCE(SUM(f.filesize), 0) AS s FROM {files} f
                       JOIN {context} ctx ON ctx.id = f.contextid
                      WHERE f.filename <> '.' AND $pathWhere",
                    $pathParams,
                );
                $data['Total file size (bytes)'] = (int) $fileSize->s;
            } else {
                $data['Total files'] = 0;
                $data['Total file size (bytes)'] = 0;
            }
        } else {
            $data['Total files'] = 0;
            $data['Total file size (bytes)'] = 0;
        }

        // --- Role assignments ---
        $verbose->step('Counting role assignments');
        $catRoles = $DB->count_records('role_assignments', ['contextid' => $categoryContext->id]);
        $data['Category role assignments'] = $catRoles;

        // --- Render output ---
        $verbose->step('Rendering output');

        if ($format === 'table') {
            $table = new Table($output);
            $table->setHeaders(['Metric', 'Value']);
            foreach ($data as $key => $value) {
                $table->addRow([$key, $value]);
            }
            $table->render();
        } else {
            $formatter = new ResultFormatter($output, $format);
            $headers = array_keys($data);
            $formatter->display($headers, [array_values($data)]);
        }

        return Command::SUCCESS;
    }
}
