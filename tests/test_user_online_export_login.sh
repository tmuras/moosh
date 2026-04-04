#!/usr/bin/env bash
#
# Integration test for moosh2 user:online, user:export, user:login
# Requires a working Moodle 5.2 installation
#
# Usage: bash tests/test_user_online_export_login.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 user:online/export/login integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  user:online
# ═══════════════════════════════════════════════════════════════════

echo "========== user:online =========="
echo ""

echo "--- Test: Default output ---"
OUT=$($PHP $MOOSH user:online -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 0" 0 $EC
# May show users or "No users online" depending on test timing
assert_output_not_empty "Output not empty" "$OUT"
echo ""

echo "--- Test: With time window ---"
OUT=$($PHP $MOOSH user:online --time 86400 -p "$MOODLE_PATH" 2>&1)
# 24 hour window should catch recent admin activity
assert_output_contains "Shows username" "username" "$OUT"
echo ""

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH user:online --time 86400 -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "CSV header" "id,username" "$OUT"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH user:online --time 86400 -p "$MOODLE_PATH" -o json 2>&1)
assert_output_contains "JSON has username" '"username"' "$OUT"
echo ""

echo "--- Test: With limit ---"
OUT=$($PHP $MOOSH user:online --time 86400 --limit 1 -p "$MOODLE_PATH" -o csv 2>&1)
# Header + at most 1 data row
LINE_COUNT=$(echo "$OUT" | wc -l)
if [ "$LINE_COUNT" -le 2 ]; then
    echo "  PASS: Limit respected ($LINE_COUNT lines)"
    ((PASS++))
else
    echo "  FAIL: Expected at most 2 lines, got $LINE_COUNT"
    ((FAIL++))
fi
echo ""

echo "--- Test: No users in tiny window ---"
OUT=$($PHP $MOOSH user:online --time 0 -p "$MOODLE_PATH" 2>&1)
assert_output_contains "No users" "No users" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH user:online -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "currently online" "$OUT"
assert_output_contains "Help shows --time" "--time" "$OUT"
assert_output_contains "Help shows --limit" "--limit" "$OUT"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  user:export
# ═══════════════════════════════════════════════════════════════════

echo "========== user:export =========="
echo ""

echo "--- Test: Export all to stdout ---"
OUT=$($PHP $MOOSH user:export -p "$MOODLE_PATH" 2>&1)
assert_output_contains "CSV header" "id,username,email" "$OUT"
assert_output_contains "Has admin" "admin" "$OUT"
echo ""

echo "--- Test: Export to file ---"
TMPFILE=$(mktemp /tmp/moosh_users_XXXXXX.csv)
OUT=$($PHP $MOOSH user:export -p "$MOODLE_PATH" "$TMPFILE" 2>&1)
assert_output_contains "Shows exported" "Exported" "$OUT"
CONTENT=$(head -1 "$TMPFILE")
assert_output_contains "File has header" "id,username" "$CONTENT"
LINE_COUNT=$(wc -l < "$TMPFILE")
if [ "$LINE_COUNT" -gt 2 ]; then
    echo "  PASS: File has $LINE_COUNT lines"
    ((PASS++))
else
    echo "  FAIL: Expected more than 2 lines, got $LINE_COUNT"
    ((FAIL++))
fi
rm -f "$TMPFILE"
echo ""

echo "--- Test: Export single user by username ---"
OUT=$($PHP $MOOSH user:export --userid admin -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Has admin row" "admin" "$OUT"
LINE_COUNT=$(echo "$OUT" | wc -l)
if [ "$LINE_COUNT" -eq 2 ]; then
    echo "  PASS: Single user (header + 1 row)"
    ((PASS++))
else
    echo "  FAIL: Expected 2 lines, got $LINE_COUNT"
    ((FAIL++))
fi
echo ""

echo "--- Test: Export single user by ID ---"
OUT=$($PHP $MOOSH user:export --userid 2 --by-id -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Has admin by ID" "admin" "$OUT"
echo ""

echo "--- Test: Export course users ---"
OUT=$($PHP $MOOSH user:export --course 2 -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Course export header" "id,username" "$OUT"
echo ""

echo "--- Test: Nonexistent user ---"
OUT=$($PHP $MOOSH user:export --userid nonexistent999 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for nonexistent" 1 $EC
assert_output_contains "Not found" "not found" "$OUT"
echo ""

echo "--- Test: Nonexistent course ---"
OUT=$($PHP $MOOSH user:export --course 99999 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for nonexistent course" 1 $EC
assert_output_contains "Course not found" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH user:export -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Export users" "$OUT"
assert_output_contains "Help shows --userid" "--userid" "$OUT"
assert_output_contains "Help shows --course" "--course" "$OUT"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  user:login
# ═══════════════════════════════════════════════════════════════════

echo "========== user:login =========="
echo ""

echo "--- Test: Login as admin ---"
OUT=$($PHP $MOOSH user:login admin -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Login exit code 0" 0 $EC
# Output should contain session_name:session_id format
echo "$OUT" | grep -qE '^[A-Za-z]+:.+'
if [ $? -eq 0 ]; then
    echo "  PASS: Session cookie format"
    ((PASS++))
else
    echo "  FAIL: Expected cookie format, got: $OUT"
    ((FAIL++))
fi
echo ""

echo "--- Test: Login by ID ---"
OUT=$($PHP $MOOSH user:login --id 2 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Login by ID exit code 0" 0 $EC
assert_output_not_empty "Session not empty" "$OUT"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH user:login admin -p "$MOODLE_PATH" -o json 2>&1)
assert_output_contains "JSON has cookie_name" '"cookie_name"' "$OUT"
assert_output_contains "JSON has cookie_value" '"cookie_value"' "$OUT"
echo ""

echo "--- Test: Nonexistent user ---"
OUT=$($PHP $MOOSH user:login nonexistent999 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for nonexistent" 1 $EC
assert_output_contains "Not found" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH user:login -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "login session" "$OUT"
assert_output_contains "Help shows --id" "--id" "$OUT"
echo ""

print_summary
