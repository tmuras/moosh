#!/usr/bin/env bash
#
# Integration test for moosh2 category:create command
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_category_create.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 category:create integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ── Dry run ───────────────────────────────────────────────────────

echo "--- Test: Dry run (no --run) ---"
OUT=$($PHP $MOOSH category:create -p "$MOODLE_PATH" "New Category")
echo "$OUT"
assert_output_contains "Shows dry run message" "Dry run" "$OUT"
assert_output_contains "Shows name" "New Category" "$OUT"
# Verify category was NOT created
VERIFY=$($PHP $MOOSH category:list -p "$MOODLE_PATH" --sql "cc.name = 'New Category'" -o csv)
assert_output_not_contains "Category not created without --run" "New Category" "$VERIFY"
echo ""

# ── Create single category ────────────────────────────────────────

echo "--- Test: Create single category ---"
OUT=$($PHP $MOOSH category:create -p "$MOODLE_PATH" --run "New Category" -o csv)
echo "$OUT"
assert_output_contains "Output has header" "id,name,parent" "$OUT"
assert_output_contains "Output has name" "New Category" "$OUT"
assert_output_contains "Parent is 0" ",0" "$OUT"
# Verify category exists
VERIFY=$($PHP $MOOSH category:list -p "$MOODLE_PATH" --sql "cc.name = 'New Category'" -o csv)
assert_output_contains "Category exists after create" "New Category" "$VERIFY"
echo ""

# ── Create multiple categories ────────────────────────────────────

echo "--- Test: Create multiple categories ---"
OUT=$($PHP $MOOSH category:create -p "$MOODLE_PATH" --run "Cat A" "Cat B" "Cat C" -o csv)
echo "$OUT"
assert_output_contains "First category created" "Cat A" "$OUT"
assert_output_contains "Second category created" "Cat B" "$OUT"
assert_output_contains "Third category created" "Cat C" "$OUT"
echo ""

# ── Create subcategory ────────────────────────────────────────────

echo "--- Test: Create subcategory ---"
# Get parent category ID (Mathematics = id 2)
OUT=$($PHP $MOOSH category:create -p "$MOODLE_PATH" --run --parent 2 "Linear Algebra" -o csv)
echo "$OUT"
assert_output_contains "Created Linear Algebra" "Linear Algebra" "$OUT"
assert_output_contains "Parent is 2" ",2" "$OUT"
# Verify it's a subcategory
VERIFY=$($PHP $MOOSH category:list -p "$MOODLE_PATH" --sql "cc.name = 'Linear Algebra'" -o csv)
assert_output_contains "Subcategory parent is 2" ",2," "$VERIFY"
echo ""

# ── Create category with options ──────────────────────────────────

echo "--- Test: Create category with options ---"
OUT=$($PHP $MOOSH category:create -p "$MOODLE_PATH" --run \
    --description "A test category" \
    --idnumber "TESTCAT" \
    --visible 1 \
    "Custom Category" -o csv)
echo "$OUT"
assert_output_contains "Created Custom Category" "Custom Category" "$OUT"
echo ""

# ── Create hidden category ────────────────────────────────────────

echo "--- Test: Create hidden category ---"
OUT=$($PHP $MOOSH category:create -p "$MOODLE_PATH" --run --visible 0 "Hidden Category" -o csv)
echo "$OUT"
assert_output_contains "Created Hidden Category" "Hidden Category" "$OUT"
echo ""

# ── JSON output ───────────────────────────────────────────────────

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH category:create -p "$MOODLE_PATH" --run "JSON Cat" -o json)
echo "$OUT"
assert_output_contains "JSON has name" '"name"' "$OUT"
assert_output_contains "JSON has JSON Cat" '"JSON Cat"' "$OUT"
echo ""

# ── Help output ───────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH category:create -p "$MOODLE_PATH" --help)
assert_output_contains "Help shows description" "Create Moodle course categories" "$OUT"
assert_output_contains "Help shows --parent" "--parent" "$OUT"
assert_output_contains "Help shows --description" "--description" "$OUT"
assert_output_contains "Help shows --idnumber" "--idnumber" "$OUT"
assert_output_contains "Help shows --visible" "--visible" "$OUT"
echo ""

# ── category-create alias ────────────────────────────────────────


print_summary
