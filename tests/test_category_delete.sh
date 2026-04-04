#!/usr/bin/env bash
#
# Integration test for moosh2 category:delete
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_category_delete.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 category:delete integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Test data:
# Category 1 (id=1) - empty
# Mathematics (id=2) - 3 courses
# Sciences (id=3) - 3 courses
# Humanities (id=4) - 3 courses
# Computer Science (id=5) - 6 courses

echo "========== category:delete =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH category:delete -p "$MOODLE_PATH" 1 2>&1)
echo "$OUT"
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows category name" "Category 1" "$OUT"
assert_output_contains "Shows courses count" "courses=0" "$OUT"
assert_output_contains "Shows permanent delete" "permanently deleted" "$OUT"
echo ""

echo "--- Test: Dry run with --move-to ---"
OUT=$($PHP $MOOSH category:delete -p "$MOODLE_PATH" --move-to 1 2 2>&1)
assert_output_contains "Shows move target" "Category 1" "$OUT"
assert_output_contains "Shows courses" "courses=3" "$OUT"
assert_output_contains "Shows moved" "moved to" "$OUT"
echo ""

echo "--- Test: Delete empty category ---"
OUT=$($PHP $MOOSH category:delete -p "$MOODLE_PATH" --run 1 2>&1)
echo "$OUT"
assert_output_contains "Shows deleted" "Deleted category" "$OUT"
assert_output_contains "Shows 0 courses" "0 course(s)" "$OUT"
# Verify it's gone
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_not_contains "Category 1 removed" "Category 1" "$OUT"
echo ""

echo "--- Test: Delete category with courses (full delete) ---"
OUT=$($PHP $MOOSH category:delete -p "$MOODLE_PATH" --run 3 2>&1)
echo "$OUT"
assert_output_contains "Deleted Sciences" "Deleted category" "$OUT"
assert_output_contains "3 courses deleted" "3 course(s)" "$OUT"
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_not_contains "Sciences removed" "Sciences" "$OUT"
echo ""

echo "--- Test: Delete with --move-to ---"
# Create a target category first
OUT=$($PHP $MOOSH category:create -p "$MOODLE_PATH" --run "Target" 2>&1)
TARGET_ID=$(echo "$OUT" | grep -oP '^\|\s*\K\d+' | head -1)
echo "  Created target category ID=$TARGET_ID"
# Move Mathematics courses to Target
OUT=$($PHP $MOOSH category:delete -p "$MOODLE_PATH" --run --move-to "$TARGET_ID" 2 2>&1)
echo "$OUT"
assert_output_contains "Shows moved" "moved to" "$OUT"
# Verify courses exist in target
OUT=$($PHP $MOOSH category:list -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "Target has courses" "Target" "$OUT"
assert_output_not_contains "Mathematics removed" "Mathematics," "$OUT"
echo ""

echo "--- Test: Delete multiple categories ---"
OUT=$($PHP $MOOSH category:delete -p "$MOODLE_PATH" 4 5 2>&1)
assert_output_contains "Shows Humanities" "Humanities" "$OUT"
assert_output_contains "Shows Computer Science" "Computer Science" "$OUT"
echo ""

echo "--- Test: Nonexistent category ---"
OUT=$($PHP $MOOSH category:delete -p "$MOODLE_PATH" 99999 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for nonexistent" 1 "$EXIT_CODE"
assert_output_contains "Shows not found" "not found" "$OUT"
echo ""

echo "--- Test: Invalid move-to target ---"
OUT=$($PHP $MOOSH category:delete -p "$MOODLE_PATH" --move-to 99999 4 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for invalid move-to" 1 "$EXIT_CODE"
assert_output_contains "Move-to not found" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH category:delete -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Delete course categories" "$OUT"
assert_output_contains "Help shows --move-to" "--move-to" "$OUT"
echo ""


print_summary
