#!/usr/bin/env bash
#
# Integration test for moosh2 activity:info
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_activity_info.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 activity:info integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Test data: cmid 1 is a resource in course 2 (Algebra Fundamentals)

# ═══════════════════════════════════════════════════════════════════
# activity:info
# ═══════════════════════════════════════════════════════════════════

echo "========== activity:info =========="
echo ""

echo "--- Test: Table output ---"
OUT=$($PHP $MOOSH activity:info -p "$MOODLE_PATH" 1 2>&1)
echo "$OUT"
assert_output_contains "Shows course module ID" "Course module ID" "$OUT"
assert_output_contains "Shows module type" "resource" "$OUT"
assert_output_contains "Shows name" "Algebra Fundamentals" "$OUT"
assert_output_contains "Shows course shortname" "algebrafundamentals" "$OUT"
assert_output_contains "Shows visibility" "Visible" "$OUT"
assert_output_contains "Shows completion" "Completion tracking" "$OUT"
assert_output_contains "Shows grade item" "Grade item" "$OUT"
assert_output_contains "Shows log entries" "Log entries" "$OUT"
assert_output_contains "Shows files" "Files" "$OUT"
assert_output_contains "Shows context" "Context ID" "$OUT"
echo ""

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH activity:info -p "$MOODLE_PATH" 1 -o csv 2>&1)
assert_output_contains "CSV has module type" "resource" "$OUT"
assert_output_contains "CSV has header" "Course module ID" "$OUT"
assert_output_contains "CSV has name" "Algebra Fundamentals" "$OUT"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH activity:info -p "$MOODLE_PATH" 1 -o json 2>&1)
assert_output_contains "JSON has module type" '"Module type"' "$OUT"
assert_output_contains "JSON has resource" "resource" "$OUT"
echo ""

echo "--- Test: Nonexistent cmid ---"
OUT=$($PHP $MOOSH activity:info -p "$MOODLE_PATH" 99999 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for nonexistent cmid" 1 "$EXIT_CODE"
assert_output_contains "Shows not found" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH activity:info -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Show detailed information" "$OUT"
assert_output_contains "Help shows cmid" "cmid" "$OUT"
echo ""


# Test with a different activity to verify we handle various types
echo "--- Test: Multiple activities ---"
# Get all cmids and test a few
for CMID in 1 2 3; do
    OUT=$($PHP $MOOSH activity:info -p "$MOODLE_PATH" "$CMID" -o csv 2>&1)
    assert_output_contains "Activity $CMID has data" "Course module ID" "$OUT"
done
echo ""

print_summary
