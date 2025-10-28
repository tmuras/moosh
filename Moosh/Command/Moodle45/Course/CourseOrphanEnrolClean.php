<?php
/**
 * Clean orphaned enrolments for a non-existing course
 * Runs ONLY if the course record is missing.
 *
 * Usage:
 * @example moosh course-orphan-enrol-clean <courseid>
 *
 * Notes:
 * - Uses Moodle enrol API (enrol_course_delete) to allow plugins to clean their own data.
 * - Unassigns roles in the course context if the context still exists.
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @introduced 2025-01-19
 * @author     Ewa Soroka
 */

namespace Moosh\Command\Moodle45\Course;

use Moosh\MooshCommand;

class CourseOrphanEnrolClean extends MooshCommand {
    public function __construct() {
        parent::__construct('orphan-enrol-clean', 'course');

        $this->addArgument('courseid');

        $this->maxArguments = 255;
    }

    public function execute() {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/enrol/locallib.php');

        $courseid = (int)$this->arguments[0];
        if ($courseid == 0) {
            cli_error("Invalid courseid.");
        }

        // this is only for orphan records: the course must NOT exist.
        if ($DB->record_exists('course', ['id' => $courseid])) {
            cli_error("Course id {$courseid} still exists.");
        }

        // Context may still exist even if the course record is gone.
        $context   = \context_course::instance($courseid, IGNORE_MISSING);
        $contextid = $context ? $context->id : null;

        // Collect enrol instances and related user_enrolments.
        $enrols = $DB->get_records('enrol', ['courseid' => $courseid], 'id ASC', 'id, enrol, courseid');
        $enrolids = array_keys($enrols);

        $uecount = 0;
        if ($enrolids) {
            list($inSql, $inParams) = $DB->get_in_or_equal($enrolids, SQL_PARAMS_NAMED, 'enrolids');
            $uecount = $DB->count_records_select('user_enrolments', "enrolid $inSql", $inParams);
        }

        $racount = 0;
        if ($contextid) {
            $racount = $DB->count_records('role_assignments', ['contextid' => $contextid]);
        }

        cli_writeln("Orphan enrolment cleanup for courseid={$courseid}");
        cli_writeln(str_repeat('-', 60));
        cli_writeln("Course record:           missing (OK)");
        cli_writeln(sprintf("Course context:          %s", $contextid ? "present (id={$contextid})" : "missing"));
        cli_writeln(sprintf("Enrol instances:         %d", count($enrolids)));
        cli_writeln(sprintf("User enrolments:         %d", $uecount));
        cli_writeln(sprintf("Role assignments:        %d", $racount));
        cli_writeln(str_repeat('-', 60));

        if (!$enrolids && !$contextid) {
            cli_writeln("Nothing to clean. Exiting.");
            return 1;
        }

        // Step 1: use the enrol API to delete enrol instances
        if ($enrolids) {
            cli_writeln("Step 1: delete enrol instances (API, then low-level cleanup)");

            foreach ($enrols as $instance) {
                $plugin = enrol_get_plugin($instance->enrol);
                if (!$plugin) {
                    cli_writeln("  WARN: enrol plugin '{$instance->enrol}' not available, skipping instance id={$instance->id}");
                    continue;
                }
                cli_writeln("  -> delete_instance enrol#{$instance->id} ({$instance->enrol})");
                // 1) API attempt (may throw if plugin expects existing course)
                try {
                    $plugin->delete_instance($instance);
                    cli_writeln("     OK (API)");
                } catch (\Throwable $e) {
                    cli_writeln("     API failed: " . $e->getMessage());
                }
                // 2) Low-level cleanup (idempotent; ensures the instance is gone even if API failed early)
                try {
                    $tx = $DB->start_delegated_transaction();
                    // remove role_assignments created by this enrol instance (if any)
                    $DB->delete_records('role_assignments', [
                        'itemid'    => $instance->id,
                        'component' => 'enrol_' . $instance->enrol
                    ]);
                    // remove user enrolments for this instance
                    $DB->delete_records('user_enrolments', ['enrolid' => $instance->id]);
                    // remove the instance itself
                    $DB->delete_records('enrol', ['id' => $instance->id]);
                    $tx->allow_commit();
                    cli_writeln(str_repeat('-', 60));
                    cli_writeln("     OK (low-level cleanup)");
                } catch (\Throwable $sqlerr) {
                    cli_writeln("     FAIL (low-level cleanup): " . $sqlerr->getMessage() . " (continuing)");
                }
            }
        } else {
            cli_writeln("Step 1: skipped (no enrol instances).");
        }

        // Step 2: unassign roles in the leftover course context (if any).
        if ($contextid) {
            cli_writeln("Step 2: role_unassign_all(contextid={$contextid})");
            try {
                role_unassign_all(['contextid' => $contextid, 'component' => ''], true);
                cli_writeln("  OK: role assignments removed.");
            } catch (\Throwable $e) {
                cli_writeln("  WARN: role_unassign_all: " . $e->getMessage());
            }
        } else {
            cli_writeln("Step 2: skipped (no course context).");
        }

        return 0;
    }
}
