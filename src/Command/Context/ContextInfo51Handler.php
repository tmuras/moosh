<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Context;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * context:info implementation for Moodle 5.1.
 */
class ContextInfo51Handler extends BaseHandler
{
    private const LEVEL_NAMES = [
        10 => 'System',
        30 => 'User',
        40 => 'Course category',
        50 => 'Course',
        70 => 'Module',
        80 => 'Block',
    ];

    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'contextid',
            InputArgument::REQUIRED,
            'The ID of the context to inspect (from mdl_context.id)',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $contextId = (int) $input->getArgument('contextid');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/accesslib.php';

        $dbContext = $DB->get_record('context', ['id' => $contextId]);
        if (!$dbContext) {
            $output->writeln("<error>Context with ID $contextId not found.</error>");
            return Command::FAILURE;
        }

        $context = \context::instance_by_id($contextId, MUST_EXIST);

        $data = [];

        // --- Basic context info ---
        $verbose->step('Collecting context information');
        $data['Context ID'] = $context->id;
        $data['Context level'] = $context->contextlevel;
        $data['Context level name'] = self::LEVEL_NAMES[$context->contextlevel] ?? "Unknown ({$context->contextlevel})";
        $data['Context name'] = $context->get_context_name();
        $data['Instance ID'] = $context->instanceid;
        $data['Depth'] = $context->depth;
        $data['Path'] = $context->path;

        // Resolve path to names.
        $pathIds = array_filter(array_map('intval', explode('/', $context->path)));
        $pathNames = [];
        foreach ($pathIds as $id) {
            $pathCtx = \context::instance_by_id($id, IGNORE_MISSING);
            $pathNames[] = $pathCtx ? $pathCtx->get_context_name() : "(ID $id)";
        }
        $data['Path names'] = implode(' / ', $pathNames);

        // URL.
        try {
            $url = $context->get_url();
            $data['URL'] = $url ? $url->out(false) : '';
        } catch (\Throwable $e) {
            $data['URL'] = '';
        }

        // --- Type-specific details ---
        $verbose->step('Collecting type-specific details');
        $this->collectTypeSpecificData($data, $context, $verbose);

        // --- Role assignments ---
        $verbose->step('Counting role assignments');
        $totalRoleAssignments = $DB->count_records('role_assignments', ['contextid' => $contextId]);
        $data['Role assignments'] = $totalRoleAssignments;

        if ($totalRoleAssignments > 0) {
            $roleNames = $DB->get_records_menu('role', null, '', 'id, shortname');
            $roleBreakdown = $DB->get_records_sql(
                "SELECT roleid, COUNT(*) AS c FROM {role_assignments} WHERE contextid = ? GROUP BY roleid ORDER BY c DESC",
                [$contextId],
            );
            foreach ($roleBreakdown as $ra) {
                $roleName = $roleNames[$ra->roleid] ?? "role-{$ra->roleid}";
                $data["Assigned as $roleName"] = (int) $ra->c;
            }
        }

        // --- Role capability overrides ---
        $verbose->step('Counting capability overrides');
        $capOverrides = $DB->count_records('role_capabilities', ['contextid' => $contextId]);
        $data['Capability overrides'] = $capOverrides;

        if ($capOverrides > 0) {
            $roleNames = $roleNames ?? $DB->get_records_menu('role', null, '', 'id, shortname');
            $capByRole = $DB->get_records_sql(
                "SELECT roleid, COUNT(*) AS c FROM {role_capabilities} WHERE contextid = ? GROUP BY roleid ORDER BY c DESC",
                [$contextId],
            );
            foreach ($capByRole as $cr) {
                $roleName = $roleNames[$cr->roleid] ?? "role-{$cr->roleid}";
                $data["Overrides for $roleName"] = (int) $cr->c;
            }
        }

        // --- Child contexts ---
        $verbose->step('Counting child contexts');
        $totalChildren = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {context} WHERE path LIKE ?",
            [$context->path . '/%'],
        );
        $data['Child contexts'] = $totalChildren;

        if ($totalChildren > 0) {
            $childrenByLevel = $DB->get_records_sql(
                "SELECT contextlevel, COUNT(*) AS c FROM {context} WHERE path LIKE ? GROUP BY contextlevel ORDER BY contextlevel",
                [$context->path . '/%'],
            );
            foreach ($childrenByLevel as $cl) {
                $levelName = self::LEVEL_NAMES[$cl->contextlevel] ?? "Level {$cl->contextlevel}";
                $data["Children at $levelName level"] = (int) $cl->c;
            }
        }

        // --- Files ---
        $verbose->step('Counting files in this context');
        $fileCount = $DB->get_record_sql(
            "SELECT COUNT(*) AS c FROM {files} WHERE contextid = ? AND filename <> '.'",
            [$contextId],
        );
        $data['Files in context'] = (int) $fileCount->c;

        $fileSize = $DB->get_record_sql(
            "SELECT COALESCE(SUM(filesize), 0) AS s FROM {files} WHERE contextid = ? AND filename <> '.'",
            [$contextId],
        );
        $data['File size (bytes)'] = (int) $fileSize->s;

        // Files in descendant contexts.
        if ($totalChildren > 0) {
            $descendantFiles = $DB->get_record_sql(
                "SELECT COUNT(*) AS c FROM {files} f
                   JOIN {context} ctx ON ctx.id = f.contextid
                  WHERE f.filename <> '.' AND ctx.path LIKE ?",
                [$context->path . '/%'],
            );
            $data['Files in descendants'] = (int) $descendantFiles->c;

            $descendantFileSize = $DB->get_record_sql(
                "SELECT COALESCE(SUM(f.filesize), 0) AS s FROM {files} f
                   JOIN {context} ctx ON ctx.id = f.contextid
                  WHERE f.filename <> '.' AND ctx.path LIKE ?",
                [$context->path . '/%'],
            );
            $data['Descendant file size (bytes)'] = (int) $descendantFileSize->s;
        }

        // --- Log entries ---
        $verbose->step('Counting log entries');
        $logs = $DB->get_record_sql(
            "SELECT COUNT(*) AS c FROM {logstore_standard_log} WHERE contextid = ?",
            [$contextId],
        );
        $data['Log entries'] = (int) $logs->c;

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

    /**
     * Collect type-specific data depending on the context level.
     */
    private function collectTypeSpecificData(array &$data, \context $context, VerboseLogger $verbose): void
    {
        global $DB;

        match ($context->contextlevel) {
            CONTEXT_SYSTEM => $this->collectSystemData($data, $context, $verbose),
            CONTEXT_USER => $this->collectUserData($data, $context, $verbose),
            CONTEXT_COURSECAT => $this->collectCategoryData($data, $context, $verbose),
            CONTEXT_COURSE => $this->collectCourseData($data, $context, $verbose),
            CONTEXT_MODULE => $this->collectModuleData($data, $context, $verbose),
            CONTEXT_BLOCK => $this->collectBlockData($data, $context, $verbose),
            default => null,
        };
    }

    private function collectSystemData(array &$data, \context $context, VerboseLogger $verbose): void
    {
        global $DB;

        $verbose->info('System context — collecting global stats');
        $data['Total users'] = $DB->count_records('user', ['deleted' => 0]);
        $data['Total courses'] = $DB->count_records('course') - 1; // Exclude site course.
        $data['Total categories'] = $DB->count_records('course_categories');
    }

    private function collectUserData(array &$data, \context $context, VerboseLogger $verbose): void
    {
        global $DB;

        $user = $DB->get_record('user', ['id' => $context->instanceid]);
        if ($user) {
            $data['Username'] = $user->username;
            $data['Full name'] = trim($user->firstname . ' ' . $user->lastname);
            $data['Email'] = $user->email;
            $data['Suspended'] = (int) $user->suspended;
            $data['Last access'] = $user->lastaccess ? date('Y-m-d H:i:s', $user->lastaccess) : 'never';
        }
    }

    private function collectCategoryData(array &$data, \context $context, VerboseLogger $verbose): void
    {
        global $DB;

        $cat = $DB->get_record('course_categories', ['id' => $context->instanceid]);
        if ($cat) {
            $data['Category name'] = $cat->name;
            $data['Category visible'] = (int) $cat->visible;
            $data['Category course count'] = (int) $cat->coursecount;
            $data['Category depth'] = (int) $cat->depth;
        }
    }

    private function collectCourseData(array &$data, \context $context, VerboseLogger $verbose): void
    {
        global $DB;

        $course = $DB->get_record('course', ['id' => $context->instanceid]);
        if ($course) {
            $data['Course shortname'] = $course->shortname;
            $data['Course fullname'] = $course->fullname;
            $data['Course visible'] = (int) $course->visible;

            $enrolled = $DB->get_record_sql(
                "SELECT COUNT(DISTINCT ue.userid) AS c
                   FROM {user_enrolments} ue
                   JOIN {enrol} e ON e.id = ue.enrolid
                  WHERE e.courseid = ?",
                [$course->id],
            );
            $data['Enrolled users'] = (int) $enrolled->c;

            $modules = $DB->count_records('course_modules', ['course' => $course->id]);
            $data['Course modules'] = $modules;

            $sections = $DB->count_records('course_sections', ['course' => $course->id]);
            $data['Course sections'] = $sections;
        }
    }

    private function collectModuleData(array &$data, \context $context, VerboseLogger $verbose): void
    {
        global $DB;

        $cm = $DB->get_record('course_modules', ['id' => $context->instanceid]);
        if ($cm) {
            $module = $DB->get_record('modules', ['id' => $cm->module]);
            $data['Module type'] = $module ? $module->name : "module-{$cm->module}";

            // Get the activity name from the module's own table.
            if ($module) {
                $activity = $DB->get_record($module->name, ['id' => $cm->instance], 'name');
                if ($activity) {
                    $data['Activity name'] = $activity->name;
                }
            }

            $data['Course ID'] = (int) $cm->course;
            $data['Section ID'] = (int) $cm->section;
            $data['Module visible'] = (int) $cm->visible;
            $data['Added on'] = date('Y-m-d H:i:s', $cm->added);

            // Completion data.
            if ($cm->completion > 0) {
                $data['Completion tracking'] = match ((int) $cm->completion) {
                    1 => 'Manual',
                    2 => 'Automatic',
                    default => (string) $cm->completion,
                };
                $completions = $DB->count_records('course_modules_completion', ['coursemoduleid' => $cm->id]);
                $data['Completion records'] = $completions;
            } else {
                $data['Completion tracking'] = 'None';
            }

            // Grades for this module.
            $grades = $DB->get_record_sql(
                "SELECT COUNT(*) AS c FROM {grade_items} gi
                   JOIN {grade_grades} gg ON gg.itemid = gi.id
                  WHERE gi.itemmodule = ? AND gi.iteminstance = ? AND gg.finalgrade IS NOT NULL",
                [$module ? $module->name : '', $cm->instance],
            );
            $data['Grade records'] = (int) $grades->c;
        }
    }

    private function collectBlockData(array &$data, \context $context, VerboseLogger $verbose): void
    {
        global $DB;

        $block = $DB->get_record('block_instances', ['id' => $context->instanceid]);
        if ($block) {
            $data['Block type'] = $block->blockname;
            $data['Parent context ID'] = (int) $block->parentcontextid;
            $data['Block visible'] = (int) ($block->visible ?? 1);
            $data['Block region'] = $block->defaultregion;
            $data['Block weight'] = (int) $block->defaultweight;

            // Resolve parent context name.
            $parentCtx = \context::instance_by_id($block->parentcontextid, IGNORE_MISSING);
            if ($parentCtx) {
                $data['Parent context name'] = $parentCtx->get_context_name();
            }
        }
    }
}
