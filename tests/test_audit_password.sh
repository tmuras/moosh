#!/usr/bin/env bash
#
# Integration test for moosh2 audit:password command
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_audit_password.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 audit:password integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Step 2: Create a user with a weak password
echo "--- Creating test user with weak password ---"
$PHP $MOOSH user:create -p "$MOODLE_PATH" --run --password "Abc123!@" weakuser1 -o csv > /dev/null
WEAK_ID=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --sql "u.username = 'weakuser1'" -i)
echo "Created weakuser1 with ID $WEAK_ID"

# Set password to "password" directly (bypassing policy)
$PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$user = \$DB->get_record('user', ['username' => 'weakuser1']);
\$user->password = hash_internal_user_password('password');
\$DB->update_record('user', \$user);
" 2>/dev/null
echo "Set weakuser1 password to 'password'"
echo ""

# ── Detect weak password by userid ───────────────────────────────

echo "--- Test: Detect weak password by userid ---"
OUT=$($PHP $MOOSH audit:password -p "$MOODLE_PATH" --userid $WEAK_ID -o csv)
echo "$OUT"
assert_output_contains "Header row" "id,username" "$OUT"
assert_output_contains "Weak user detected" "weakuser1" "$OUT"
echo ""

# ── Reveal password ───────────────────────────────────────────────

echo "--- Test: Reveal password ---"
OUT=$($PHP $MOOSH audit:password -p "$MOODLE_PATH" --userid $WEAK_ID --reveal -o csv)
echo "$OUT"
assert_output_contains "Reveal header" "id,username,password" "$OUT"
assert_output_contains "Password revealed" "password" "$OUT"
echo ""

# ── Strong password not flagged ───────────────────────────────────

echo "--- Test: Admin (strong password) not flagged ---"
OUT=$($PHP $MOOSH audit:password -p "$MOODLE_PATH" --userid 2 -o csv)
echo "$OUT"
assert_output_not_contains "Admin not in results" "admin" "$OUT"
echo ""

# ── JSON output ───────────────────────────────────────────────────

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH audit:password -p "$MOODLE_PATH" --userid $WEAK_ID -o json)
echo "$OUT"
assert_output_contains "JSON has id" '"id"' "$OUT"
assert_output_contains "JSON has username" '"username"' "$OUT"
assert_output_contains "JSON has weakuser1" '"weakuser1"' "$OUT"
echo ""

# ── JSON with reveal ──────────────────────────────────────────────

echo "--- Test: JSON with reveal ---"
OUT=$($PHP $MOOSH audit:password -p "$MOODLE_PATH" --userid $WEAK_ID --reveal -o json)
echo "$OUT"
assert_output_contains "JSON has password key" '"password"' "$OUT"
assert_output_contains "JSON password value" '"password"' "$OUT"
echo ""

# ── Table output ──────────────────────────────────────────────────

echo "--- Test: Table output ---"
OUT=$($PHP $MOOSH audit:password -p "$MOODLE_PATH" --userid $WEAK_ID)
echo "$OUT"
assert_output_contains "Table has id" "id" "$OUT"
assert_output_contains "Table has username" "username" "$OUT"
assert_output_contains "Table has weakuser1" "weakuser1" "$OUT"
echo ""

# ── Help output ───────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH audit:password -p "$MOODLE_PATH" --help)
assert_output_contains "Help shows description" "Audit user passwords" "$OUT"
assert_output_contains "Help shows --reveal" "--reveal" "$OUT"
assert_output_contains "Help shows --userid" "--userid" "$OUT"
echo ""

# ── audit-password alias ──────────────────────────────────────────


print_summary
