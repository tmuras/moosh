#!/usr/bin/env bash
#
# Integration test for moosh2 category:list command
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_category_list.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 category:list integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Test data summary:
#   5 categories:
#     1 - Category 1 (empty, default)
#     2 - Mathematics (3 courses)
#     3 - Sciences (3 courses)
#     4 - Humanities (3 courses)
#     5 - Computer Science (6 courses)
#   All categories are top-level (parent=0), visible, depth=1

# ── Basic listing ─────────────────────────────────────────────────

echo "--- Test: Basic category listing ---"
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH" -o csv)
echo "$OUT"
assert_output_contains "Header row present" "id,name,parent,depth,path,coursecount,visible" "$OUT"
assert_output_contains "Mathematics listed" "Mathematics" "$OUT"
assert_output_contains "Sciences listed" "Sciences" "$OUT"
assert_output_contains "Humanities listed" "Humanities" "$OUT"
assert_output_contains "Computer Science listed" "Computer Science" "$OUT"
assert_output_contains "Category 1 listed" "Category 1" "$OUT"
echo ""

# ── ID-only output ────────────────────────────────────────────────

echo "--- Test: ID-only output ---"
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH" -i)
echo "$OUT"
assert_output_contains "Contains category ID 1" "1" "$OUT"
assert_output_contains "Contains category ID 5" "5" "$OUT"
assert_output_not_empty "Output is not empty" "$OUT"
LINE_COUNT=$(echo "$OUT" | wc -l)
assert_output_contains "Output is a single line" "1" "$LINE_COUNT"
echo ""

# ── Table output ──────────────────────────────────────────────────

echo "--- Test: Table output ---"
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH")
echo "$OUT"
assert_output_contains "Table has id header" "id" "$OUT"
assert_output_contains "Table has name header" "name" "$OUT"
assert_output_contains "Table has coursecount header" "coursecount" "$OUT"
echo ""

# ── JSON output ───────────────────────────────────────────────────

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH" -o json)
echo "$OUT" | head -10
assert_output_contains "JSON has name key" '"name"' "$OUT"
assert_output_contains "JSON has Mathematics" '"Mathematics"' "$OUT"
echo ""

# ── Boolean filters ───────────────────────────────────────────────

echo "--- Test: --is-not empty (categories with courses) ---"
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH" --is-not empty -o csv)
echo "$OUT"
assert_output_contains "Mathematics present" "Mathematics" "$OUT"
assert_output_contains "Sciences present" "Sciences" "$OUT"
assert_output_not_contains "Category 1 excluded" "Category 1" "$OUT"
echo ""

echo "--- Test: --is empty (categories without courses) ---"
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH" --is empty -o csv)
echo "$OUT"
assert_output_contains "Category 1 present" "Category 1" "$OUT"
assert_output_not_contains "Mathematics excluded" "Mathematics" "$OUT"
echo ""

echo "--- Test: --is top-level ---"
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH" --is top-level -o csv)
assert_output_contains "All categories are top-level" "Mathematics" "$OUT"
assert_output_contains "Sciences top-level" "Sciences" "$OUT"
echo ""

echo "--- Test: --is visible ---"
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH" --is visible -o csv)
assert_output_contains "Visible Mathematics" "Mathematics" "$OUT"
echo ""

# ── Custom fields ─────────────────────────────────────────────────

echo "--- Test: Custom fields ---"
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH" -f id,name,coursecount -o csv)
echo "$OUT"
assert_output_contains "Custom fields header" "id,name" "$OUT"
assert_output_contains "Category in custom output" "Mathematics" "$OUT"
echo ""

# ── SQL filter ────────────────────────────────────────────────────

echo "--- Test: --sql option ---"
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH" --sql "cc.name = 'Mathematics'" -o csv)
echo "$OUT"
assert_output_contains "SQL returns Mathematics" "Mathematics" "$OUT"
assert_output_not_contains "SQL excludes Sciences" "Sciences" "$OUT"
echo ""

# ── Numeric filters ───────────────────────────────────────────────

echo "--- Test: --number courses>3 ---"
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH" --number courses\>3 -o csv)
echo "$OUT"
assert_output_contains "Computer Science has >3 courses" "Computer Science" "$OUT"
assert_output_not_contains "Mathematics excluded (<= 3)" "Mathematics" "$OUT"
echo ""

echo "--- Test: --number courses=3 ---"
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH" --number courses=3 -o csv)
echo "$OUT"
assert_output_contains "Mathematics has 3 courses" "Mathematics" "$OUT"
assert_output_contains "Sciences has 3 courses" "Sciences" "$OUT"
assert_output_contains "Humanities has 3 courses" "Humanities" "$OUT"
assert_output_not_contains "Computer Science excluded (6 courses)" "Computer Science" "$OUT"
echo ""

echo "--- Test: --number courses=0 ---"
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH" --number courses=0 -o csv)
echo "$OUT"
assert_output_contains "Category 1 has 0 courses" "Category 1" "$OUT"
assert_output_not_contains "Mathematics excluded" "Mathematics" "$OUT"
echo ""

echo "--- Test: --number subcategories=0 ---"
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH" --number subcategories=0 -o csv)
assert_output_contains "All have 0 subcategories" "Mathematics" "$OUT"
echo ""

# ── Pipe -i into --stdin ──────────────────────────────────────────

echo "--- Test: Pipe category:list -i into category:list --stdin ---"
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH" --sql "cc.name = 'Mathematics'" -i | $PHP $MOOSH category:list -p "$MOODLE_PATH" --stdin -o csv)
echo "$OUT"
assert_output_contains "Piped output has header" "id,name" "$OUT"
assert_output_contains "Piped output contains Mathematics" "Mathematics" "$OUT"
assert_output_not_contains "Piped output excludes Sciences" "Sciences" "$OUT"
echo ""

# ── Parent filter ─────────────────────────────────────────────────

echo "--- Test: --parent 0 (top-level) ---"
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH" --parent 0 -o csv)
assert_output_contains "Mathematics is top-level" "Mathematics" "$OUT"
LINE_COUNT=$(echo "$OUT" | wc -l)
assert_output_contains "6 lines (header + 5 categories)" "6" "$LINE_COUNT"
echo ""

# ── Help output ───────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH" --help)
assert_output_contains "Help shows description" "List Moodle course categories" "$OUT"
assert_output_contains "Help shows is/is-not options" "--is" "$OUT"
assert_output_contains "Help shows --sql option" "--sql" "$OUT"
assert_output_contains "Help shows --number option" "--number" "$OUT"
assert_output_contains "Help shows courses metric" "courses" "$OUT"
assert_output_contains "Help shows subcategories metric" "subcategories" "$OUT"
assert_output_contains "Help shows --parent option" "--parent" "$OUT"
echo ""

# ── category-list alias ──────────────────────────────────────────


print_summary
