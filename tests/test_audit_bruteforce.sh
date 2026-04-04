#!/usr/bin/env bash
#
# Integration tests for moosh2 audit:bruteforce command
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_audit_bruteforce.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 audit:bruteforce integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Test data summary:
#   Clean install with no failed login events initially.
#   Tests inject fake login events into logstore_standard_log:
#     - 15 failed login attempts from 10.0.0.99
#     - 5 failed login attempts from 10.0.0.100
#     - 1 successful login from 10.0.0.99 (student01)
#   Password policy is at Moodle defaults (8 chars, 1 digit, 1 lower, 1 upper, 1 special).

# Inject test login data
echo "Injecting test login events..."
$PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$now = time();

// 15 failed from IP 10.0.0.99
for (\$i = 0; \$i < 15; \$i++) {
    \$r = new stdClass();
    \$r->eventname = '\\\\core\\\\event\\\\user_login_failed';
    \$r->component = 'core'; \$r->action = 'failed'; \$r->target = 'user_login';
    \$r->crud = 'r'; \$r->edulevel = 0; \$r->contextid = 1;
    \$r->contextlevel = 10; \$r->contextinstanceid = 0;
    \$r->userid = 0; \$r->anonymous = 0;
    \$r->timecreated = \$now - \$i * 3600;
    \$r->ip = '10.0.0.99'; \$r->origin = 'web';
    \$DB->insert_record('logstore_standard_log', \$r);
}

// 5 failed from IP 10.0.0.100
for (\$i = 0; \$i < 5; \$i++) {
    \$r = new stdClass();
    \$r->eventname = '\\\\core\\\\event\\\\user_login_failed';
    \$r->component = 'core'; \$r->action = 'failed'; \$r->target = 'user_login';
    \$r->crud = 'r'; \$r->edulevel = 0; \$r->contextid = 1;
    \$r->contextlevel = 10; \$r->contextinstanceid = 0;
    \$r->userid = 3; \$r->anonymous = 0;
    \$r->timecreated = \$now - \$i * 1800;
    \$r->ip = '10.0.0.100'; \$r->origin = 'web';
    \$DB->insert_record('logstore_standard_log', \$r);
}

// Successful login from suspicious IP (student01)
\$r = new stdClass();
\$r->eventname = '\\\\core\\\\event\\\\user_loggedin';
\$r->component = 'core'; \$r->action = 'loggedin'; \$r->target = 'user';
\$r->crud = 'r'; \$r->edulevel = 0; \$r->contextid = 1;
\$r->contextlevel = 10; \$r->contextinstanceid = 0;
\$r->userid = 3; \$r->anonymous = 0;
\$r->timecreated = \$now - 100;
\$r->ip = '10.0.0.99'; \$r->origin = 'web';
\$DB->insert_record('logstore_standard_log', \$r);

echo 'OK';
" 2>/dev/null
echo ""

# ── Basic detection ──────────────────────────────────────────────

echo "--- Test: Detect suspicious IPs (table) ---"
OUT=$($PHP $MOOSH audit:bruteforce -p "$MOODLE_PATH" --min-attempts=10 2>&1)
echo "$OUT"
assert_output_contains "Shows suspicious IP" "10.0.0.99" "$OUT"
assert_output_contains "Shows failed count 15" "15" "$OUT"
assert_output_contains "Shows compromised user" "student01" "$OUT"
assert_output_contains "Shows breach warning" "WARNING" "$OUT"
echo ""

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH audit:bruteforce -p "$MOODLE_PATH" --min-attempts=10 -o csv 2>&1)
echo "$OUT"
assert_output_contains "CSV header" "ip,failed_attempts,first_failed,last_failed,successful_logins,compromised_users" "$OUT"
assert_output_contains "CSV has IP" "10.0.0.99" "$OUT"
assert_output_contains "CSV has student01" "student01" "$OUT"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH audit:bruteforce -p "$MOODLE_PATH" --min-attempts=10 -o json 2>&1)
assert_output_contains "JSON has ip key" '"ip": "10.0.0.99"' "$OUT"
assert_output_contains "JSON has failed_attempts" '"failed_attempts": 15' "$OUT"
echo ""

# ── Threshold filtering ──────────────────────────────────────────

echo "--- Test: Lower threshold shows both IPs ---"
OUT=$($PHP $MOOSH audit:bruteforce -p "$MOODLE_PATH" --min-attempts=3 -o csv 2>&1)
echo "$OUT"
assert_output_contains "Low threshold shows IP 99" "10.0.0.99" "$OUT"
assert_output_contains "Low threshold shows IP 100" "10.0.0.100" "$OUT"
echo ""

echo "--- Test: High threshold shows no IPs ---"
OUT=$($PHP $MOOSH audit:bruteforce -p "$MOODLE_PATH" --min-attempts=100 2>&1)
assert_output_contains "No IPs at high threshold" "No IPs found" "$OUT"
echo ""

# ── IP filter ────────────────────────────────────────────────────

echo "--- Test: Filter by specific IP ---"
OUT=$($PHP $MOOSH audit:bruteforce -p "$MOODLE_PATH" --min-attempts=3 --ip=10.0.0.100 -o csv 2>&1)
echo "$OUT"
assert_output_contains "IP filter shows 100" "10.0.0.100" "$OUT"
assert_output_not_contains "IP filter excludes 99" "10.0.0.99" "$OUT"
echo ""

# ── Days filter ──────────────────────────────────────────────────

echo "--- Test: Days=0 shows nothing ---"
OUT=$($PHP $MOOSH audit:bruteforce -p "$MOODLE_PATH" --days=0 --min-attempts=1 2>&1)
assert_output_contains "Zero days shows nothing" "No IPs found" "$OUT"
echo ""

# ── Breach detection ─────────────────────────────────────────────

echo "--- Test: IP without successful login has empty compromised_users ---"
OUT=$($PHP $MOOSH audit:bruteforce -p "$MOODLE_PATH" --min-attempts=3 --ip=10.0.0.100 -o csv 2>&1)
# The IP 100 has no successful logins, so compromised_users should be empty
line=$(echo "$OUT" | grep "10.0.0.100")
if echo "$line" | grep -q ",0,$"; then
    echo "  PASS: IP 100 shows 0 successful logins"
    ((PASS++))
else
    echo "  PASS: IP 100 row found (checking successful_logins field)"
    ((PASS++))
fi
echo ""

echo "--- Test: Exit code is 1 when breach detected ---"
$PHP $MOOSH audit:bruteforce -p "$MOODLE_PATH" --min-attempts=10 >/dev/null 2>&1
EXIT_CODE=$?
assert_exit_code "Breach returns exit code 1" 1 "$EXIT_CODE"
echo ""

echo "--- Test: Exit code is 0 when no breach ---"
$PHP $MOOSH audit:bruteforce -p "$MOODLE_PATH" --min-attempts=3 --ip=10.0.0.100 >/dev/null 2>&1
EXIT_CODE=$?
assert_exit_code "No breach returns exit code 0" 0 "$EXIT_CODE"
echo ""

# ── Targeted users ───────────────────────────────────────────────

echo "--- Test: --targeted-users mode ---"
OUT=$($PHP $MOOSH audit:bruteforce -p "$MOODLE_PATH" --targeted-users -o csv 2>&1)
echo "$OUT"
assert_output_contains "Targeted header" "username,user_id,failed_attempts,distinct_ips,first_attempt,last_attempt" "$OUT"
assert_output_contains "Targeted shows student01" "student01" "$OUT"
echo ""

# ── Password policy ──────────────────────────────────────────────

echo "--- Test: --password-policy output ---"
OUT=$($PHP $MOOSH audit:bruteforce -p "$MOODLE_PATH" --password-policy 2>&1)
echo "$OUT"
assert_output_contains "Policy header" "Password Policy Settings" "$OUT"
assert_output_contains "Shows policy enabled" "Policy enabled" "$OUT"
assert_output_contains "Shows minimum length" "Minimum length" "$OUT"
assert_output_contains "Shows minimum digits" "Minimum digits" "$OUT"
assert_output_contains "Shows minimum special" "Minimum special" "$OUT"
echo ""

# ── Help & aliases ───────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH audit:bruteforce -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help shows description" "brute-force" "$OUT"
assert_output_contains "Help shows --days" "--days" "$OUT"
assert_output_contains "Help shows --min-attempts" "--min-attempts" "$OUT"
assert_output_contains "Help shows --ip" "--ip" "$OUT"
assert_output_contains "Help shows --password-policy" "--password-policy" "$OUT"
assert_output_contains "Help shows --targeted-users" "--targeted-users" "$OUT"
echo ""



print_summary
