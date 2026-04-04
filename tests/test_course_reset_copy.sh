#!/usr/bin/env bash
#
# Integration test for moosh2 course:reset, course:copy
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_course_reset_copy.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 course:reset/copy integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ═══════════════════════════════════════════════════════════════════
# course:reset
# ═══════════════════════════════════════════════════════════════════

echo "========== course:reset =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH course:reset -p "$MOODLE_PATH" 2 2>&1)
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows course" "algebrafundamentals" "$OUT"
assert_output_contains "Shows reset_events" "reset_events" "$OUT"
assert_output_contains "Shows reset_gradebook" "reset_gradebook_grades" "$OUT"
echo ""

echo "--- Test: Reset with --run ---"
OUT=$($PHP $MOOSH course:reset -p "$MOODLE_PATH" --run 2 2>&1 | tail -3)
assert_output_contains "Shows reset complete" "has been reset" "$OUT"
echo ""

echo "--- Test: Custom settings ---"
OUT=$($PHP $MOOSH course:reset -p "$MOODLE_PATH" -s "reset_events=0 reset_notes=0" 2 2>&1)
assert_output_contains "Shows events=0" "reset_events = 0" "$OUT"
assert_output_contains "Shows notes=0" "reset_notes = 0" "$OUT"
echo ""

echo "--- Test: Nonexistent course ---"
OUT=$($PHP $MOOSH course:reset -p "$MOODLE_PATH" 99999 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for bad course" 1 "$EXIT_CODE"
assert_output_contains "Not found" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH course:reset -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Reset course data" "$OUT"
assert_output_contains "Help shows --settings" "--settings" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
# course:copy
# ═══════════════════════════════════════════════════════════════════

echo "========== course:copy =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH course:copy -p "$MOODLE_PATH" 2 "Test Copy" test_copy_course 2 2>&1)
echo "$OUT"
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows source" "algebrafundamentals" "$OUT"
assert_output_contains "Shows new name" "Test Copy" "$OUT"
assert_output_contains "Shows new shortname" "test_copy_course" "$OUT"
echo ""

echo "--- Test: Copy with --run ---"
OUT=$($PHP $MOOSH course:copy -p "$MOODLE_PATH" --run 2 "Test Copy" test_copy_course 2 2>&1)
echo "$OUT"
assert_output_contains "Shows queued" "task queued" "$OUT"
assert_output_contains "Shows shortnames" "test_copy_course" "$OUT"
echo ""

echo "--- Test: Duplicate shortname ---"
OUT=$($PHP $MOOSH course:copy -p "$MOODLE_PATH" 2 "Another Copy" algebrafundamentals_2 2 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for duplicate shortname" 1 "$EXIT_CODE"
assert_output_contains "Shortname taken" "already exists" "$OUT"
echo ""

echo "--- Test: Nonexistent category ---"
OUT=$($PHP $MOOSH course:copy -p "$MOODLE_PATH" 2 "Copy" unique_sn_123 99999 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for bad category" 1 "$EXIT_CODE"
assert_output_contains "Category not found" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH course:copy -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Copy a course" "$OUT"
assert_output_contains "Help shows --userdata" "--userdata" "$OUT"
echo ""


print_summary
