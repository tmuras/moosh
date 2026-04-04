#!/usr/bin/env bash
#
# Integration tests for moosh2 group and grouping commands
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_group.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 group/grouping commands integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  group:create
# ═══════════════════════════════════════════════════════════════════

echo "========== group:create =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH group:create "Group A" 2 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Create group ---"
OUT=$($PHP $MOOSH group:create "Group A" 2 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Create exit code 0" 0 $EC
assert_output_contains "Shows name" "Group A" "$OUT"
GRP_A_ID=$(echo "$OUT" | grep -oP '^\| \K[0-9]+' | head -1)
echo "  Group A ID: $GRP_A_ID"
echo ""

echo "--- Test: Create with options ---"
OUT=$($PHP $MOOSH group:create "Group B" 2 --idnumber GRPB --enrolmentkey secret -p "$MOODLE_PATH" --run -o csv 2>&1)
EC=$?
assert_exit_code "Options create exit code 0" 0 $EC
assert_output_contains "CSV has GRPB" "GRPB" "$OUT"
GRP_B_ID=$(echo "$OUT" | tail -1 | cut -d, -f1)
echo "  Group B ID: $GRP_B_ID"
echo ""

echo "--- Test: Create multiple ---"
OUT=$($PHP $MOOSH group:create "Group C" "Group D" 2 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Multi create exit code 0" 0 $EC
assert_output_contains "Shows Group C" "Group C" "$OUT"
assert_output_contains "Shows Group D" "Group D" "$OUT"
echo ""

echo "--- Test: Invalid course ---"
OUT=$($PHP $MOOSH group:create "Test" 999 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid course" 1 $EC
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH group:create -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Create a group" "$OUT"
assert_output_contains "Help shows --visibility" "--visibility" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  group:list
# ═══════════════════════════════════════════════════════════════════

echo "========== group:list =========="
echo ""

echo "--- Test: List groups ---"
OUT=$($PHP $MOOSH group:list 2 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "List exit code 0" 0 $EC
assert_output_contains "Shows Group A" "Group A" "$OUT"
assert_output_contains "Shows Group B" "Group B" "$OUT"
echo ""

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH group:list 2 -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "CSV header" "id,name,idnumber,visibility,members" "$OUT"
echo ""

echo "--- Test: ID-only ---"
OUT=$($PHP $MOOSH group:list 2 -p "$MOODLE_PATH" --id-only 2>&1)
assert_output_not_empty "ID-only not empty" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH group:list -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "List groups" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  group:mod
# ═══════════════════════════════════════════════════════════════════

echo "========== group:mod =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH group:mod $GRP_A_ID --name "Renamed" -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Rename ---"
OUT=$($PHP $MOOSH group:mod $GRP_A_ID --name "Group A Renamed" -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Rename exit code 0" 0 $EC
assert_output_contains "Shows renamed" "Group A Renamed" "$OUT"
echo ""

echo "--- Test: Change idnumber ---"
OUT=$($PHP $MOOSH group:mod $GRP_A_ID --idnumber GRPA -p "$MOODLE_PATH" --run -o csv 2>&1)
EC=$?
assert_exit_code "Idnumber exit code 0" 0 $EC
assert_output_contains "CSV has GRPA" "GRPA" "$OUT"
echo ""

echo "--- Test: Add member ---"
OUT=$($PHP $MOOSH group:mod $GRP_A_ID --add-member student01 --add-member student02 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Add member exit code 0" 0 $EC
assert_output_contains "Shows added" "Added 2 member" "$OUT"
echo ""

echo "--- Test: Remove member ---"
# Re-add admin first since empty test will clear all members
$PHP $MOOSH group:mod $GRP_A_ID --add-member student01 -p "$MOODLE_PATH" --run > /dev/null 2>&1
OUT=$($PHP $MOOSH group:mod $GRP_A_ID --remove-member student01 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Remove member exit code 0" 0 $EC
assert_output_contains "Shows removed" "Removed 1 member" "$OUT"
echo ""

echo "--- Test: Empty group ---"
OUT=$($PHP $MOOSH group:mod $GRP_A_ID --empty -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Empty exit code 0" 0 $EC
assert_output_contains "Shows removed all" "Removed" "$OUT"
echo ""

echo "--- Test: Invalid group ---"
OUT=$($PHP $MOOSH group:mod 99999 --name "Test" -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid group" 1 $EC
echo ""

echo "--- Test: No modification ---"
OUT=$($PHP $MOOSH group:mod $GRP_A_ID -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for no mod" 1 $EC
echo ""

echo "--- Test: group:delete ---"
DEL_OUT=$($PHP $MOOSH group:create "ToDelete" 2 -p "$MOODLE_PATH" --run -o csv 2>&1)
DEL_ID=$(echo "$DEL_OUT" | tail -1 | cut -d, -f1)
OUT=$($PHP $MOOSH group:delete $DEL_ID -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Delete exit code 0" 0 $EC
assert_output_contains "Shows deleted" "Deleted" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH group:mod -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help shows --add-member" "--add-member" "$OUT"
assert_output_contains "Help shows --empty" "--empty" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  grouping:create
# ═══════════════════════════════════════════════════════════════════

echo "========== grouping:create =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH grouping:create "Lab Groups" 2 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Create grouping ---"
OUT=$($PHP $MOOSH grouping:create "Lab Groups" 2 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Create exit code 0" 0 $EC
assert_output_contains "Shows name" "Lab Groups" "$OUT"
GRPING_ID=$(echo "$OUT" | grep -oP '^\| \K[0-9]+' | head -1)
echo "  Grouping ID: $GRPING_ID"
echo ""

echo "--- Test: Create with idnumber ---"
OUT=$($PHP $MOOSH grouping:create "Exam Groups" 2 --idnumber EXAM -p "$MOODLE_PATH" --run -o csv 2>&1)
EC=$?
assert_exit_code "Idnumber create exit code 0" 0 $EC
assert_output_contains "CSV has EXAM" "EXAM" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH grouping:create -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Create a grouping" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  grouping:list
# ═══════════════════════════════════════════════════════════════════

echo "========== grouping:list =========="
echo ""

echo "--- Test: List groupings ---"
OUT=$($PHP $MOOSH grouping:list 2 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "List exit code 0" 0 $EC
assert_output_contains "Shows Lab Groups" "Lab Groups" "$OUT"
assert_output_contains "Shows Exam Groups" "Exam Groups" "$OUT"
echo ""

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH grouping:list 2 -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "CSV header" "id,name,idnumber,groups" "$OUT"
echo ""

echo "--- Test: ID-only ---"
OUT=$($PHP $MOOSH grouping:list 2 -p "$MOODLE_PATH" --id-only 2>&1)
assert_output_not_empty "ID-only not empty" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH grouping:list -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "List groupings" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  grouping:mod
# ═══════════════════════════════════════════════════════════════════

echo "========== grouping:mod =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH grouping:mod $GRPING_ID --name "Renamed" -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Rename ---"
OUT=$($PHP $MOOSH grouping:mod $GRPING_ID --name "Lab Sessions" -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Rename exit code 0" 0 $EC
assert_output_contains "Shows renamed" "Lab Sessions" "$OUT"
echo ""

echo "--- Test: Assign groups ---"
OUT=$($PHP $MOOSH grouping:mod $GRPING_ID --add-group $GRP_A_ID --add-group $GRP_B_ID -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Assign exit code 0" 0 $EC
assert_output_contains "Shows assigned" "Assigned 2 group" "$OUT"
echo ""

echo "--- Test: Group count ---"
OUT=$($PHP $MOOSH grouping:list 2 -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "Shows 2 groups" ",2" "$OUT"
echo ""

echo "--- Test: Unassign group ---"
OUT=$($PHP $MOOSH grouping:mod $GRPING_ID --remove-group $GRP_B_ID -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Unassign exit code 0" 0 $EC
assert_output_contains "Shows unassigned" "Unassigned 1 group" "$OUT"
echo ""

echo "--- Test: Invalid grouping ---"
OUT=$($PHP $MOOSH grouping:mod 99999 --name "Test" -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid grouping" 1 $EC
echo ""

echo "--- Test: No modification ---"
OUT=$($PHP $MOOSH grouping:mod $GRPING_ID -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for no mod" 1 $EC
echo ""

echo "--- Test: grouping:delete ---"
DEL_OUT=$($PHP $MOOSH grouping:create "ToDelete" 2 -p "$MOODLE_PATH" --run -o csv 2>&1)
DEL_ID=$(echo "$DEL_OUT" | tail -1 | cut -d, -f1)
OUT=$($PHP $MOOSH grouping:delete $DEL_ID -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Delete exit code 0" 0 $EC
assert_output_contains "Shows deleted" "Deleted" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH grouping:mod -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help shows --add-group" "--add-group" "$OUT"
assert_output_contains "Help shows --remove-group" "--remove-group" "$OUT"
echo ""


print_summary
