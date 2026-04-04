#!/usr/bin/env bash
#
# Integration test for moosh2 auth:list, auth:info, auth:mod commands
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_auth.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 auth commands integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ═══════════════════════════════════════════════════════════════════
# auth:list
# ═══════════════════════════════════════════════════════════════════

echo "========== auth:list =========="
echo ""

echo "--- Test: Basic CSV listing ---"
OUT=$($PHP $MOOSH auth:list -p "$MOODLE_PATH" -o csv)
echo "$OUT"
assert_output_contains "Header row" "plugin,name,enabled,users,can_signup,can_change_password,is_internal" "$OUT"
assert_output_contains "Manual plugin listed" "manual" "$OUT"
assert_output_contains "Email plugin listed" "email" "$OUT"
assert_output_contains "LDAP plugin listed" "ldap" "$OUT"
assert_output_contains "Manual has 62 users" "manual,\"Manual accounts\",1,62" "$OUT"
echo ""

echo "--- Test: Enabled-only ---"
OUT=$($PHP $MOOSH auth:list -p "$MOODLE_PATH" --enabled-only -o csv)
echo "$OUT"
assert_output_contains "Manual is enabled" "manual" "$OUT"
assert_output_contains "Email is enabled" "email" "$OUT"
assert_output_not_contains "LDAP is not enabled" "ldap" "$OUT"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH auth:list -p "$MOODLE_PATH" -o json)
assert_output_contains "JSON has plugin key" '"plugin"' "$OUT"
assert_output_contains "JSON has manual" '"manual"' "$OUT"
echo ""

echo "--- Test: Table output ---"
OUT=$($PHP $MOOSH auth:list -p "$MOODLE_PATH")
assert_output_contains "Table has plugin header" "plugin" "$OUT"
assert_output_contains "Table has manual" "manual" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH auth:list -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "List authentication plugins" "$OUT"
assert_output_contains "Help shows --enabled-only" "--enabled-only" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
# auth:info
# ═══════════════════════════════════════════════════════════════════

echo "========== auth:info =========="
echo ""

echo "--- Test: Manual auth info ---"
OUT=$($PHP $MOOSH auth:info -p "$MOODLE_PATH" manual -o json)
echo "$OUT" | head -15
assert_output_contains "Plugin name" '"Plugin": "manual"' "$OUT"
assert_output_contains "Display name" '"Manual accounts"' "$OUT"
assert_output_contains "Enabled yes" '"Enabled": "yes"' "$OUT"
assert_output_contains "Can change password yes" '"Can change password": "yes"' "$OUT"
assert_output_contains "Is internal yes" '"Is internal": "yes"' "$OUT"
assert_output_contains "62 total users" '"Total users": 62' "$OUT"
assert_output_contains "0 suspended" '"Suspended users": 0' "$OUT"
assert_output_contains "Locked fields none" '"Locked fields": "none"' "$OUT"
echo ""

echo "--- Test: Email auth info ---"
OUT=$($PHP $MOOSH auth:info -p "$MOODLE_PATH" email -o json)
assert_output_contains "Email plugin" '"Plugin": "email"' "$OUT"
assert_output_contains "Can signup yes" '"Can signup": "yes"' "$OUT"
assert_output_contains "0 users" '"Total users": 0' "$OUT"
echo ""

echo "--- Test: Disabled auth info ---"
OUT=$($PHP $MOOSH auth:info -p "$MOODLE_PATH" ldap -o json)
assert_output_contains "LDAP disabled" '"Enabled": "no"' "$OUT"
echo ""

echo "--- Test: Table output ---"
OUT=$($PHP $MOOSH auth:info -p "$MOODLE_PATH" manual)
assert_output_contains "Table format" "Metric" "$OUT"
assert_output_contains "Table has Manual accounts" "Manual accounts" "$OUT"
echo ""

echo "--- Test: Invalid plugin ---"
OUT=$($PHP $MOOSH auth:info -p "$MOODLE_PATH" nonexistent 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for invalid plugin" 1 "$EXIT_CODE"
assert_output_contains "Error for invalid plugin" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH auth:info -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Show detailed information about an auth plugin" "$OUT"
assert_output_contains "Help shows plugin argument" "plugin" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
# auth:mod
# ═══════════════════════════════════════════════════════════════════

echo "========== auth:mod =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH auth:mod -p "$MOODLE_PATH" enable ldap)
echo "$OUT"
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows plugin" "ldap" "$OUT"
echo ""

echo "--- Test: Enable ldap ---"
OUT=$($PHP $MOOSH auth:mod -p "$MOODLE_PATH" --run enable ldap)
echo "$OUT"
assert_output_contains "Action applied" "enable" "$OUT"
assert_output_contains "LDAP in enabled list" "ldap" "$OUT"
# Verify via auth:list
VERIFY=$($PHP $MOOSH auth:list -p "$MOODLE_PATH" --enabled-only -o csv)
assert_output_contains "LDAP now enabled" "ldap" "$VERIFY"
echo ""

echo "--- Test: Enable already enabled ---"
OUT=$($PHP $MOOSH auth:mod -p "$MOODLE_PATH" --run enable ldap)
assert_output_contains "Already enabled" "already enabled" "$OUT"
echo ""

echo "--- Test: Move up ---"
# Current order: email, ldap. Move ldap up -> ldap, email
OUT=$($PHP $MOOSH auth:mod -p "$MOODLE_PATH" --run up ldap)
echo "$OUT"
assert_output_contains "Action up applied" "up" "$OUT"
# Verify order in output: ldap should be before email
assert_output_contains "Order shows ldap, email" "ldap, email" "$OUT"
echo ""

echo "--- Test: Move down ---"
OUT=$($PHP $MOOSH auth:mod -p "$MOODLE_PATH" --run down ldap)
echo "$OUT"
assert_output_contains "Action down applied" "down" "$OUT"
echo ""

echo "--- Test: Disable ldap ---"
OUT=$($PHP $MOOSH auth:mod -p "$MOODLE_PATH" --run disable ldap)
echo "$OUT"
assert_output_contains "Disable applied" "disable" "$OUT"
VERIFY=$($PHP $MOOSH auth:list -p "$MOODLE_PATH" --enabled-only -o csv)
assert_output_not_contains "LDAP now disabled" "ldap" "$VERIFY"
echo ""

echo "--- Test: Cannot modify manual ---"
OUT=$($PHP $MOOSH auth:mod -p "$MOODLE_PATH" --run disable manual 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for manual" 1 "$EXIT_CODE"
assert_output_contains "Cannot modify manual" "manual" "$OUT"
echo ""

echo "--- Test: Invalid action ---"
OUT=$($PHP $MOOSH auth:mod -p "$MOODLE_PATH" --run invalid ldap 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for invalid action" 1 "$EXIT_CODE"
assert_output_contains "Invalid action error" "Invalid action" "$OUT"
echo ""

echo "--- Test: Invalid plugin ---"
OUT=$($PHP $MOOSH auth:mod -p "$MOODLE_PATH" --run enable nonexistent 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for invalid plugin" 1 "$EXIT_CODE"
assert_output_contains "Plugin not found" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH auth:mod -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Enable, disable, or reorder" "$OUT"
echo ""


print_summary
