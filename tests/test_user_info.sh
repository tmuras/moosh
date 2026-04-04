#!/usr/bin/env bash
#
# Integration test for moosh2 user:info command
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_user_info.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 user:info integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Test data:
#   guest (id=1), admin (id=2), student01 (id=3), teacher01 (id=53)
#   student01 enrolled in 10 courses via manual enrolment, role: student
#   teacher01 enrolled in 10 courses via manual enrolment, role: editingteacher

# ── Basic table output ────────────────────────────────────────────

echo "--- Test: Basic table output (admin) ---"
OUT=$($PHP $MOOSH user:info -p "$MOODLE_PATH" 2)
echo "$OUT"
assert_output_contains "Shows User ID" "User ID" "$OUT"
assert_output_contains "Shows Username" "admin" "$OUT"
assert_output_contains "Shows Email" "admin@example.com" "$OUT"
assert_output_contains "Shows Auth method" "Auth method" "$OUT"
assert_output_contains "Shows Confirmed" "Confirmed" "$OUT"
assert_output_contains "Shows Courses enrolled" "Courses enrolled" "$OUT"
assert_output_contains "Shows Log entries" "Log entries" "$OUT"
assert_output_contains "Shows Files uploaded" "Files uploaded" "$OUT"
assert_output_contains "Shows User preferences" "User preferences" "$OUT"
echo ""

# ── CSV output ────────────────────────────────────────────────────

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH user:info -p "$MOODLE_PATH" 2 -o csv)
echo "$OUT" | head -2
assert_output_contains "CSV has User ID header" '"User ID"' "$OUT"
assert_output_contains "CSV has Username header" 'Username' "$OUT"
assert_output_contains "CSV has Courses enrolled header" '"Courses enrolled"' "$OUT"
echo ""

# ── JSON output ───────────────────────────────────────────────────

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH user:info -p "$MOODLE_PATH" 2 -o json)
echo "$OUT" | head -10
assert_output_contains "JSON has User ID key" '"User ID"' "$OUT"
assert_output_contains "JSON has Username key" '"Username"' "$OUT"
assert_output_contains "JSON has admin value" '"admin"' "$OUT"
echo ""

# ── Student profile ──────────────────────────────────────────────

echo "--- Test: Student profile ---"
OUT=$($PHP $MOOSH user:info -p "$MOODLE_PATH" 3 -o json)
echo "$OUT" | head -15
assert_output_contains "Student username" '"student01"' "$OUT"
assert_output_contains "Student first name" '"Student"' "$OUT"
assert_output_contains "Student last name" '"User01"' "$OUT"
assert_output_contains "Student email" '"student01@example.invalid"' "$OUT"
assert_output_contains "Auth is manual" '"manual"' "$OUT"
echo ""

# ── Student enrolments ───────────────────────────────────────────

echo "--- Test: Student enrolments ---"
OUT=$($PHP $MOOSH user:info -p "$MOODLE_PATH" 3 -o json)
assert_output_contains "10 courses enrolled" '"Courses enrolled": 10' "$OUT"
assert_output_contains "Enrolments via manual" '"Enrolments via manual": 10' "$OUT"
echo ""

# ── Student role assignments ─────────────────────────────────────

echo "--- Test: Student role assignments ---"
OUT=$($PHP $MOOSH user:info -p "$MOODLE_PATH" 3 -o json)
assert_output_contains "10 total role assignments" '"Total role assignments": 10' "$OUT"
assert_output_contains "Assignments as student" '"Assignments as student": 10' "$OUT"
assert_output_contains "System roles none" '"System roles": "none"' "$OUT"
echo ""

# ── Teacher profile ──────────────────────────────────────────────

echo "--- Test: Teacher profile ---"
OUT=$($PHP $MOOSH user:info -p "$MOODLE_PATH" 53 -o json)
assert_output_contains "Teacher username" '"teacher01"' "$OUT"
assert_output_contains "Teacher 10 courses" '"Courses enrolled": 10' "$OUT"
assert_output_contains "Assignments as editingteacher" '"Assignments as editingteacher": 10' "$OUT"
echo ""

# ── Guest user ────────────────────────────────────────────────────

echo "--- Test: Guest user ---"
OUT=$($PHP $MOOSH user:info -p "$MOODLE_PATH" 1 -o json)
assert_output_contains "Guest username" '"guest"' "$OUT"
assert_output_contains "Guest 0 courses" '"Courses enrolled": 0' "$OUT"
assert_output_contains "Guest 0 role assignments" '"Total role assignments": 0' "$OUT"
assert_output_contains "Guest 0 group memberships" '"Group memberships": 0' "$OUT"
echo ""

# ── Admin log entries ─────────────────────────────────────────────

echo "--- Test: Admin log entries ---"
OUT=$($PHP $MOOSH user:info -p "$MOODLE_PATH" 2 -o json)
# Admin has log entries from setup
assert_output_contains "Admin has log entries" '"Log entries":' "$OUT"
assert_output_not_contains "Admin log entries not zero" '"Log entries": 0' "$OUT"
echo ""

# ── Login statistics ──────────────────────────────────────────────

echo "--- Test: Admin login statistics ---"
OUT=$($PHP $MOOSH user:info -p "$MOODLE_PATH" 2 -o json)
assert_output_contains "Has successful logins field" '"Successful logins (last 30 days)":' "$OUT"
assert_output_contains "Has failed logins field" '"Failed logins (last 30 days)":' "$OUT"
assert_output_not_contains "Admin successful logins not zero" '"Successful logins (last 30 days)": 0' "$OUT"
echo ""

echo "--- Test: Student login statistics ---"
OUT=$($PHP $MOOSH user:info -p "$MOODLE_PATH" 3 -o json)
assert_output_contains "Student 0 successful logins" '"Successful logins (last 30 days)": 0' "$OUT"
assert_output_contains "Student 0 failed logins" '"Failed logins (last 30 days)": 0' "$OUT"
echo ""

# ── Zero counters for student ─────────────────────────────────────

echo "--- Test: Zero counters for student ---"
OUT=$($PHP $MOOSH user:info -p "$MOODLE_PATH" 3 -o json)
assert_output_contains "0 forum posts" '"Forum posts": 0' "$OUT"
assert_output_contains "0 forum discussions" '"Forum discussions started": 0' "$OUT"
assert_output_contains "0 assignment submissions" '"Assignment submissions": 0' "$OUT"
assert_output_contains "0 graded items" '"Graded items": 0' "$OUT"
assert_output_contains "0 badges" '"Badges issued": 0' "$OUT"
assert_output_contains "0 messages sent" '"Messages sent": 0' "$OUT"
assert_output_contains "0 notifications" '"Notifications": 0' "$OUT"
echo ""

# ── Profile fields ────────────────────────────────────────────────

echo "--- Test: Profile fields ---"
OUT=$($PHP $MOOSH user:info -p "$MOODLE_PATH" 3 -o json)
assert_output_contains "Has Confirmed field" '"Confirmed":' "$OUT"
assert_output_contains "Has Suspended field" '"Suspended":' "$OUT"
assert_output_contains "Has Deleted field" '"Deleted":' "$OUT"
assert_output_contains "Has Language field" '"Language":' "$OUT"
assert_output_contains "Has Timezone field" '"Timezone":' "$OUT"
assert_output_contains "Has Time created field" '"Time created":' "$OUT"
assert_output_contains "Has Description length field" '"Description length":' "$OUT"
echo ""

# ── Invalid user ID ──────────────────────────────────────────────

echo "--- Test: Invalid user ID ---"
OUT=$($PHP $MOOSH user:info -p "$MOODLE_PATH" 999 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code is 1 for invalid user" 1 "$EXIT_CODE"
assert_output_contains "Error message for invalid user" "not found" "$OUT"
echo ""

# ── Missing userid argument ──────────────────────────────────────

echo "--- Test: Missing userid argument ---"
OUT=$($PHP $MOOSH user:info -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Error mentions missing argument" "userid" "$OUT"
echo ""

# ── Help output ───────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH user:info -p "$MOODLE_PATH" --help)
assert_output_contains "Help shows description" "Show detailed information about a user" "$OUT"
assert_output_contains "Help shows userid argument" "userid" "$OUT"
echo ""

# ── user-info alias ───────────────────────────────────────────────


print_summary
