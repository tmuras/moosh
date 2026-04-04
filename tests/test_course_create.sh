#!/usr/bin/env bash
#
# Integration test for moosh2 course:create command
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_course_create.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 course:create integration tests ==="
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
OUT=$($PHP $MOOSH course:create -p "$MOODLE_PATH" newcourse1)
echo "$OUT"
assert_output_contains "Shows dry run message" "Dry run" "$OUT"
assert_output_contains "Shows shortname" "newcourse1" "$OUT"
assert_output_contains "Shows category" "category: 1" "$OUT"
# Verify course was NOT created
VERIFY=$($PHP $MOOSH course:list -p "$MOODLE_PATH" --sql "shortname = 'newcourse1'" -o csv)
assert_output_not_contains "Course not created without --run" "newcourse1" "$VERIFY"
echo ""

# ── Create single course ──────────────────────────────────────────

echo "--- Test: Create single course ---"
OUT=$($PHP $MOOSH course:create -p "$MOODLE_PATH" --run --category 2 newcourse1 -o csv)
echo "$OUT"
assert_output_contains "Output has header" "id,shortname,fullname,category" "$OUT"
assert_output_contains "Output has shortname" "newcourse1" "$OUT"
assert_output_contains "Category is 2" ",2" "$OUT"
# Verify course exists
VERIFY=$($PHP $MOOSH course:list -p "$MOODLE_PATH" --sql "shortname = 'newcourse1'" -o csv)
assert_output_contains "Course exists after create" "newcourse1" "$VERIFY"
echo ""

# ── Create multiple courses ───────────────────────────────────────

echo "--- Test: Create multiple courses ---"
OUT=$($PHP $MOOSH course:create -p "$MOODLE_PATH" --run --category 3 multi1 multi2 multi3 -o csv)
echo "$OUT"
assert_output_contains "First course created" "multi1" "$OUT"
assert_output_contains "Second course created" "multi2" "$OUT"
assert_output_contains "Third course created" "multi3" "$OUT"
echo ""

# ── Create course with options ────────────────────────────────────

echo "--- Test: Create course with all options ---"
OUT=$($PHP $MOOSH course:create -p "$MOODLE_PATH" --run \
    --category 4 \
    --fullname "Advanced Mathematics" \
    --idnumber "MATH301" \
    --visible 1 \
    --numsections 10 \
    advmath -o csv)
echo "$OUT"
assert_output_contains "Created advmath" "advmath" "$OUT"
assert_output_contains "Full name" "Advanced Mathematics" "$OUT"
echo ""

# ── Create hidden course ──────────────────────────────────────────

echo "--- Test: Create hidden course ---"
OUT=$($PHP $MOOSH course:create -p "$MOODLE_PATH" --run --category 2 --visible 0 hiddencourse -o csv)
echo "$OUT"
assert_output_contains "Created hidden course" "hiddencourse" "$OUT"
# Verify it's hidden
VERIFY=$($PHP $MOOSH course:list -p "$MOODLE_PATH" --sql "shortname = 'hiddencourse'" -o csv)
assert_output_contains "Course shows visible 0" ",0" "$VERIFY"
echo ""

# ── JSON output ───────────────────────────────────────────────────

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH course:create -p "$MOODLE_PATH" --run --category 2 jsoncourse -o json)
echo "$OUT"
assert_output_contains "JSON has shortname" '"shortname"' "$OUT"
assert_output_contains "JSON has jsoncourse" '"jsoncourse"' "$OUT"
echo ""

# ── Help output ───────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH course:create -p "$MOODLE_PATH" --help)
assert_output_contains "Help shows description" "Create Moodle courses" "$OUT"
assert_output_contains "Help shows --category" "--category" "$OUT"
assert_output_contains "Help shows --fullname" "--fullname" "$OUT"
assert_output_contains "Help shows --format" "--format" "$OUT"
assert_output_contains "Help shows --visible" "--visible" "$OUT"
assert_output_contains "Help shows --numsections" "--numsections" "$OUT"
echo ""

# ── course-create alias ──────────────────────────────────────────


print_summary
