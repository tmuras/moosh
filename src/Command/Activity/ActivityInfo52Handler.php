<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Activity;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ActivityInfo52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'cmid',
            InputArgument::REQUIRED,
            'Course module ID',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $cmid = (int) $input->getArgument('cmid');

        require_once $CFG->dirroot . '/course/lib.php';
        require_once $CFG->libdir . '/gradelib.php';
        require_once $CFG->libdir . '/completionlib.php';

        // Load course module
        $verbose->step('Loading course module');
        $cm = get_coursemodule_from_id('', $cmid);
        if (!$cm) {
            $output->writeln("<error>Course module with ID $cmid not found.</error>");
            return Command::FAILURE;
        }

        $course = get_course($cm->course);
        $modContext = \context_module::instance($cmid);
        $moduleRecord = $DB->get_record('modules', ['id' => $cm->module]);

        $data = [];

        // --- Basic info ---
        $verbose->step('Collecting basic information');
        $data['Course module ID'] = $cm->id;
        $data['Module type'] = $moduleRecord->name;
        $data['Instance ID'] = $cm->instance;
        $data['Name'] = $cm->name;
        $data['ID number'] = $cm->idnumber ?: '(none)';

        // Course info
        $data['Course ID'] = $course->id;
        $data['Course shortname'] = $course->shortname;

        // Section
        $section = $DB->get_record('course_sections', ['id' => $cm->section]);
        $data['Section number'] = $section ? $section->section : 'unknown';
        $data['Section name'] = $section && !empty($section->name) ? $section->name : '(default)';

        // Visibility
        $data['Visible'] = $cm->visible ? 'yes' : 'no';
        $data['Visible on course page'] = $cm->visibleoncoursepage ? 'yes' : 'no';

        // Availability
        $data['Availability restriction'] = !empty($cm->availability) ? $cm->availability : '(none)';

        // Grouping
        $data['Group mode'] = match ((int) $cm->groupmode) {
            0 => 'no groups',
            1 => 'separate groups',
            2 => 'visible groups',
            default => (string) $cm->groupmode,
        };
        if ($cm->groupingid > 0) {
            $grouping = $DB->get_record('groupings', ['id' => $cm->groupingid]);
            $data['Grouping'] = $grouping ? $grouping->name : "ID {$cm->groupingid}";
        } else {
            $data['Grouping'] = '(none)';
        }

        // Dates
        $data['Added'] = $cm->added ? userdate($cm->added) : 'unknown';

        // --- Module-specific instance data ---
        $verbose->step('Loading module instance');
        $instance = $DB->get_record($moduleRecord->name, ['id' => $cm->instance]);
        if ($instance) {
            if (isset($instance->intro) && !empty($instance->intro)) {
                $introLen = mb_strlen(strip_tags($instance->intro));
                $data['Introduction length'] = "$introLen chars";
            }
            if (isset($instance->timemodified) && $instance->timemodified) {
                $data['Last modified'] = userdate($instance->timemodified);
            }
            if (isset($instance->duedate) && $instance->duedate) {
                $data['Due date'] = userdate($instance->duedate);
            }
            if (isset($instance->cutoffdate) && $instance->cutoffdate) {
                $data['Cut-off date'] = userdate($instance->cutoffdate);
            }
            if (isset($instance->allowsubmissionsfromdate) && $instance->allowsubmissionsfromdate) {
                $data['Submissions open'] = userdate($instance->allowsubmissionsfromdate);
            }
            if (isset($instance->timeopen) && $instance->timeopen) {
                $data['Open time'] = userdate($instance->timeopen);
            }
            if (isset($instance->timeclose) && $instance->timeclose) {
                $data['Close time'] = userdate($instance->timeclose);
            }
        }

        // --- Completion ---
        $verbose->step('Checking completion');
        $data['Completion tracking'] = match ((int) $cm->completion) {
            0 => 'none',
            1 => 'manual',
            2 => 'automatic',
            default => (string) $cm->completion,
        };

        if ($cm->completion > 0) {
            $completions = $DB->get_records_sql(
                "SELECT completionstate, COUNT(*) AS c
                   FROM {course_modules_completion}
                  WHERE coursemoduleid = ?
                  GROUP BY completionstate",
                [$cmid],
            );
            $total = 0;
            $completed = 0;
            foreach ($completions as $comp) {
                $total += (int) $comp->c;
                if ((int) $comp->completionstate > 0) {
                    $completed += (int) $comp->c;
                }
            }
            $data['Completion records'] = $total;
            $data['Completed'] = $completed;
            $data['Not completed'] = $total - $completed;

            if ($cm->completionexpected) {
                $data['Completion expected by'] = userdate($cm->completionexpected);
            }
        }

        // --- Grades ---
        $verbose->step('Checking grades');
        $gradeItems = $DB->get_records('grade_items', [
            'itemtype' => 'mod',
            'itemmodule' => $moduleRecord->name,
            'iteminstance' => $cm->instance,
            'courseid' => $course->id,
        ]);

        if ($gradeItems) {
            foreach ($gradeItems as $gi) {
                $data['Grade item'] = $gi->itemname ?: '(unnamed)';
                $data['Grade type'] = match ((int) $gi->gradetype) {
                    0 => 'none',
                    1 => 'value',
                    2 => 'scale',
                    3 => 'text',
                    default => (string) $gi->gradetype,
                };
                if ((int) $gi->gradetype === 1) {
                    $data['Grade max'] = $gi->grademax;
                    $data['Grade min'] = $gi->grademin;
                }
                $gradeCount = $DB->count_records_sql(
                    "SELECT COUNT(*) FROM {grade_grades} WHERE itemid = ? AND finalgrade IS NOT NULL",
                    [$gi->id],
                );
                $data['Grades recorded'] = $gradeCount;

                if ($gradeCount > 0) {
                    $stats = $DB->get_record_sql(
                        "SELECT AVG(finalgrade) AS avg, MIN(finalgrade) AS min, MAX(finalgrade) AS max
                           FROM {grade_grades}
                          WHERE itemid = ? AND finalgrade IS NOT NULL",
                        [$gi->id],
                    );
                    $data['Grade average'] = round($stats->avg, 2);
                    $data['Grade min value'] = round($stats->min, 2);
                    $data['Grade max value'] = round($stats->max, 2);
                }
                break; // Show first grade item only
            }
        } else {
            $data['Grade item'] = '(none)';
        }

        // --- Log entries ---
        $verbose->step('Counting log entries');
        $logCount = $DB->count_records('logstore_standard_log', ['contextid' => $modContext->id]);
        $data['Log entries'] = $logCount;

        if ($logCount > 0) {
            $lastLog = $DB->get_record_sql(
                "SELECT MAX(timecreated) AS t FROM {logstore_standard_log} WHERE contextid = ?",
                [$modContext->id],
            );
            $data['Last accessed'] = $lastLog ? userdate($lastLog->t) : 'unknown';

            $distinctUsers = $DB->count_records_sql(
                "SELECT COUNT(DISTINCT userid) FROM {logstore_standard_log} WHERE contextid = ? AND userid > 0",
                [$modContext->id],
            );
            $data['Unique users (logs)'] = $distinctUsers;
        }

        // --- Files ---
        $verbose->step('Counting files');
        $fileStats = $DB->get_record_sql(
            "SELECT COUNT(*) AS c, COALESCE(SUM(filesize), 0) AS s
               FROM {files}
              WHERE contextid = ? AND filename <> '.'",
            [$modContext->id],
        );
        $data['Files'] = (int) $fileStats->c;
        $data['Total file size'] = $this->formatBytes((int) $fileStats->s);

        // --- Context info ---
        $data['Context ID'] = $modContext->id;
        $data['Context path'] = $modContext->path;

        // Role overrides in this context
        $overrides = $DB->count_records('role_capabilities', ['contextid' => $modContext->id]);
        $data['Role capability overrides'] = $overrides;

        // --- Render ---
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
            $formatter->display(array_keys($data), [array_values($data)]);
        }

        return Command::SUCCESS;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = (int) floor(log($bytes, 1024));
        return round($bytes / (1024 ** $i), 1) . ' ' . $units[$i];
    }
}
