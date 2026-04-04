#!/usr/bin/env bash
#
# Integration test for moosh2 sql:select command
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_sql_select.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 sql:select integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ── Basic CSV query ───────────────────────────────────────────────

echo "--- Test: Basic CSV query ---"
OUT=$($PHP $MOOSH sql:select -p "$MOODLE_PATH" "SELECT id, username, email FROM {user} WHERE id <= 3" -o csv)
echo "$OUT"
assert_output_contains "Header row" "id,username,email" "$OUT"
assert_output_contains "Guest user" "guest" "$OUT"
assert_output_contains "Admin user" "admin" "$OUT"
assert_output_contains "Student user" "student01" "$OUT"
echo ""

# ── Table output ──────────────────────────────────────────────────

echo "--- Test: Table output ---"
OUT=$($PHP $MOOSH sql:select -p "$MOODLE_PATH" "SELECT id, username FROM {user} WHERE username = 'admin'")
echo "$OUT"
assert_output_contains "Table has id" "id" "$OUT"
assert_output_contains "Table has username" "username" "$OUT"
assert_output_contains "Table has admin" "admin" "$OUT"
echo ""

# ── JSON output ───────────────────────────────────────────────────

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH sql:select -p "$MOODLE_PATH" "SELECT id, shortname FROM {course} WHERE id = 2" -o json)
echo "$OUT"
assert_output_contains "JSON has id" '"id"' "$OUT"
assert_output_contains "JSON has shortname" '"shortname"' "$OUT"
assert_output_contains "JSON has algebra" "algebrafundamentals" "$OUT"
echo ""

# ── Limit option ──────────────────────────────────────────────────

echo "--- Test: --limit option ---"
OUT=$($PHP $MOOSH sql:select -p "$MOODLE_PATH" "SELECT id FROM {user}" --limit 3 -o csv)
echo "$OUT"
LINE_COUNT=$(echo "$OUT" | wc -l)
assert_output_contains "Limit to 4 lines (header + 3)" "4" "$LINE_COUNT"
echo ""

# ── Count query ───────────────────────────────────────────────────

echo "--- Test: Count query ---"
OUT=$($PHP $MOOSH sql:select -p "$MOODLE_PATH" "SELECT COUNT(*) AS total FROM {course} WHERE id > 1" -o csv)
echo "$OUT"
assert_output_contains "Header has total" "total" "$OUT"
assert_output_contains "16 courses" "16" "$OUT"
echo ""

# ── Join query ────────────────────────────────────────────────────

echo "--- Test: Join query ---"
OUT=$($PHP $MOOSH sql:select -p "$MOODLE_PATH" "SELECT u.username, e.enrol FROM {user_enrolments} ue JOIN {enrol} e ON e.id = ue.enrolid JOIN {user} u ON u.id = ue.userid WHERE e.courseid = 2 AND u.username = 'student01'" -o csv)
echo "$OUT"
assert_output_contains "Join returns student01" "student01" "$OUT"
assert_output_contains "Enrol method" "manual" "$OUT"
echo ""

# ── Empty result ──────────────────────────────────────────────────

echo "--- Test: Empty result ---"
OUT=$($PHP $MOOSH sql:select -p "$MOODLE_PATH" "SELECT id FROM {user} WHERE username = 'nonexistent'" -o csv)
EXIT_CODE=$?
assert_exit_code "Exit code 0 for empty result" 0 "$EXIT_CODE"
echo ""

# ── Reject non-SELECT ─────────────────────────────────────────────

echo "--- Test: Reject DELETE ---"
OUT=$($PHP $MOOSH sql:select -p "$MOODLE_PATH" "DELETE FROM {user}" 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for DELETE" 1 "$EXIT_CODE"
assert_output_contains "Only SELECT allowed" "Only SELECT" "$OUT"
echo ""

echo "--- Test: Reject UPDATE ---"
OUT=$($PHP $MOOSH sql:select -p "$MOODLE_PATH" "UPDATE {user} SET username = 'x'" 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for UPDATE" 1 "$EXIT_CODE"
assert_output_contains "Only SELECT allowed" "Only SELECT" "$OUT"
echo ""

echo "--- Test: Reject INSERT ---"
OUT=$($PHP $MOOSH sql:select -p "$MOODLE_PATH" "INSERT INTO {user} (username) VALUES ('x')" 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for INSERT" 1 "$EXIT_CODE"
assert_output_contains "Only SELECT allowed" "Only SELECT" "$OUT"
echo ""

# ── Invalid SQL ───────────────────────────────────────────────────

echo "--- Test: Invalid SQL ---"
OUT=$($PHP $MOOSH sql:select -p "$MOODLE_PATH" "SELECT * FROM {nonexistent_table}" 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for invalid SQL" 1 "$EXIT_CODE"
assert_output_contains "Query failed error" "Query failed" "$OUT"
echo ""

# ── Help output ───────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH sql:select -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Run a SELECT query" "$OUT"
assert_output_contains "Help shows --limit" "--limit" "$OUT"
assert_output_contains "Help shows query argument" "query" "$OUT"
echo ""

# ── sql-select alias ──────────────────────────────────────────────


print_summary
