#!/usr/bin/env bash
#
# Integration test for moosh2 user:delete and course:delete
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_user_course_delete.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 user:delete and course:delete integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ═══════════════════════════════════════════════════════════════════
# user:delete
# ═══════════════════════════════════════════════════════════════════

echo "========== user:delete =========="
echo ""

echo "--- Test: Dry run by username ---"
OUT=$($PHP $MOOSH user:delete -p "$MOODLE_PATH" student01 2>&1)
echo "$OUT"
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows username" "student01" "$OUT"
assert_output_contains "Shows email" "student01@example" "$OUT"
echo ""

echo "--- Test: Dry run by ID ---"
OUT=$($PHP $MOOSH user:delete -p "$MOODLE_PATH" --id 3 2>&1)
assert_output_contains "Shows dry run by ID" "Dry run" "$OUT"
assert_output_contains "Shows ID=3" "ID=3" "$OUT"
echo ""

echo "--- Test: Delete single user by username ---"
OUT=$($PHP $MOOSH user:delete -p "$MOODLE_PATH" --run student01 2>&1)
echo "$OUT"
assert_output_contains "Shows deleted" "Deleted user" "$OUT"
assert_output_contains "Shows username" "student01" "$OUT"
# Verify user is deleted
OUT=$($PHP $MOOSH user:delete -p "$MOODLE_PATH" student01 2>&1)
EXIT_CODE=$?
assert_exit_code "Already deleted user returns error" 1 "$EXIT_CODE"
echo ""

echo "--- Test: Delete multiple users by ID ---"
OUT=$($PHP $MOOSH user:delete -p "$MOODLE_PATH" --id --run 4 5 2>&1)
assert_output_contains "Deleted student02" "student02" "$OUT"
assert_output_contains "Deleted student03" "student03" "$OUT"
echo ""

echo "--- Test: Cannot delete admin ---"
OUT=$($PHP $MOOSH user:delete -p "$MOODLE_PATH" admin 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for admin" 1 "$EXIT_CODE"
assert_output_contains "Admin protection" "Cannot delete" "$OUT"
echo ""

echo "--- Test: Cannot delete guest ---"
OUT=$($PHP $MOOSH user:delete -p "$MOODLE_PATH" guest 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for guest" 1 "$EXIT_CODE"
assert_output_contains "Guest protection" "Cannot delete" "$OUT"
echo ""

echo "--- Test: Nonexistent user ---"
OUT=$($PHP $MOOSH user:delete -p "$MOODLE_PATH" nonexistentuser123 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for nonexistent" 1 "$EXIT_CODE"
assert_output_contains "Shows not found" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH user:delete -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Delete users" "$OUT"
assert_output_contains "Help shows --id" "--id" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
# course:delete
# ═══════════════════════════════════════════════════════════════════

echo "========== course:delete =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH course:delete -p "$MOODLE_PATH" 2 2>&1)
echo "$OUT"
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows shortname" "algebrafundamentals" "$OUT"
assert_output_contains "Shows fullname" "Algebra Fundamentals" "$OUT"
echo ""

echo "--- Test: Dry run multiple ---"
OUT=$($PHP $MOOSH course:delete -p "$MOODLE_PATH" 2 3 2>&1)
assert_output_contains "Shows Algebra" "Algebra" "$OUT"
assert_output_contains "Shows Calculus" "Calculus" "$OUT"
echo ""

echo "--- Test: Delete single course ---"
OUT=$($PHP $MOOSH course:delete -p "$MOODLE_PATH" --run 2 2>&1)
assert_output_contains "Shows deleted" "Deleted course" "$OUT"
assert_output_contains "Shows shortname" "algebrafundamentals" "$OUT"
# Verify it's gone
OUT=$($PHP $MOOSH course:delete -p "$MOODLE_PATH" 2 2>&1)
EXIT_CODE=$?
assert_exit_code "Deleted course returns error" 1 "$EXIT_CODE"
echo ""

echo "--- Test: Delete multiple courses ---"
OUT=$($PHP $MOOSH course:delete -p "$MOODLE_PATH" --run 3 4 2>&1)
assert_output_contains "Deleted Calculus" "calculusi" "$OUT"
assert_output_contains "Deleted Statistics" "statisticsandprobabi" "$OUT"
echo ""

echo "--- Test: Cannot delete site course ---"
OUT=$($PHP $MOOSH course:delete -p "$MOODLE_PATH" 1 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for site course" 1 "$EXIT_CODE"
assert_output_contains "Site course protection" "Cannot delete" "$OUT"
echo ""

echo "--- Test: Nonexistent course ---"
OUT=$($PHP $MOOSH course:delete -p "$MOODLE_PATH" 99999 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for nonexistent" 1 "$EXIT_CODE"
assert_output_contains "Shows not found" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH course:delete -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Delete courses" "$OUT"
echo ""


print_summary
