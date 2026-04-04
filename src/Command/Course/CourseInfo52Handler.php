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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * course:info implementation for Moodle 5.1.
 */
class CourseInfo52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'courseid',
            InputArgument::REQUIRED,
            'The ID of the course to inspect',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $runMode = $input->getOption('run');
        $courseid = (int) $input->getArgument('courseid');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->dirroot . '/course/lib.php';
        require_once $CFG->libdir . '/accesslib.php';

        $course = get_course($courseid);
        $coursecontext = \context_course::instance($courseid, MUST_EXIST);

        $data = [];
        $data['Course ID'] = $course->id;

        // --- Contexts ---
        $verbose->step('Counting contexts');
        $contexts = [];
        $contextByLevel = [];
        $contextByModule = [];

        $dbcontexts = $DB->get_records_sql(
            "SELECT * FROM {context} WHERE path LIKE ?",
            [$coursecontext->path . '/%'],
        );

        foreach ($dbcontexts as $dbcontext) {
            $ctx = \context::instance_by_id($dbcontext->id, MUST_EXIST);
            $contexts[$dbcontext->id] = $ctx;

            if (!isset($contextByLevel[$ctx->contextlevel])) {
                $contextByLevel[$ctx->contextlevel] = 0;
            }
            $contextByLevel[$ctx->contextlevel]++;

            if (is_a($ctx, 'context_module')) {
                $cm = $DB->get_record('course_modules', ['id' => $ctx->instanceid]);
                if (!isset($contextByModule[$cm->module])) {
                    $contextByModule[$cm->module] = 0;
                }
                $contextByModule[$cm->module]++;
            }
        }
        ksort($contextByLevel);
        ksort($contextByModule);

        $data['Number of contexts'] = count($contexts);
        foreach ($contextByLevel as $level => $count) {
            $data["Contexts at level $level"] = $count;
        }
        $moduleNames = $DB->get_records_menu('modules', null, '', 'id, name');
        foreach ($contextByModule as $moduleId => $count) {
            $name = $moduleNames[$moduleId] ?? "module-$moduleId";
            $data["Contexts for mod $name"] = $count;
        }

        // --- Question bank questions ---
        $verbose->step('Counting question bank questions');
        $questions = $DB->get_record_sql(
            "SELECT COUNT(DISTINCT qbe.id) AS count
               FROM {question_bank_entries} qbe
               JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
               JOIN {context} ctx ON ctx.id = qc.contextid
              WHERE ctx.contextlevel = ? AND ctx.instanceid = ?",
            [CONTEXT_COURSE, $courseid],
        );
        $data['Question bank questions'] = $questions ? (int) $questions->count : 0;

        // --- Role capability overwrites ---
        $verbose->step('Counting role capability overwrites');
        $capOverwrites = 0;
        foreach ($dbcontexts as $dbcontext) {
            $capOverwrites += $DB->count_records('role_capabilities', ['contextid' => $dbcontext->id]);
        }
        $data['Role capability overwrites'] = $capOverwrites;

        // --- Local role assignments ---
        $verbose->step('Counting local role assignments');
        $roleAssignments = 0;
        foreach ($dbcontexts as $dbcontext) {
            $roleAssignments += $DB->count_records('role_assignments', ['contextid' => $dbcontext->id]);
        }
        $data['Local role assignments'] = $roleAssignments;

        // --- Enrolled users ---
        $verbose->step('Counting enrolled users');
        $enrolled = $DB->get_record_sql(
            "SELECT COUNT(DISTINCT userid) AS c FROM {role_assignments} WHERE contextid = ?",
            [$coursecontext->id],
        );
        $data['Enrolled users'] = (int) $enrolled->c;

        $usersByRole = $DB->get_records_sql(
            "SELECT roleid, COUNT(*) AS c FROM {role_assignments} WHERE contextid = ? GROUP BY roleid",
            [$coursecontext->id],
        );
        $roleNames = $DB->get_records_menu('role', null, '', 'id, shortname');
        foreach ($usersByRole as $u) {
            if ($u->c > 0) {
                $roleName = $roleNames[$u->roleid] ?? "role-{$u->roleid}";
                $data["Users in role $roleName"] = (int) $u->c;
            }
        }

        // --- Groups ---
        $verbose->step('Counting groups');
        $groups = $DB->get_records_sql(
            "SELECT g.id, COUNT(m.id) AS c
               FROM {groups} g
               LEFT JOIN {groups_members} m ON g.id = m.groupid
              WHERE g.courseid = ?
              GROUP BY g.id",
            [$courseid],
        );
        $groupCount = count($groups);
        $data['Number of groups'] = $groupCount;

        if ($groupCount > 0) {
            $groupSizes = array_column((array) $groups, 'c');
            $data['Group members min'] = (int) min($groupSizes);
            $data['Group members max'] = (int) max($groupSizes);
            $data['Group members avg'] = (int) (array_sum($groupSizes) / $groupCount);
        } else {
            $data['Group members min'] = 0;
            $data['Group members max'] = 0;
            $data['Group members avg'] = 0;
        }

        // --- Modinfo size ---
        $verbose->step('Calculating modinfo size');
        $modinfo = get_fast_modinfo($course);
        $data['Modinfo size (bytes)'] = strlen(serialize($modinfo));

        // --- Sections ---
        $verbose->step('Counting sections');
        $sections = $DB->get_records('course_sections', ['course' => $courseid]);
        $sectionCount = count($sections);
        $data['Number of sections'] = $sectionCount;

        $sectionsVisible = 0;
        $sectionsMin = null;
        $sectionsMax = 0;
        $modsTotal = 0;

        foreach ($sections as $section) {
            $sectionsVisible += $section->visible;
            $mods = !$section->sequence ? 0 : substr_count($section->sequence, ',') + 1;
            $modsTotal += $mods;
            if ($mods > $sectionsMax) {
                $sectionsMax = $mods;
            }
            if ($sectionsMin === null || $mods < $sectionsMin) {
                $sectionsMin = $mods;
            }
        }
        $data['Sections visible'] = $sectionsVisible;
        $data['Sections hidden'] = $sectionCount - $sectionsVisible;
        $data['Section modules min'] = $sectionsMin ?? 0;
        $data['Section modules max'] = $sectionsMax;
        $data['Section modules avg'] = $sectionCount > 0 ? (int) ($modsTotal / $sectionCount) : 0;

        // --- Grades ---
        $verbose->step('Counting grades');
        $grades = $DB->get_record_sql(
            "SELECT COUNT(*) AS c FROM {grade_items} i JOIN {grade_grades} g ON i.id = g.itemid WHERE i.courseid = ?",
            [$courseid],
        );
        $data['Number of grades'] = (int) $grades->c;

        // --- Badges ---
        $verbose->step('Counting badges');
        $badges = $DB->get_record('badge', ['courseid' => $courseid], 'COUNT(*) AS c');
        $data['Number of badges'] = (int) $badges->c;

        // --- Log entries ---
        $verbose->step('Counting log entries');
        $logs = $DB->get_record('logstore_standard_log', ['courseid' => $courseid], 'COUNT(*) AS c');
        $data['Number of log entries'] = (int) $logs->c;

        // --- Files ---
        $verbose->step('Counting files');
        $files = $DB->get_record_sql(
            "SELECT COUNT(*) AS c FROM {files} WHERE filename <> '.' AND contextid IN (SELECT id FROM {context} WHERE path LIKE ?)",
            [$coursecontext->path . '/%'],
        );
        $data['Number of files'] = (int) $files->c;

        $fileSize = $DB->get_record_sql(
            "SELECT SUM(filesize) AS s FROM {files} WHERE filename <> '.' AND contextid IN (SELECT id FROM {context} WHERE path LIKE ?)",
            [$coursecontext->path . '/%'],
        );
        $data['Total file size (bytes)'] = (int) ($fileSize->s ?? 0);

        // --- Cache build time (only in --run mode) ---
        if ($runMode) {
            $verbose->step('Rebuilding course cache (--run mode)');
            $start = microtime(true);
            rebuild_course_cache($courseid);
            $data['Cache build time (s)'] = round(microtime(true) - $start, 4);
        } else {
            $verbose->info('Skipping cache rebuild (use --run to enable)');
        }

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
