#!/usr/bin/env bash
#
# Integration test for moosh2 course:top
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_course_top.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 course:top integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Test data: course 15 (Recently Active Course) has 1 course_viewed log entry

echo "========== course:top =========="
echo ""

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH course:top -p "$MOODLE_PATH" -o csv)
echo "$OUT"
assert_output_contains "Header" "courseid,shortname,fullname,hits" "$OUT"
assert_output_contains "Recently Active Course" "recentlyactive" "$OUT"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH course:top -p "$MOODLE_PATH" -o json)
assert_output_contains "JSON has courseid" '"courseid"' "$OUT"
assert_output_contains "JSON has hits" '"hits"' "$OUT"
echo ""

echo "--- Test: Limit option ---"
OUT=$($PHP $MOOSH course:top -p "$MOODLE_PATH" --limit 1 -o csv)
DATA_LINES=$(echo "$OUT" | tail -n +2 | wc -l)
if [ "$DATA_LINES" -le 1 ]; then
    echo "  PASS: Limit respected ($DATA_LINES rows)"
    ((PASS++))
else
    echo "  FAIL: Limit not respected ($DATA_LINES rows)"
    ((FAIL++))
fi
echo ""

echo "--- Test: Days option ---"
OUT=$($PHP $MOOSH course:top -p "$MOODLE_PATH" --days 1 -o csv)
assert_output_contains "Days filter works" "courseid" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH course:top -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Show top courses" "$OUT"
assert_output_contains "Help shows --limit" "--limit" "$OUT"
assert_output_contains "Help shows --days" "--days" "$OUT"
echo ""


print_summary
