#!/usr/bin/env bash
#
# Integration tests for moosh2 event commands:
#   event:list, event:fire, event:log
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_event.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 event commands integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  event:list
# ═══════════════════════════════════════════════════════════════════

echo "========== event:list =========="
echo ""

echo "--- Test: List all events ---"
OUT=$($PHP $MOOSH event:list -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "List exit code 0" 0 $EC
assert_output_contains "Shows classname header" "classname" "$OUT"
assert_output_contains "Shows a core event" "core" "$OUT"
echo ""

echo "--- Test: Filter by component ---"
OUT=$($PHP $MOOSH event:list --component core -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Shows core events" "core" "$OUT"
assert_output_contains "Shows course_viewed" "course_viewed" "$OUT"
echo ""

echo "--- Test: Filter by CRUD ---"
OUT=$($PHP $MOOSH event:list --crud c --component core -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Shows create events" "created" "$OUT"
echo ""

echo "--- Test: Search ---"
OUT=$($PHP $MOOSH event:list --search user_loggedin -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Shows login event" "loggedin" "$OUT"
echo ""

echo "--- Test: ID-only ---"
OUT=$($PHP $MOOSH event:list --search course_viewed --classname-only -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Shows classname" "core" "$OUT"
assert_output_not_contains "No table" "component" "$OUT"
echo ""

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH event:list --component core --crud r -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "CSV header" "classname,component,action" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH event:list -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "List all available Moodle events" "$OUT"
assert_output_contains "Help shows --component" "--component" "$OUT"
assert_output_contains "Help shows --crud" "--crud" "$OUT"
assert_output_contains "Help shows --search" "--search" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  event:fire
# ═══════════════════════════════════════════════════════════════════

echo "========== event:fire =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH event:fire '\core\event\course_viewed' --courseid 2 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Fire course_viewed ---"
OUT=$($PHP $MOOSH event:fire '\core\event\course_viewed' --courseid 2 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Fire exit code 0" 0 $EC
assert_output_contains "Shows fired" "Fired" "$OUT"
echo ""

echo "--- Test: Fire with different course ---"
OUT=$($PHP $MOOSH event:fire '\core\event\course_viewed' --courseid 3 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Different course exit code 0" 0 $EC
assert_output_contains "Shows fired" "Fired" "$OUT"
echo ""

echo "--- Test: Invalid event class ---"
OUT=$($PHP $MOOSH event:fire '\nonexistent\event\fake' -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid event" 1 $EC
assert_output_contains "Error for invalid event" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH event:fire -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Trigger a Moodle event" "$OUT"
assert_output_contains "Help shows --data" "--data" "$OUT"
assert_output_contains "Help shows --courseid" "--courseid" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  event:log
# ═══════════════════════════════════════════════════════════════════

echo "========== event:log =========="
echo ""

echo "--- Test: List recent log entries ---"
OUT=$($PHP $MOOSH event:log --limit 5 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Log exit code 0" 0 $EC
assert_output_contains "Shows time column" "time" "$OUT"
assert_output_contains "Shows eventname" "eventname" "$OUT"
echo ""

echo "--- Test: Filter by component ---"
OUT=$($PHP $MOOSH event:log --component core --limit 5 -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Shows core events" "core" "$OUT"
echo ""

echo "--- Test: Filter by action ---"
OUT=$($PHP $MOOSH event:log --action loggedin --limit 5 -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Shows login events" "loggedin" "$OUT"
echo ""

echo "--- Test: Filter by courseid ---"
OUT=$($PHP $MOOSH event:log --courseid 2 --limit 5 -p "$MOODLE_PATH" 2>&1)
# May have entries or not, both valid
assert_output_not_empty "Courseid filter not empty" "$OUT"
echo ""

echo "--- Test: Filter by since ---"
OUT=$($PHP $MOOSH event:log --since "1 hour ago" --limit 5 -p "$MOODLE_PATH" 2>&1)
assert_output_not_empty "Since filter not empty" "$OUT"
echo ""

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH event:log --limit 3 -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "CSV header" "id,time,userid,eventname" "$OUT"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH event:log --action loggedin --limit 1 -p "$MOODLE_PATH" -o json 2>&1)
assert_output_contains "JSON has eventname" '"eventname"' "$OUT"
echo ""

echo "--- Test: ID-only ---"
OUT=$($PHP $MOOSH event:log --limit 3 --id-only -p "$MOODLE_PATH" 2>&1)
assert_output_not_empty "ID-only not empty" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH event:log -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Query the event log" "$OUT"
assert_output_contains "Help shows --userid" "--userid" "$OUT"
assert_output_contains "Help shows --since" "--since" "$OUT"
assert_output_contains "Help shows --eventname" "--eventname" "$OUT"
echo ""


print_summary
