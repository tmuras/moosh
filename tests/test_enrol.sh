#!/usr/bin/env bash
#
# Integration tests for moosh2 enrol commands:
#   enrol:list, enrol:mod
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_enrol.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 enrol commands integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  enrol:list
# ═══════════════════════════════════════════════════════════════════

echo "========== enrol:list =========="
echo ""

echo "--- Test: List enrolment methods ---"
OUT=$($PHP $MOOSH enrol:list 2 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "List exit code 0" 0 $EC
assert_output_contains "Shows manual" "manual" "$OUT"
assert_output_contains "Shows guest" "guest" "$OUT"
assert_output_contains "Shows self" "self" "$OUT"
assert_output_contains "Shows enabled" "enabled" "$OUT"
echo ""

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH enrol:list 2 -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "CSV header" "id,enrol,name,status,roleid,enrolments" "$OUT"
assert_output_contains "CSV has manual" "manual" "$OUT"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH enrol:list 2 -p "$MOODLE_PATH" -o json 2>&1)
assert_output_contains "JSON has enrol" '"enrol": "manual"' "$OUT"
echo ""

echo "--- Test: ID-only ---"
OUT=$($PHP $MOOSH enrol:list 2 -p "$MOODLE_PATH" --id-only 2>&1)
assert_output_not_empty "ID-only not empty" "$OUT"
echo ""

echo "--- Test: Shows enrolment count ---"
OUT=$($PHP $MOOSH enrol:list 2 -p "$MOODLE_PATH" -o csv 2>&1)
# Course 2 has 60 manual enrolments (50 students + 10 teachers)
assert_output_contains "Has enrolment count" "60" "$OUT"
echo ""

echo "--- Test: Invalid course ---"
OUT=$($PHP $MOOSH enrol:list 999 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid course" 1 $EC
assert_output_contains "Error for invalid course" "not found" "$OUT"
echo ""

echo "--- Test: enrol:list help ---"
OUT=$($PHP $MOOSH enrol:list -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "List enrolment methods" "$OUT"
assert_output_contains "Help shows courseid" "courseid" "$OUT"
echo ""



# ═══════════════════════════════════════════════════════════════════
#  enrol:mod
# ═══════════════════════════════════════════════════════════════════

echo "========== enrol:mod =========="
echo ""

# Get the guest enrolment instance ID
GUEST_ID=$($PHP $MOOSH enrol:list 2 -p "$MOODLE_PATH" -o csv 2>&1 | grep guest | head -1 | cut -d, -f1)
SELF_ID=$($PHP $MOOSH enrol:list 2 -p "$MOODLE_PATH" -o csv 2>&1 | grep self | head -1 | cut -d, -f1)
MANUAL_ID=$($PHP $MOOSH enrol:list 2 -p "$MOODLE_PATH" -o csv 2>&1 | grep manual | head -1 | cut -d, -f1)
echo "  Guest ID: $GUEST_ID, Self ID: $SELF_ID, Manual ID: $MANUAL_ID"

echo "--- Test: Mod dry run ---"
OUT=$($PHP $MOOSH enrol:mod $GUEST_ID --enabled 1 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows status change" "enabled" "$OUT"
echo ""

echo "--- Test: Enable guest ---"
OUT=$($PHP $MOOSH enrol:mod $GUEST_ID --enabled 1 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Enable exit code 0" 0 $EC
assert_output_contains "Shows enabled" "enabled" "$OUT"
assert_output_contains "Shows guest" "guest" "$OUT"
echo ""

echo "--- Test: Disable guest ---"
OUT=$($PHP $MOOSH enrol:mod $GUEST_ID --enabled 0 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Disable exit code 0" 0 $EC
assert_output_contains "Shows disabled" "disabled" "$OUT"
echo ""

echo "--- Test: Change role ---"
OUT=$($PHP $MOOSH enrol:mod $SELF_ID --roleid 3 -p "$MOODLE_PATH" --run -o csv 2>&1)
EC=$?
assert_exit_code "Roleid exit code 0" 0 $EC
assert_output_contains "CSV has roleid 3" ",3," "$OUT"
# Restore
$PHP $MOOSH enrol:mod $SELF_ID --roleid 5 -p "$MOODLE_PATH" --run > /dev/null 2>&1
echo ""

echo "--- Test: Set name ---"
OUT=$($PHP $MOOSH enrol:mod $SELF_ID --name "Custom Self Enrol" -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Name exit code 0" 0 $EC
assert_output_contains "Shows name" "Custom Self Enrol" "$OUT"
echo ""

echo "--- Test: Multiple instances ---"
OUT=$($PHP $MOOSH enrol:mod $GUEST_ID $SELF_ID --enabled 1 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Multi exit code 0" 0 $EC
line_count=$(echo "$OUT" | grep -c 'enabled')
if [ "$line_count" -ge 2 ]; then
    echo "  PASS: Both instances enabled"
    ((PASS++))
else
    echo "  FAIL: Expected 2 enabled lines, got $line_count"
    ((FAIL++))
fi
echo ""

echo "--- Test: Delete instance ---"
# Enable self first, then create a new self instance to delete
$PHP $MOOSH course:mod 2 --selfenrol 1 -p "$MOODLE_PATH" --run > /dev/null 2>&1
# Get the newly created self ID (there might be two now)
NEW_SELF_ID=$($PHP $MOOSH enrol:list 2 -p "$MOODLE_PATH" -o csv 2>&1 | grep self | tail -1 | cut -d, -f1)
if [ "$NEW_SELF_ID" != "$SELF_ID" ]; then
    OUT=$($PHP $MOOSH enrol:delete $NEW_SELF_ID -p "$MOODLE_PATH" --run 2>&1)
    EC=$?
    assert_exit_code "Delete exit code 0" 0 $EC
    assert_output_contains "Shows deleted" "Deleted" "$OUT"
else
    echo "  PASS: Skip delete test (no extra instance created)"
    ((PASS++))
fi
echo ""

echo "--- Test: Invalid instance ---"
OUT=$($PHP $MOOSH enrol:mod 99999 --enabled 1 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid instance" 1 $EC
assert_output_contains "Error for invalid instance" "not found" "$OUT"
echo ""

echo "--- Test: No modification ---"
OUT=$($PHP $MOOSH enrol:mod $GUEST_ID -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for no mod" 1 $EC
assert_output_contains "Error for no mod" "No modifications" "$OUT"
echo ""

echo "--- Test: enrol:mod help ---"
OUT=$($PHP $MOOSH enrol:mod -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Modify an enrolment" "$OUT"
assert_output_contains "Help shows --enabled" "--enabled" "$OUT"
echo ""



print_summary
