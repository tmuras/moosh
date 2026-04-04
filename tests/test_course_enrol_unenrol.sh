#!/usr/bin/env bash
#
# Integration test for moosh2 course:enrol, course:unenrol
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_course_enrol_unenrol.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 course:enrol/unenrol integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ═══════════════════════════════════════════════════════════════════
# course:enrol
# ═══════════════════════════════════════════════════════════════════

echo "========== course:enrol =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH course:enrol -p "$MOODLE_PATH" 2 student01 2>&1)
echo "$OUT"
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows course" "algebrafundamentals" "$OUT"
assert_output_contains "Shows role" "student" "$OUT"
assert_output_contains "Shows user" "student01" "$OUT"
echo ""

echo "--- Test: Enrol with --run ---"
OUT=$($PHP $MOOSH course:enrol -p "$MOODLE_PATH" --run 2 student01 2>&1)
assert_output_contains "Shows enrolled" "Enrolled" "$OUT"
assert_output_contains "Shows username" "student01" "$OUT"
echo ""

echo "--- Test: Enrol by ID ---"
OUT=$($PHP $MOOSH course:enrol -p "$MOODLE_PATH" --run --id 2 4 2>&1)
assert_output_contains "Enrolled by ID" "Enrolled" "$OUT"
echo ""

echo "--- Test: Enrol with custom role ---"
OUT=$($PHP $MOOSH course:enrol -p "$MOODLE_PATH" --run -r editingteacher 2 student05 2>&1)
assert_output_contains "Enrolled as teacher" "editingteacher" "$OUT"
echo ""

echo "--- Test: Site course rejected ---"
OUT=$($PHP $MOOSH course:enrol -p "$MOODLE_PATH" 1 student01 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for site course" 1 "$EXIT_CODE"
assert_output_contains "Cannot enrol site course" "Cannot enrol" "$OUT"
echo ""

echo "--- Test: Nonexistent user ---"
OUT=$($PHP $MOOSH course:enrol -p "$MOODLE_PATH" 2 nonexistentuser 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for bad user" 1 "$EXIT_CODE"
assert_output_contains "User not found" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH course:enrol -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Enrol users" "$OUT"
assert_output_contains "Help shows --role" "--role" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
# course:unenrol
# ═══════════════════════════════════════════════════════════════════

echo "========== course:unenrol =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH course:unenrol -p "$MOODLE_PATH" 2 3 2>&1)
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows user" "student01" "$OUT"
assert_output_contains "Shows plugin" "manual" "$OUT"
echo ""

echo "--- Test: Unenrol with --run ---"
OUT=$($PHP $MOOSH course:unenrol -p "$MOODLE_PATH" --run 2 3 2>&1)
assert_output_contains "Shows unenrolled" "Unenrolled" "$OUT"
assert_output_contains "Shows username" "student01" "$OUT"
echo ""

echo "--- Test: Already unenrolled ---"
OUT=$($PHP $MOOSH course:unenrol -p "$MOODLE_PATH" --run 2 3 2>&1)
assert_output_contains "No enrolments" "No enrolments" "$OUT"
echo ""

echo "--- Test: Nonexistent course ---"
OUT=$($PHP $MOOSH course:unenrol -p "$MOODLE_PATH" 99999 3 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for bad course" 1 "$EXIT_CODE"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH course:unenrol -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Unenrol users" "$OUT"
assert_output_contains "Help shows --plugin" "--plugin" "$OUT"
echo ""


print_summary
