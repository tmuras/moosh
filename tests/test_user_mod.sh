#!/usr/bin/env bash
#
# Integration tests for moosh2 user:mod command
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_user_mod.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 user:mod integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Test data summary:
#   62 users: admin, guest, student01..student50, teacher01..teacher10
#   admin is a site administrator
#   student/teacher users have default auth=manual
#   Roles: manager (system role), student, editingteacher, etc.

# ── Dry run ──────────────────────────────────────────────────────

echo "--- Test: Dry run shows changes ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student01 --email=new@example.com 2>&1)
echo "$OUT"
assert_output_contains "Dry run message" "Dry run" "$OUT"
assert_output_contains "Shows username" "student01" "$OUT"
assert_output_contains "Shows email change" "email: new@example.com" "$OUT"
echo ""

# Verify no change was made
echo "--- Test: Dry run did not modify data ---"
EMAIL=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$u = \$DB->get_record('user', ['username' => 'student01']);
echo \$u->email;
" 2>/dev/null)
if [ "$EMAIL" != "new@example.com" ]; then
    echo "  PASS: Email unchanged after dry run"
    ((PASS++))
else
    echo "  FAIL: Email was changed during dry run"
    ((FAIL++))
fi
echo ""

# ── Email change ─────────────────────────────────────────────────

echo "--- Test: Change email with --run ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student01 --email=modified@example.com --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Output shows student01" "student01" "$OUT"
assert_output_contains "Output shows new email" "modified@example.com" "$OUT"
echo ""

# ── Auth change ──────────────────────────────────────────────────

echo "--- Test: Change auth ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student01 --auth=ldap --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Output shows ldap auth" "ldap" "$OUT"
echo ""

# Reset back
$PHP $MOOSH user:mod -p "$MOODLE_PATH" student01 --auth=manual --run >/dev/null 2>&1

# ── Firstname and lastname ───────────────────────────────────────

echo "--- Test: Change firstname and lastname ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student01 --firstname=John --lastname=Smith --run -o json 2>&1)
echo "$OUT"
assert_output_contains "JSON has student01" '"username": "student01"' "$OUT"
echo ""

# Verify in DB
FULLNAME=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$u = \$DB->get_record('user', ['username' => 'student01']);
echo \$u->firstname . ' ' . \$u->lastname;
" 2>/dev/null)
if [ "$FULLNAME" = "John Smith" ]; then
    echo "  PASS: Name changed to John Smith"
    ((PASS++))
else
    echo "  FAIL: Expected 'John Smith', got '$FULLNAME'"
    ((FAIL++))
fi
echo ""

# ── City and country ─────────────────────────────────────────────

echo "--- Test: Change city and country ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student01 --city=London --country=GB --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Mod output has student01" "student01" "$OUT"
echo ""

CITY=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$u = \$DB->get_record('user', ['username' => 'student01']);
echo \$u->city;
" 2>/dev/null)
if [ "$CITY" = "London" ]; then
    echo "  PASS: City set to London"
    ((PASS++))
else
    echo "  FAIL: Expected London, got '$CITY'"
    ((FAIL++))
fi
echo ""

# ── Suspended ────────────────────────────────────────────────────

echo "--- Test: Suspend user ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student02 --suspended=1 --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Suspended shows 1" ",1" "$OUT"
echo ""

echo "--- Test: Unsuspend user ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student02 --suspended=0 --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Unsuspended shows 0" ",0" "$OUT"
echo ""

echo "--- Test: Invalid suspended value ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student01 --suspended=maybe --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Invalid suspended value returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error mentions invalid" "Invalid" "$OUT"
echo ""

# ── Password ─────────────────────────────────────────────────────

echo "--- Test: Change password ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student01 --password='NewPass123!' --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Password change output" "student01" "$OUT"
echo ""

echo "--- Test: Weak password rejected ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student01 --password=weak --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Weak password returns failure" 1 "$EXIT_CODE"
echo ""

echo "--- Test: Weak password with --ignore-policy ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student01 --password=weak --ignore-policy --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Ignore policy worked" "student01" "$OUT"
echo ""

# ── Username change ──────────────────────────────────────────────

echo "--- Test: Change username ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student03 --username=renamed_student --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "New username in output" "renamed_student" "$OUT"
echo ""

# Rename back
$PHP $MOOSH user:mod -p "$MOODLE_PATH" renamed_student --username=student03 --run >/dev/null 2>&1

echo "--- Test: Username change rejects multiple users ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student01 student02 --username=newname --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Username with multiple users returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error mentions single user" "single user" "$OUT"
echo ""

# ── Lookup by ID ─────────────────────────────────────────────────

echo "--- Test: Modify by user ID ---"
STUDENT_ID=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$u = \$DB->get_record('user', ['username' => 'student04']);
echo \$u->id;
" 2>/dev/null)
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" "$STUDENT_ID" --city=Berlin --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "ID lookup shows student04" "student04" "$OUT"
echo ""

# ── Multiple users ───────────────────────────────────────────────

echo "--- Test: Modify multiple users ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student05 student06 --city=Paris --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Multi-mod has student05" "student05" "$OUT"
assert_output_contains "Multi-mod has student06" "student06" "$OUT"
echo ""

# ── System role assignment ───────────────────────────────────────

echo "--- Test: Assign system role ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student01 --assign-role=manager --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Assign role output" "student01" "$OUT"
echo ""

# Verify role assigned
ASSIGNED=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$u = \$DB->get_record('user', ['username' => 'student01']);
\$r = \$DB->get_record('role', ['shortname' => 'manager']);
\$ctx = context_system::instance();
echo \$DB->count_records('role_assignments', ['userid' => \$u->id, 'roleid' => \$r->id, 'contextid' => \$ctx->id]);
" 2>/dev/null)
if [ "$ASSIGNED" = "1" ]; then
    echo "  PASS: Manager role assigned to student01"
    ((PASS++))
else
    echo "  FAIL: Expected 1 assignment, got $ASSIGNED"
    ((FAIL++))
fi
echo ""

# ── System role unassignment ─────────────────────────────────────

echo "--- Test: Unassign system role ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student01 --unassign-role=manager --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Unassign role output" "student01" "$OUT"
echo ""

# Verify role unassigned
ASSIGNED=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$u = \$DB->get_record('user', ['username' => 'student01']);
\$r = \$DB->get_record('role', ['shortname' => 'manager']);
\$ctx = context_system::instance();
echo \$DB->count_records('role_assignments', ['userid' => \$u->id, 'roleid' => \$r->id, 'contextid' => \$ctx->id]);
" 2>/dev/null)
if [ "$ASSIGNED" = "0" ]; then
    echo "  PASS: Manager role unassigned from student01"
    ((PASS++))
else
    echo "  FAIL: Expected 0 assignments, got $ASSIGNED"
    ((FAIL++))
fi
echo ""

echo "--- Test: Assign non-system role fails ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student01 --assign-role=student --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Non-system role returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error mentions not system role" "not a system role" "$OUT"
echo ""

echo "--- Test: Assign nonexistent role fails ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student01 --assign-role=nonexistentrole --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Nonexistent role returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error mentions not found" "not found" "$OUT"
echo ""

# ── Global admin ─────────────────────────────────────────────────

echo "--- Test: Make user global admin ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student01 --global-admin --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Global admin output" "student01" "$OUT"
echo ""

# Verify admin status
IS_ADMIN=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
\$admins = explode(',', \$CFG->siteadmins);
\$u = \$DB->get_record('user', ['username' => 'student01']);
echo in_array(\$u->id, \$admins) ? 'YES' : 'NO';
" 2>/dev/null)
if [ "$IS_ADMIN" = "YES" ]; then
    echo "  PASS: student01 is now a site admin"
    ((PASS++))
else
    echo "  FAIL: student01 is not a site admin"
    ((FAIL++))
fi
echo ""

echo "--- Test: Remove global admin ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student01 --remove-global-admin --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Remove admin output" "student01" "$OUT"
echo ""

IS_ADMIN=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
\$admins = explode(',', \$CFG->siteadmins);
\$u = \$DB->get_record('user', ['username' => 'student01']);
echo in_array(\$u->id, \$admins) ? 'YES' : 'NO';
" 2>/dev/null)
if [ "$IS_ADMIN" = "NO" ]; then
    echo "  PASS: student01 is no longer a site admin"
    ((PASS++))
else
    echo "  FAIL: student01 is still a site admin"
    ((FAIL++))
fi
echo ""

# ── Error handling ───────────────────────────────────────────────

echo "--- Test: No modifications error ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" student01 --run 2>&1)
EXIT_CODE=$?
assert_exit_code "No mods returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error mentions no modifications" "No modifications" "$OUT"
echo ""

echo "--- Test: Nonexistent user ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" nonexistent_user_xyz --email=x@x.com --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Nonexistent user returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error mentions not found" "not found" "$OUT"
echo ""

# ── Help & aliases ───────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH user:mod -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help shows description" "Modify" "$OUT"
assert_output_contains "Help shows --email" "--email" "$OUT"
assert_output_contains "Help shows --password" "--password" "$OUT"
assert_output_contains "Help shows --assign-role" "--assign-role" "$OUT"
assert_output_contains "Help shows --unassign-role" "--unassign-role" "$OUT"
assert_output_contains "Help shows --global-admin" "--global-admin" "$OUT"
assert_output_contains "Help shows --suspended" "--suspended" "$OUT"
echo ""




print_summary
