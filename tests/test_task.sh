#!/usr/bin/env bash
#
# Integration tests for moosh2 task commands
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_task.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 task commands integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Use a known task for testing
TASK_CLASS='\core\task\send_new_user_passwords_task'

# ═══════════════════════════════════════════════════════════════════
#  task:list
# ═══════════════════════════════════════════════════════════════════

echo "========== task:list =========="
echo ""

echo "--- Test: List all tasks ---"
OUT=$($PHP $MOOSH task:list -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "List exit code 0" 0 $EC
assert_output_contains "Shows classname header" "classname" "$OUT"
assert_output_contains "Shows schedule header" "schedule" "$OUT"
assert_output_contains "Shows a core task" "core" "$OUT"
echo ""

echo "--- Test: Filter by component ---"
OUT=$($PHP $MOOSH task:list --component moodle -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Shows moodle tasks" "moodle" "$OUT"
echo ""

echo "--- Test: Filter disabled ---"
OUT=$($PHP $MOOSH task:list --disabled -p "$MOODLE_PATH" 2>&1)
# All shown should have 'yes' in disabled column
assert_output_contains "Shows disabled tasks" "yes" "$OUT"
echo ""

echo "--- Test: Filter enabled ---"
OUT=$($PHP $MOOSH task:list --enabled -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Enabled filter exit code 0" 0 $EC
echo ""

echo "--- Test: Running filter (no running expected) ---"
OUT=$($PHP $MOOSH task:list --running -p "$MOODLE_PATH" 2>&1)
assert_output_contains "No running tasks" "No tasks found" "$OUT"
echo ""

echo "--- Test: ID-only ---"
OUT=$($PHP $MOOSH task:list --classname-only -p "$MOODLE_PATH" 2>&1)
assert_output_not_empty "ID-only not empty" "$OUT"
assert_output_contains "Shows task classname" "task" "$OUT"
echo ""

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH task:list -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "CSV header" "classname,component,schedule" "$OUT"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH task:list --component moodle -p "$MOODLE_PATH" -o json 2>&1)
assert_output_contains "JSON has component" '"component": "moodle"' "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH task:list -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "List scheduled tasks" "$OUT"
assert_output_contains "Help shows --running" "--running" "$OUT"
assert_output_contains "Help shows --failed" "--failed" "$OUT"
echo ""



# ═══════════════════════════════════════════════════════════════════
#  task:mod
# ═══════════════════════════════════════════════════════════════════

echo "========== task:mod =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH task:mod "$TASK_CLASS" --enabled 0 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Disable task ---"
OUT=$($PHP $MOOSH task:mod "$TASK_CLASS" --enabled 0 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Disable exit code 0" 0 $EC
assert_output_contains "Shows disabled" "yes" "$OUT"
echo ""

echo "--- Test: Enable task ---"
OUT=$($PHP $MOOSH task:mod "$TASK_CLASS" --enabled 1 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Enable exit code 0" 0 $EC
assert_output_contains "Shows enabled" "no" "$OUT"
echo ""

echo "--- Test: Change schedule ---"
OUT=$($PHP $MOOSH task:mod "$TASK_CLASS" --minute '*/10' --hour '2' -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Schedule change exit code 0" 0 $EC
assert_output_contains "Shows new schedule" "*/10 2" "$OUT"
echo ""

echo "--- Test: Reset to default ---"
OUT=$($PHP $MOOSH task:mod "$TASK_CLASS" --reset -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Reset exit code 0" 0 $EC
assert_output_contains "Shows reset" "Reset" "$OUT"
echo ""

echo "--- Test: Invalid task ---"
OUT=$($PHP $MOOSH task:mod '\nonexistent\task' --enabled 0 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid task" 1 $EC
assert_output_contains "Error for invalid task" "not found" "$OUT"
echo ""

echo "--- Test: No modification ---"
OUT=$($PHP $MOOSH task:mod "$TASK_CLASS" -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for no mod" 1 $EC
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH task:mod -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Modify a scheduled task" "$OUT"
assert_output_contains "Help shows --minute" "--minute" "$OUT"
assert_output_contains "Help shows --reset" "--reset" "$OUT"
assert_output_contains "Help shows --clear-fail" "--clear-fail" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  task:run
# ═══════════════════════════════════════════════════════════════════

echo "========== task:run =========="
echo ""

echo "--- Test: Run task ---"
OUT=$($PHP $MOOSH task:run "$TASK_CLASS" -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Run exit code 0" 0 $EC
assert_output_contains "Shows executing" "Executing" "$OUT"
assert_output_contains "Shows completed" "completed" "$OUT"
echo ""

echo "--- Test: Invalid task ---"
OUT=$($PHP $MOOSH task:run '\nonexistent\task' -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid task" 1 $EC
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH task:run -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Execute a scheduled task" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  task:adhoc
# ═══════════════════════════════════════════════════════════════════

echo "========== task:adhoc =========="
echo ""

echo "--- Test: List adhoc tasks ---"
OUT=$($PHP $MOOSH task:adhoc -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "List exit code 0" 0 $EC
# May have tasks or may say "No adhoc tasks found" - both valid
assert_output_not_empty "List not empty" "$OUT"
echo ""

echo "--- Test: Count ---"
OUT=$($PHP $MOOSH task:adhoc --count -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Count exit code 0" 0 $EC
assert_output_contains "Shows total" "Total adhoc tasks" "$OUT"
assert_output_contains "Shows pending" "Pending" "$OUT"
assert_output_contains "Shows running" "Running" "$OUT"
assert_output_contains "Shows failed" "Failed" "$OUT"
echo ""

echo "--- Test: Failed filter ---"
OUT=$($PHP $MOOSH task:adhoc --failed -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Failed filter exit code 0" 0 $EC
echo ""

echo "--- Test: Clean dry run ---"
OUT=$($PHP $MOOSH task:adhoc --clean -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Clean dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Clean ---"
OUT=$($PHP $MOOSH task:adhoc --clean -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Clean exit code 0" 0 $EC
assert_output_contains "Shows cleaned" "Cleaned" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH task:adhoc -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "List or manage adhoc tasks" "$OUT"
assert_output_contains "Help shows --failed" "--failed" "$OUT"
assert_output_contains "Help shows --execute" "--execute" "$OUT"
assert_output_contains "Help shows --clean" "--clean" "$OUT"
assert_output_contains "Help shows --count" "--count" "$OUT"
echo ""


print_summary
