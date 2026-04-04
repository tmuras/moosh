#!/usr/bin/env bash
#
# Integration test for moosh2 fontawesome:list
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_fontawesome_list.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 fontawesome:list integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

echo "========== fontawesome:list =========="
echo ""

echo "--- Test: CSV output (count) ---"
LINE_COUNT=$($PHP $MOOSH fontawesome:list -p "$MOODLE_PATH" -o csv 2>&1 | wc -l)
if [ "$LINE_COUNT" -gt 2000 ]; then
    echo "  PASS: Has many icons ($LINE_COUNT lines)"
    ((PASS++))
else
    echo "  FAIL: Expected >2000 icons, got $LINE_COUNT"
    ((FAIL++))
fi
echo ""

echo "--- Test: CSV header ---"
OUT=$($PHP $MOOSH fontawesome:list -p "$MOODLE_PATH" house -o csv 2>&1)
FIRST_LINE=$(echo "$OUT" | head -1)
assert_output_contains "Header row" "name,codepoint,style,html" "$FIRST_LINE"
echo ""

echo "--- Test: Search for 'house' ---"
OUT=$($PHP $MOOSH fontawesome:list -p "$MOODLE_PATH" house -o csv 2>&1)
assert_output_contains "Finds house icon" "house,f015,solid" "$OUT"
assert_output_contains "Has HTML" "fa-solid fa-house" "$OUT"
assert_output_not_contains "No unrelated icons" "github" "$OUT"
echo ""

echo "--- Test: Search for 'github' ---"
OUT=$($PHP $MOOSH fontawesome:list -p "$MOODLE_PATH" github -o csv 2>&1)
assert_output_contains "Finds github" "github" "$OUT"
assert_output_contains "Brand style" "brands" "$OUT"
assert_output_contains "Brand HTML class" "fa-brands" "$OUT"
echo ""

echo "--- Test: Style filter solid ---"
OUT=$($PHP $MOOSH fontawesome:list -p "$MOODLE_PATH" house --style solid -o csv 2>&1)
assert_output_contains "Solid house found" "house" "$OUT"
assert_output_not_contains "No brands in solid" "brands" "$OUT"
echo ""

echo "--- Test: Style filter brands ---"
OUT=$($PHP $MOOSH fontawesome:list -p "$MOODLE_PATH" --style brands -o csv 2>&1 | head -5)
assert_output_contains "Has brand icons" "brands" "$OUT"
assert_output_not_contains "No solid in brands" ",solid," "$OUT"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH fontawesome:list -p "$MOODLE_PATH" house -o json 2>&1)
assert_output_contains "JSON has name" '"name"' "$OUT"
assert_output_contains "JSON has codepoint" '"codepoint"' "$OUT"
assert_output_contains "JSON has style" '"style"' "$OUT"
assert_output_contains "JSON has html" '"html"' "$OUT"
echo ""

echo "--- Test: Table output ---"
OUT=$($PHP $MOOSH fontawesome:list -p "$MOODLE_PATH" house 2>&1)
assert_output_contains "Table has house" "house" "$OUT"
assert_output_contains "Table has solid" "solid" "$OUT"
echo ""

echo "--- Test: No results ---"
OUT=$($PHP $MOOSH fontawesome:list -p "$MOODLE_PATH" xyznonexistent123 -o csv 2>&1)
LINE_COUNT=$(echo "$OUT" | wc -l)
if [ "$LINE_COUNT" -le 1 ]; then
    echo "  PASS: Only header for no results ($LINE_COUNT lines)"
    ((PASS++))
else
    echo "  FAIL: Expected only header, got $LINE_COUNT lines"
    ((FAIL++))
fi
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH fontawesome:list -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "List all Font Awesome icons" "$OUT"
assert_output_contains "Help shows --style" "--style" "$OUT"
echo ""


print_summary
