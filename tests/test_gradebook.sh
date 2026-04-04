#!/usr/bin/env bash
#
# Integration tests for moosh2 gradebook commands:
#   gradebook:export, gradebook:import
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_gradebook.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 gradebook commands integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

TMPDIR=$(mktemp -d)
trap "rm -rf $TMPDIR" EXIT

# Test data summary:
#   Course 2 has 50 students enrolled.
#   We create an assignment activity so there's a grade item to export/import.

echo "--- Creating test assignment ---"
OUT=$($PHP $MOOSH activity:create assign 2 -p "$MOODLE_PATH" --name "Test Assignment" --run 2>&1)
echo "$OUT"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  gradebook:export
# ═══════════════════════════════════════════════════════════════════

echo "========== gradebook:export =========="
echo ""

echo "--- Test: Export default txt format ---"
OUT=$($PHP $MOOSH gradebook:export 2 -p "$MOODLE_PATH" 2>&1)
assert_output_contains "CSV header has Email" "Email address" "$OUT"
assert_output_contains "CSV header has First name" "First name" "$OUT"
assert_output_contains "Has student data" "student01@example.invalid" "$OUT"
echo ""

echo "--- Test: Export contains all students ---"
line_count=$(echo "$OUT" | wc -l)
if [ "$line_count" -ge 50 ]; then
    echo "  PASS: Has at least 50 lines (got $line_count)"
    ((PASS++))
else
    echo "  FAIL: Expected at least 50 lines, got $line_count"
    ((FAIL++))
fi
echo ""

echo "--- Test: Export to file ---"
$PHP $MOOSH gradebook:export 2 -p "$MOODLE_PATH" > "$TMPDIR/export.csv" 2>&1
assert_exit_code "Export exit code 0" 0 $?
if [ -s "$TMPDIR/export.csv" ]; then
    echo "  PASS: Export file not empty"
    ((PASS++))
else
    echo "  FAIL: Export file is empty"
    ((FAIL++))
fi
echo ""

echo "--- Test: Export XML format ---"
OUT=$($PHP $MOOSH gradebook:export 2 -p "$MOODLE_PATH" -f xml 2>&1)
assert_output_contains "XML has results tag" "results" "$OUT"
echo ""

echo "--- Test: Export invalid format ---"
OUT=$($PHP $MOOSH gradebook:export 2 -p "$MOODLE_PATH" -f pdf 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid format" 1 $EC
assert_output_contains "Error for invalid format" "Invalid format" "$OUT"
echo ""

echo "--- Test: Export invalid course ---"
OUT=$($PHP $MOOSH gradebook:export 999 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid course" 1 $EC
assert_output_contains "Error for invalid course" "not found" "$OUT"
echo ""

echo "--- Test: Export percentage display ---"
OUT=$($PHP $MOOSH gradebook:export 2 -p "$MOODLE_PATH" --display-type 2 2>&1)
assert_output_contains "Percentage header" "Percentage" "$OUT"
echo ""

echo "--- Test: gradebook:export help ---"
OUT=$($PHP $MOOSH gradebook:export -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Export gradebook data for a course" "$OUT"
assert_output_contains "Help shows courseid" "courseid" "$OUT"
assert_output_contains "Help shows --format" "--format" "$OUT"
assert_output_contains "Help shows --display-type" "--display-type" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  gradebook:import
# ═══════════════════════════════════════════════════════════════════

echo "========== gradebook:import =========="
echo ""

echo "--- Test: Import dry run ---"
OUT=$($PHP $MOOSH gradebook:import "$TMPDIR/export.csv" 2 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows mapping" "Mapped CSV column" "$OUT"
assert_output_contains "Shows user count" "Users matched" "$OUT"
assert_output_contains "Shows dry run message" "Dry run" "$OUT"
echo ""

echo "--- Test: Import with --run ---"
OUT=$($PHP $MOOSH gradebook:import "$TMPDIR/export.csv" 2 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Import exit code 0" 0 $EC
assert_output_contains "Shows imported count" "Imported" "$OUT"
assert_output_contains "Shows user count" "user(s)" "$OUT"
echo ""

echo "--- Test: Import nonexistent file ---"
OUT=$($PHP $MOOSH gradebook:import /nonexistent/grades.csv 2 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for missing file" 1 $EC
assert_output_contains "Error for missing file" "not found" "$OUT"
echo ""

echo "--- Test: Import invalid course ---"
OUT=$($PHP $MOOSH gradebook:import "$TMPDIR/export.csv" 999 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid course" 1 $EC
assert_output_contains "Error for invalid course" "not found" "$OUT"
echo ""

echo "--- Test: Import invalid map-users-by ---"
OUT=$($PHP $MOOSH gradebook:import "$TMPDIR/export.csv" 2 -p "$MOODLE_PATH" --map-users-by username 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid map-users-by" 1 $EC
assert_output_contains "Error for invalid mapping" "Invalid" "$OUT"
echo ""

echo "--- Test: gradebook:import help ---"
OUT=$($PHP $MOOSH gradebook:import -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Import grades from a CSV file" "$OUT"
assert_output_contains "Help shows file" "file" "$OUT"
assert_output_contains "Help shows --map-users-by" "--map-users-by" "$OUT"
assert_output_contains "Help shows --course-idnumber" "--course-idnumber" "$OUT"
echo ""


print_summary
