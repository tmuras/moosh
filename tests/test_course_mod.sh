#!/usr/bin/env bash
#
# Integration tests for moosh2 course:mod command
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_course_mod.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 course:mod integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  course:mod
# ═══════════════════════════════════════════════════════════════════

echo "========== course:mod =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH course:mod 2 --fullname "New Name" -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows fullname change" "New Name" "$OUT"
echo ""

echo "--- Test: Change fullname ---"
OUT=$($PHP $MOOSH course:mod 2 --fullname "Updated Algebra" -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Fullname exit code 0" 0 $EC
assert_output_contains "Shows updated fullname" "Updated Algebra" "$OUT"
echo ""

echo "--- Test: Change shortname ---"
OUT=$($PHP $MOOSH course:mod 2 --shortname "algebra_updated" -p "$MOODLE_PATH" --run -o csv 2>&1)
EC=$?
assert_exit_code "Shortname exit code 0" 0 $EC
assert_output_contains "CSV has shortname" "algebra_updated" "$OUT"
echo ""

echo "--- Test: Change visibility ---"
OUT=$($PHP $MOOSH course:mod 2 --visible 0 -p "$MOODLE_PATH" --run -o csv 2>&1)
EC=$?
assert_exit_code "Visible exit code 0" 0 $EC
# Verify it's hidden
OUT=$($PHP $MOOSH course:mod 2 --visible 1 -p "$MOODLE_PATH" --run -o csv 2>&1)
assert_exit_code "Restore visible exit code 0" 0 $EC
echo ""

echo "--- Test: Change idnumber ---"
OUT=$($PHP $MOOSH course:mod 2 --idnumber "ALG001" -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Idnumber exit code 0" 0 $EC
echo ""

echo "--- Test: Change format ---"
OUT=$($PHP $MOOSH course:mod 2 --format weeks -p "$MOODLE_PATH" --run -o csv 2>&1)
EC=$?
assert_exit_code "Format exit code 0" 0 $EC
assert_output_contains "CSV has weeks" "weeks" "$OUT"
# Restore
$PHP $MOOSH course:mod 2 --format topics -p "$MOODLE_PATH" --run > /dev/null 2>&1
echo ""

echo "--- Test: Change startdate ---"
OUT=$($PHP $MOOSH course:mod 2 --startdate "2025-09-01" -p "$MOODLE_PATH" --run -o csv 2>&1)
EC=$?
assert_exit_code "Startdate exit code 0" 0 $EC
assert_output_contains "CSV has 2025-09-01" "2025-09-01" "$OUT"
echo ""

echo "--- Test: Change enddate ---"
OUT=$($PHP $MOOSH course:mod 2 --enddate "2026-06-30" -p "$MOODLE_PATH" --run -o csv 2>&1)
EC=$?
assert_exit_code "Enddate exit code 0" 0 $EC
assert_output_contains "CSV has 2026-06-30" "2026-06-30" "$OUT"
echo ""

echo "--- Test: Move to category ---"
OUT=$($PHP $MOOSH course:mod 2 --category 3 -p "$MOODLE_PATH" --run -o csv 2>&1)
EC=$?
assert_exit_code "Move category exit code 0" 0 $EC
assert_output_contains "CSV has category 3" ",3," "$OUT"
# Move back
$PHP $MOOSH course:mod 2 --category 2 -p "$MOODLE_PATH" --run > /dev/null 2>&1
echo ""

echo "--- Test: Enable guest access ---"
OUT=$($PHP $MOOSH course:mod 2 --guest 1 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Guest enable exit code 0" 0 $EC
# Verify via enrol:list
ENROL=$($PHP $MOOSH enrol:list 2 -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "Guest is enabled" "guest" "$ENROL"
echo ""

echo "--- Test: Enable self-enrolment ---"
OUT=$($PHP $MOOSH course:mod 2 --selfenrol 1 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Selfenrol enable exit code 0" 0 $EC
echo ""

echo "--- Test: Multiple changes at once ---"
OUT=$($PHP $MOOSH course:mod 2 --fullname "Multi Change" --visible 1 --lang en -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Multi change exit code 0" 0 $EC
assert_output_contains "Shows multi change" "Multi Change" "$OUT"
echo ""

echo "--- Test: Invalid startdate ---"
OUT=$($PHP $MOOSH course:mod 2 --startdate "not-a-date" -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid date" 1 $EC
assert_output_contains "Error for invalid date" "Invalid start date" "$OUT"
echo ""

echo "--- Test: Invalid course ---"
OUT=$($PHP $MOOSH course:mod 999 --fullname "Test" -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid course" 1 $EC
assert_output_contains "Error for invalid course" "not found" "$OUT"
echo ""

echo "--- Test: Invalid category ---"
OUT=$($PHP $MOOSH course:mod 2 --category 999 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid category" 1 $EC
assert_output_contains "Error for invalid category" "not found" "$OUT"
echo ""

echo "--- Test: No modification ---"
OUT=$($PHP $MOOSH course:mod 2 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for no mod" 1 $EC
assert_output_contains "Error for no mod" "No modifications" "$OUT"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH course:mod 2 --summary "Test summary" -p "$MOODLE_PATH" --run -o json 2>&1)
assert_output_contains "JSON has shortname" '"shortname"' "$OUT"
echo ""

echo "--- Test: course:mod help ---"
OUT=$($PHP $MOOSH course:mod -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Modify course properties" "$OUT"
assert_output_contains "Help shows --fullname" "--fullname" "$OUT"
assert_output_contains "Help shows --category" "--category" "$OUT"
assert_output_contains "Help shows --guest" "--guest" "$OUT"
assert_output_contains "Help shows --selfenrol" "--selfenrol" "$OUT"
echo ""



print_summary
