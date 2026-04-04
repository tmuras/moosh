#!/usr/bin/env bash
#
# Integration test for moosh2 course:last-visited
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_course_last_visited.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 course:last-visited integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

echo "========== course:last-visited =========="
echo ""

echo "--- Test: Single course ---"
OUT=$($PHP $MOOSH course:last-visited -p "$MOODLE_PATH" 2 -o csv)
echo "$OUT"
assert_output_contains "Header" "courseid,shortname,last_access,hours_ago" "$OUT"
assert_output_contains "Course 2" "algebrafundamentals" "$OUT"
echo ""

echo "--- Test: Multiple courses ---"
OUT=$($PHP $MOOSH course:last-visited -p "$MOODLE_PATH" 2 3 4 -o csv)
echo "$OUT"
LINE_COUNT=$(echo "$OUT" | wc -l)
assert_output_contains "4 lines (header + 3 courses)" "4" "$LINE_COUNT"
echo ""

echo "--- Test: Never visited course ---"
OUT=$($PHP $MOOSH course:last-visited -p "$MOODLE_PATH" 2 -o csv)
assert_output_contains "Shows never for unvisited" "never" "$OUT"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH course:last-visited -p "$MOODLE_PATH" 2 -o json)
assert_output_contains "JSON has courseid" '"courseid"' "$OUT"
assert_output_contains "JSON has last_access" '"last_access"' "$OUT"
echo ""

echo "--- Test: Invalid course ---"
OUT=$($PHP $MOOSH course:last-visited -p "$MOODLE_PATH" 99999 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for invalid course" 1 "$EXIT_CODE"
assert_output_contains "Not found error" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH course:last-visited -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Show when a course was last visited" "$OUT"
echo ""


print_summary
