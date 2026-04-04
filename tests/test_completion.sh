#!/usr/bin/env bash
#
# Integration tests for moosh2 completion commands
#
# Usage: bash tests/test_completion.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 completion commands integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Enable completion for course 2
$PHP $MOOSH sql:run -p "$MOODLE_PATH" --run "UPDATE mdl_course SET enablecompletion = 1 WHERE id = 2" > /dev/null 2>&1

# Get a student user ID
STUDENT_ID=$($PHP $MOOSH sql:select -p "$MOODLE_PATH" "SELECT id FROM mdl_user WHERE username='student01'" -o csv 2>&1 | tail -1)
echo "  Student ID: $STUDENT_ID"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  completion:status
# ═══════════════════════════════════════════════════════════════════

echo "========== completion:status =========="
echo ""

echo "--- Test: Single user status ---"
OUT=$($PHP $MOOSH completion:status 2 --userid $STUDENT_ID -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Status exit code 0" 0 $EC
assert_output_contains "Shows completion info" "student01" "$OUT"
assert_output_contains "Shows complete status" "complete" "$OUT"
echo ""

echo "--- Test: All users ---"
OUT=$($PHP $MOOSH completion:status 2 --all -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "All users exit code 0" 0 $EC
assert_output_contains "Shows userid header" "userid" "$OUT"
echo ""

echo "--- Test: No args ---"
OUT=$($PHP $MOOSH completion:status 2 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for no args" 1 $EC
echo ""

echo "--- Test: Invalid course ---"
OUT=$($PHP $MOOSH completion:status 999 --userid $STUDENT_ID -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid course" 1 $EC
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH completion:status -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Show course completion status" "$OUT"
assert_output_contains "Help shows --userid" "--userid" "$OUT"
assert_output_contains "Help shows --all" "--all" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  completion:mark
# ═══════════════════════════════════════════════════════════════════

echo "========== completion:mark =========="
echo ""

echo "--- Test: Dry run course ---"
OUT=$($PHP $MOOSH completion:mark 2 --userid $STUDENT_ID -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Mark course complete ---"
OUT=$($PHP $MOOSH completion:mark 2 --userid $STUDENT_ID -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Mark exit code 0" 0 $EC
assert_output_contains "Shows marked" "Marked" "$OUT"
assert_output_contains "Shows complete" "complete" "$OUT"
echo ""

echo "--- Test: Mark course incomplete ---"
OUT=$($PHP $MOOSH completion:mark 2 --userid $STUDENT_ID --state incomplete -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Incomplete exit code 0" 0 $EC
assert_output_contains "Shows incomplete" "incomplete" "$OUT"
echo ""

echo "--- Test: No userid ---"
OUT=$($PHP $MOOSH completion:mark 2 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for no userid" 1 $EC
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH completion:mark -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Mark course or activity" "$OUT"
assert_output_contains "Help shows --cmid" "--cmid" "$OUT"
assert_output_contains "Help shows --state" "--state" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  completion:reset
# ═══════════════════════════════════════════════════════════════════

echo "========== completion:reset =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH completion:reset 2 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Reset course completions ---"
OUT=$($PHP $MOOSH completion:reset 2 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Reset exit code 0" 0 $EC
assert_output_contains "Shows reset" "Reset" "$OUT"
echo ""

echo "--- Test: Invalid course ---"
OUT=$($PHP $MOOSH completion:reset 999 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid course" 1 $EC
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH completion:reset -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Reset completion data" "$OUT"
assert_output_contains "Help shows --cmid" "--cmid" "$OUT"
echo ""


print_summary
