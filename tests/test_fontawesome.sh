#!/usr/bin/env bash
#
# Integration test for moosh2 fontawesome:maplist and fontawesome:refresh-cache
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_fontawesome.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 fontawesome commands integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ═══════════════════════════════════════════════════════════════════
# fontawesome:maplist
# ═══════════════════════════════════════════════════════════════════

echo "========== fontawesome:maplist =========="
echo ""

echo "--- Test: List all icons (CSV) ---"
OUT=$($PHP $MOOSH fontawesome:maplist -p "$MOODLE_PATH" -o csv)
FIRST_LINE=$(echo "$OUT" | head -1)
assert_output_contains "Header row" "component,icon,fontawesome" "$FIRST_LINE"
LINE_COUNT=$(echo "$OUT" | wc -l)
if [ "$LINE_COUNT" -gt 100 ]; then
    echo "  PASS: Has many icons ($LINE_COUNT)"
    ((PASS++))
else
    echo "  FAIL: Expected >100 icons, got $LINE_COUNT"
    ((FAIL++))
fi
assert_output_contains "Has fa class" "fa" "$OUT"
echo ""

echo "--- Test: Search for 'search' ---"
OUT=$($PHP $MOOSH fontawesome:maplist -p "$MOODLE_PATH" search -o csv)
echo "$OUT"
assert_output_contains "Finds search icon" "search" "$OUT"
assert_output_contains "Maps to magnifying-glass" "magnifying-glass" "$OUT"
assert_output_not_contains "No unrelated icons" "calendar" "$OUT"
echo ""

echo "--- Test: Search for 'calendar' ---"
OUT=$($PHP $MOOSH fontawesome:maplist -p "$MOODLE_PATH" calendar -o csv)
echo "$OUT"
assert_output_contains "Finds calendar icon" "calendar" "$OUT"
echo ""

echo "--- Test: Component filter ---"
OUT=$($PHP $MOOSH fontawesome:maplist -p "$MOODLE_PATH" --component theme -o csv)
echo "$OUT" | head -5
assert_output_contains "All rows are theme component" "theme," "$OUT"
assert_output_not_contains "No core component" "core," "$OUT"
echo ""

echo "--- Test: Component filter + search ---"
OUT=$($PHP $MOOSH fontawesome:maplist -p "$MOODLE_PATH" --component core search -o csv)
echo "$OUT"
assert_output_contains "Core search icon" "core,a/search" "$OUT"
assert_output_not_contains "No theme icons" "theme," "$OUT"
echo ""

echo "--- Test: No results ---"
OUT=$($PHP $MOOSH fontawesome:maplist -p "$MOODLE_PATH" xyznonexistent123 -o csv)
LINE_COUNT=$(echo "$OUT" | wc -l)
assert_output_contains "Only header for no results" "1" "$LINE_COUNT"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH fontawesome:maplist -p "$MOODLE_PATH" search -o json)
echo "$OUT" | head -10
assert_output_contains "JSON has component" '"component"' "$OUT"
assert_output_contains "JSON has icon" '"icon"' "$OUT"
assert_output_contains "JSON has fontawesome" '"fontawesome"' "$OUT"
echo ""

echo "--- Test: Table output ---"
OUT=$($PHP $MOOSH fontawesome:maplist -p "$MOODLE_PATH" search)
echo "$OUT"
assert_output_contains "Table has component header" "component" "$OUT"
assert_output_contains "Table has magnifying-glass" "magnifying-glass" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH fontawesome:maplist -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "List and search Font Awesome icon mappings" "$OUT"
assert_output_contains "Help shows --component" "--component" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
# fontawesome:refresh-cache
# ═══════════════════════════════════════════════════════════════════

echo "========== fontawesome:refresh-cache =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH fontawesome:refresh-cache -p "$MOODLE_PATH")
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Refresh cache ---"
OUT=$($PHP $MOOSH fontawesome:refresh-cache -p "$MOODLE_PATH" --run)
echo "$OUT"
assert_output_contains "Shows refreshed" "refreshed" "$OUT"
assert_output_contains "Shows mappings count" "mappings" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH fontawesome:refresh-cache -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Refresh the Font Awesome icon mapping cache" "$OUT"
echo ""


print_summary
