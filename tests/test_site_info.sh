#!/usr/bin/env bash
#
# Integration test for moosh2 site:info
# Requires a working Moodle 5.2 installation
#
# Usage: bash tests/test_site_info.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 site:info integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ═══════════════════════════════════════════════════════════════════
# site:info
# ═══════════════════════════════════════════════════════════════════

echo "========== site:info =========="
echo ""

echo "--- Test: Table output ---"
OUT=$($PHP $MOOSH site:info -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Shows site name" "Site name" "$OUT"
assert_output_contains "Shows URL" "URL" "$OUT"
assert_output_contains "Shows Moodle version" "Moodle version" "$OUT"
assert_output_contains "Shows database type" "Database type" "$OUT"
assert_output_contains "Shows PHP version" "PHP version" "$OUT"
assert_output_contains "Shows courses" "Courses" "$OUT"
assert_output_contains "Shows users" "Users (total)" "$OUT"
assert_output_contains "Shows enrolments" "Enrolments" "$OUT"
assert_output_contains "Shows activities" "Activities" "$OUT"
assert_output_contains "Shows file references" "File references" "$OUT"
assert_output_contains "Shows database size" "Database size" "$OUT"
assert_output_contains "Shows database tables" "Database tables" "$OUT"
assert_output_contains "Shows largest table" "Largest table #1" "$OUT"
assert_output_contains "Shows log entries" "Log entries" "$OUT"
assert_output_contains "Shows plugins" "Plugins (total)" "$OUT"
assert_output_contains "Shows dataroot" "Dataroot" "$OUT"
echo ""

echo "--- Test: Values are plausible ---"
# Check that courses count is >= 1
COURSES=$(echo "$OUT" | grep 'Courses' | grep -oP '\d+')
if [ "$COURSES" -ge 1 ]; then
    echo "  PASS: Courses count is $COURSES"
    ((PASS++))
else
    echo "  FAIL: Expected courses >= 1, got $COURSES"
    ((FAIL++))
fi
# Check that users count is >= 1
USERS=$(echo "$OUT" | grep 'Users (total)' | grep -oP '\d+')
if [ "$USERS" -ge 1 ]; then
    echo "  PASS: Users count is $USERS"
    ((PASS++))
else
    echo "  FAIL: Expected users >= 1, got $USERS"
    ((FAIL++))
fi
echo ""

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH site:info -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "CSV header" "Metric,Value" "$OUT"
assert_output_contains "CSV has site name" "Site name" "$OUT"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH site:info -p "$MOODLE_PATH" -o json 2>&1)
assert_output_contains "JSON has Metric" '"Metric"' "$OUT"
assert_output_contains "JSON has Value" '"Value"' "$OUT"
assert_output_contains "JSON has site name" "Site name" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH site:info -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Moodle site overview" "$OUT"
echo ""

print_summary
