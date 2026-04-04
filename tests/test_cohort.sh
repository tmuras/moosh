#!/usr/bin/env bash
#
# Integration tests for moosh2 cohort commands:
#   cohort:create, cohort:list, cohort:mod, cohort:enrol, cohort:unenrol
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_cohort.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 cohort commands integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

TMPDIR=$(mktemp -d)
trap "rm -rf $TMPDIR" EXIT

# ═══════════════════════════════════════════════════════════════════
#  cohort:create
# ═══════════════════════════════════════════════════════════════════

echo "========== cohort:create =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH cohort:create "Test Cohort" -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Create cohort ---"
OUT=$($PHP $MOOSH cohort:create "Class 2025" -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Create exit code 0" 0 $EC
assert_output_contains "Shows name" "Class 2025" "$OUT"
COHORT_ID=$(echo "$OUT" | grep -oP '^\| \K[0-9]+' | head -1)
echo "  Created cohort ID: $COHORT_ID"
echo ""

echo "--- Test: Create with options ---"
OUT=$($PHP $MOOSH cohort:create "Faculty" --idnumber FAC01 --description "Faculty members" -p "$MOODLE_PATH" --run -o csv 2>&1)
EC=$?
assert_exit_code "Options create exit code 0" 0 $EC
assert_output_contains "CSV has Faculty" "Faculty" "$OUT"
assert_output_contains "CSV has FAC01" "FAC01" "$OUT"
COHORT_FAC_ID=$(echo "$OUT" | tail -1 | cut -d, -f1)
echo "  Faculty cohort ID: $COHORT_FAC_ID"
echo ""

echo "--- Test: Create in category ---"
OUT=$($PHP $MOOSH cohort:create "Cat Cohort" --category 2 -p "$MOODLE_PATH" --run -o csv 2>&1)
EC=$?
assert_exit_code "Category create exit code 0" 0 $EC
assert_output_contains "CSV has Cat Cohort" "Cat Cohort" "$OUT"
echo ""

echo "--- Test: Create multiple ---"
OUT=$($PHP $MOOSH cohort:create "Group A" "Group B" -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Multi create exit code 0" 0 $EC
assert_output_contains "Shows Group A" "Group A" "$OUT"
assert_output_contains "Shows Group B" "Group B" "$OUT"
echo ""

echo "--- Test: cohort:create help ---"
OUT=$($PHP $MOOSH cohort:create -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Create a cohort" "$OUT"
assert_output_contains "Help shows --idnumber" "--idnumber" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  cohort:list
# ═══════════════════════════════════════════════════════════════════

echo "========== cohort:list =========="
echo ""

echo "--- Test: List cohorts ---"
OUT=$($PHP $MOOSH cohort:list -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "List exit code 0" 0 $EC
assert_output_contains "Shows Class 2025" "Class 2025" "$OUT"
assert_output_contains "Shows Faculty" "Faculty" "$OUT"
echo ""

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH cohort:list -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "CSV header" "id,name,idnumber,contextid,visible,members" "$OUT"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH cohort:list -p "$MOODLE_PATH" -o json 2>&1)
assert_output_contains "JSON has name" '"name": "Class 2025"' "$OUT"
echo ""

echo "--- Test: ID-only ---"
OUT=$($PHP $MOOSH cohort:list -p "$MOODLE_PATH" --id-only 2>&1)
assert_output_not_empty "ID-only not empty" "$OUT"
echo ""

echo "--- Test: cohort:list help ---"
OUT=$($PHP $MOOSH cohort:list -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "List cohorts" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  cohort:mod
# ═══════════════════════════════════════════════════════════════════

echo "========== cohort:mod =========="
echo ""

echo "--- Test: Mod dry run ---"
OUT=$($PHP $MOOSH cohort:mod $COHORT_ID --name "Renamed" -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Rename cohort ---"
OUT=$($PHP $MOOSH cohort:mod $COHORT_ID --name "Class 2025 Renamed" -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Rename exit code 0" 0 $EC
assert_output_contains "Shows renamed" "Class 2025 Renamed" "$OUT"
echo ""

echo "--- Test: Change idnumber ---"
OUT=$($PHP $MOOSH cohort:mod $COHORT_ID --idnumber CLS25 -p "$MOODLE_PATH" --run -o csv 2>&1)
EC=$?
assert_exit_code "Idnumber exit code 0" 0 $EC
assert_output_contains "CSV has CLS25" "CLS25" "$OUT"
echo ""

echo "--- Test: Add member by username ---"
OUT=$($PHP $MOOSH cohort:mod $COHORT_ID --add-member admin -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Add member exit code 0" 0 $EC
assert_output_contains "Shows added" "Added 1 member" "$OUT"
echo ""

echo "--- Test: Add member by user ID ---"
# Get a student user ID
STUDENT_ID=$($PHP $MOOSH sql:select -p "$MOODLE_PATH" "SELECT id FROM mdl_user WHERE username='student01'" -o csv 2>&1 | tail -1)
OUT=$($PHP $MOOSH cohort:mod $COHORT_ID --add-member $STUDENT_ID -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Add by ID exit code 0" 0 $EC
assert_output_contains "Shows added" "Added 1 member" "$OUT"
echo ""

echo "--- Test: Member count updated ---"
OUT=$($PHP $MOOSH cohort:mod $COHORT_ID --visible 1 -p "$MOODLE_PATH" --run -o csv 2>&1)
assert_output_contains "Shows 2 members" ",2" "$OUT"
echo ""

echo "--- Test: Remove member ---"
OUT=$($PHP $MOOSH cohort:mod $COHORT_ID --remove-member admin -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Remove member exit code 0" 0 $EC
assert_output_contains "Shows removed" "Removed 1 member" "$OUT"
echo ""

echo "--- Test: Import members from CSV ---"
cat > "$TMPDIR/members.csv" << 'CSVEOF'
username
student01
student02
student03
CSVEOF
OUT=$($PHP $MOOSH cohort:mod $COHORT_ID --import "$TMPDIR/members.csv" -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Import exit code 0" 0 $EC
assert_output_contains "Shows added members" "Added" "$OUT"
echo ""

echo "--- Test: Invalid user ---"
OUT=$($PHP $MOOSH cohort:mod $COHORT_ID --add-member nonexistentuser -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid user" 1 $EC
assert_output_contains "Error for invalid user" "not found" "$OUT"
echo ""

echo "--- Test: Invalid cohort ---"
OUT=$($PHP $MOOSH cohort:mod 99999 --name "Test" -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid cohort" 1 $EC
assert_output_contains "Error for invalid cohort" "not found" "$OUT"
echo ""

echo "--- Test: No modification ---"
OUT=$($PHP $MOOSH cohort:mod $COHORT_ID -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for no mod" 1 $EC
assert_output_contains "Error for no mod" "No modifications" "$OUT"
echo ""

echo "--- Test: cohort:delete ---"
DEL_OUT=$($PHP $MOOSH cohort:create "ToDelete" -p "$MOODLE_PATH" --run -o csv 2>&1)
DEL_ID=$(echo "$DEL_OUT" | tail -1 | cut -d, -f1)
OUT=$($PHP $MOOSH cohort:delete $DEL_ID -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Delete exit code 0" 0 $EC
assert_output_contains "Shows deleted" "Deleted" "$OUT"
echo ""

echo "--- Test: cohort:mod help ---"
OUT=$($PHP $MOOSH cohort:mod -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Modify a cohort" "$OUT"
assert_output_contains "Help shows --add-member" "--add-member" "$OUT"
assert_output_contains "Help shows --import" "--import" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  cohort:enrol
# ═══════════════════════════════════════════════════════════════════

echo "========== cohort:enrol =========="
echo ""

echo "--- Test: Enrol dry run ---"
OUT=$($PHP $MOOSH cohort:enrol $COHORT_FAC_ID 2 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows cohort name" "Faculty" "$OUT"
echo ""

echo "--- Test: Enrol cohort to course ---"
OUT=$($PHP $MOOSH cohort:enrol $COHORT_FAC_ID 2 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Enrol exit code 0" 0 $EC
assert_output_contains "Shows Faculty" "Faculty" "$OUT"
assert_output_contains "Shows student role" "student" "$OUT"
echo ""

echo "--- Test: Duplicate enrol ---"
OUT=$($PHP $MOOSH cohort:enrol $COHORT_FAC_ID 2 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for duplicate" 1 $EC
assert_output_contains "Error for duplicate" "already synced" "$OUT"
echo ""

echo "--- Test: Enrol with role ---"
OUT=$($PHP $MOOSH cohort:enrol $COHORT_FAC_ID 3 --role editingteacher -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Role enrol exit code 0" 0 $EC
assert_output_contains "Shows teacher role" "editingteacher" "$OUT"
echo ""

echo "--- Test: Invalid cohort ---"
OUT=$($PHP $MOOSH cohort:enrol 99999 2 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid cohort" 1 $EC
echo ""

echo "--- Test: Invalid course ---"
OUT=$($PHP $MOOSH cohort:enrol $COHORT_FAC_ID 999 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid course" 1 $EC
echo ""

echo "--- Test: Invalid role ---"
OUT=$($PHP $MOOSH cohort:enrol $COHORT_FAC_ID 4 --role nonexistent -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid role" 1 $EC
assert_output_contains "Error for invalid role" "not found" "$OUT"
echo ""

echo "--- Test: cohort:enrol help ---"
OUT=$($PHP $MOOSH cohort:enrol -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Sync a cohort to a course" "$OUT"
assert_output_contains "Help shows --role" "--role" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  cohort:unenrol
# ═══════════════════════════════════════════════════════════════════

echo "========== cohort:unenrol =========="
echo ""

echo "--- Test: Unenrol dry run ---"
OUT=$($PHP $MOOSH cohort:unenrol $COHORT_FAC_ID 2 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Unenrol cohort from course ---"
OUT=$($PHP $MOOSH cohort:unenrol $COHORT_FAC_ID 2 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Unenrol exit code 0" 0 $EC
assert_output_contains "Shows removed" "Removed" "$OUT"
echo ""

echo "--- Test: Unenrol with role filter ---"
OUT=$($PHP $MOOSH cohort:unenrol $COHORT_FAC_ID 3 --role editingteacher -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Role unenrol exit code 0" 0 $EC
assert_output_contains "Shows removed" "Removed" "$OUT"
echo ""

echo "--- Test: Unenrol nonexistent ---"
OUT=$($PHP $MOOSH cohort:unenrol $COHORT_FAC_ID 2 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for nonexistent" 1 $EC
assert_output_contains "Error for nonexistent" "No cohort enrolment found" "$OUT"
echo ""

echo "--- Test: cohort:unenrol help ---"
OUT=$($PHP $MOOSH cohort:unenrol -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Remove cohort enrolment sync" "$OUT"
echo ""


print_summary
