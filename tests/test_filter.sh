#!/usr/bin/env bash
#
# Integration tests for moosh2 filter commands
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_filter.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 filter commands integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  filter:list
# ═══════════════════════════════════════════════════════════════════

echo "========== filter:list =========="
echo ""

echo "--- Test: List all filters ---"
OUT=$($PHP $MOOSH filter:list -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "List exit code 0" 0 $EC
assert_output_contains "Shows mathjaxloader" "mathjaxloader" "$OUT"
assert_output_contains "Shows multilang" "multilang" "$OUT"
assert_output_contains "Shows state header" "state" "$OUT"
echo ""

echo "--- Test: Enabled only ---"
OUT=$($PHP $MOOSH filter:list --enabled -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Shows enabled filter" "on" "$OUT"
assert_output_not_contains "No disabled filters" "disabled" "$OUT"
echo ""

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH filter:list -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "CSV header" "name,displayname,state" "$OUT"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH filter:list -p "$MOODLE_PATH" -o json 2>&1)
assert_output_contains "JSON has name" '"name": "mathjaxloader"' "$OUT"
echo ""

echo "--- Test: ID-only ---"
OUT=$($PHP $MOOSH filter:list --name-only -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Shows filter name" "mathjaxloader" "$OUT"
assert_output_not_contains "No table header" "displayname" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH filter:list -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "List text filters" "$OUT"
assert_output_contains "Help shows --enabled" "--enabled" "$OUT"
assert_output_contains "Help shows --context" "--context" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  filter:mod
# ═══════════════════════════════════════════════════════════════════

echo "========== filter:mod =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH filter:mod multilang --state on -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Enable filter ---"
OUT=$($PHP $MOOSH filter:mod multilang --state on -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Enable exit code 0" 0 $EC
assert_output_contains "Shows modified" "Modified" "$OUT"
# Verify
VERIFY=$($PHP $MOOSH filter:list --enabled --name-only -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Multilang now enabled" "multilang" "$VERIFY"
echo ""

echo "--- Test: Disable filter ---"
OUT=$($PHP $MOOSH filter:mod multilang --state disabled -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Disable exit code 0" 0 $EC
assert_output_contains "Shows modified" "Modified" "$OUT"
echo ""

echo "--- Test: Set off (available but inactive) ---"
OUT=$($PHP $MOOSH filter:mod multilang --state off -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Off exit code 0" 0 $EC
echo ""

echo "--- Test: Move filter ---"
# First enable it
$PHP $MOOSH filter:mod multilang --state on -p "$MOODLE_PATH" --run > /dev/null 2>&1
OUT=$($PHP $MOOSH filter:mod multilang --move up -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Move exit code 0" 0 $EC
assert_output_contains "Shows modified" "Modified" "$OUT"
echo ""

echo "--- Test: Apply to strings ---"
OUT=$($PHP $MOOSH filter:mod multilang --apply-to-strings 1 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Apply-to-strings exit code 0" 0 $EC
echo ""

echo "--- Test: Invalid filter ---"
OUT=$($PHP $MOOSH filter:mod nonexistent --state on -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid filter" 1 $EC
assert_output_contains "Error for invalid filter" "not found" "$OUT"
echo ""

echo "--- Test: Invalid state ---"
OUT=$($PHP $MOOSH filter:mod multilang --state invalid -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid state" 1 $EC
assert_output_contains "Error for invalid state" "Invalid state" "$OUT"
echo ""

echo "--- Test: No modification ---"
OUT=$($PHP $MOOSH filter:mod multilang -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for no mod" 1 $EC
assert_output_contains "Error for no mod" "No modifications" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH filter:mod -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Modify a text filter" "$OUT"
assert_output_contains "Help shows --state" "--state" "$OUT"
assert_output_contains "Help shows --move" "--move" "$OUT"
assert_output_contains "Help shows --apply-to-strings" "--apply-to-strings" "$OUT"
assert_output_contains "Help shows --config" "--config" "$OUT"
echo ""


# Clean up - disable multilang
$PHP $MOOSH filter:mod multilang --state disabled -p "$MOODLE_PATH" --run > /dev/null 2>&1

print_summary
