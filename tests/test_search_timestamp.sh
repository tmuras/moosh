#!/usr/bin/env bash
#
# Integration test for moosh2 search:timestamp command
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_search_timestamp.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 search:timestamp integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Get a known timestamp from student01
KNOWN_TS=$($PHP -r "define('CLI_SCRIPT',true); require('$MOODLE_PATH/config.php'); global \$DB; \$r = \$DB->get_record_sql('SELECT timecreated FROM {user} WHERE username = ?', ['student01']); echo \$r->timecreated;" 2>/dev/null)
echo "Known timestamp (student01 timecreated): $KNOWN_TS"
echo ""

# ── Exact timestamp search (CSV) ─────────────────────────────────

echo "--- Test: Exact timestamp search (CSV) ---"
OUT=$($PHP $MOOSH search:timestamp -p "$MOODLE_PATH" $KNOWN_TS -o csv)
echo "$OUT" | head -5
assert_output_contains "Header row" "table,id,column,value,datetime" "$OUT"
assert_output_contains "Found in user table" "user," "$OUT"
assert_output_contains "Found timecreated column" "timecreated" "$OUT"
assert_output_contains "Shows timestamp value" "$KNOWN_TS" "$OUT"
assert_output_contains "Shows datetime" "2026-" "$OUT"
echo ""

# ── Finds across multiple tables ──────────────────────────────────

echo "--- Test: Finds across multiple tables ---"
OUT=$($PHP $MOOSH search:timestamp -p "$MOODLE_PATH" $KNOWN_TS -o csv)
# Should find in user, course, enrol, etc.
assert_output_contains "Found in course table" "course," "$OUT"
assert_output_contains "Found in enrol table" "enrol," "$OUT"
echo ""

# ── Range search ──────────────────────────────────────────────────

echo "--- Test: Range search ---"
FROM=$((KNOWN_TS - 5))
TO=$((KNOWN_TS + 5))
OUT=$($PHP $MOOSH search:timestamp -p "$MOODLE_PATH" --from $FROM --to $TO -o csv --limit 5)
echo "$OUT" | head -5
FIRST_LINE=$(echo "$OUT" | head -1)
assert_output_contains "Range header" "table,id,column,value,datetime" "$FIRST_LINE"
assert_output_contains "Range finds matches" "timecreated" "$OUT"
echo ""

# ── JSON output ───────────────────────────────────────────────────

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH search:timestamp -p "$MOODLE_PATH" $KNOWN_TS -o json --limit 2)
echo "$OUT" | head -10
assert_output_contains "JSON has table" '"table"' "$OUT"
assert_output_contains "JSON has column" '"column"' "$OUT"
assert_output_contains "JSON has value" '"value"' "$OUT"
assert_output_contains "JSON has datetime" '"datetime"' "$OUT"
echo ""

# ── Table output ──────────────────────────────────────────────────

echo "--- Test: Table output ---"
OUT=$($PHP $MOOSH search:timestamp -p "$MOODLE_PATH" $KNOWN_TS --limit 3)
assert_output_contains "Table has table header" "table" "$OUT"
assert_output_contains "Table has column header" "column" "$OUT"
echo ""

# ── Limit option ──────────────────────────────────────────────────

echo "--- Test: Limit option ---"
OUT=$($PHP $MOOSH search:timestamp -p "$MOODLE_PATH" $KNOWN_TS -o csv --limit 2)
# Count data lines per table should be capped
assert_output_contains "Has results" "timecreated" "$OUT"
echo ""

# ── No matches ────────────────────────────────────────────────────

echo "--- Test: No matches for far-future timestamp ---"
OUT=$($PHP $MOOSH search:timestamp -p "$MOODLE_PATH" 9999999999 -o csv)
LINE_COUNT=$(echo "$OUT" | wc -l)
assert_output_contains "Only header line" "1" "$LINE_COUNT"
echo ""

# ── Validation errors ─────────────────────────────────────────────

echo "--- Test: No arguments ---"
OUT=$($PHP $MOOSH search:timestamp -p "$MOODLE_PATH" 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for no args" 1 "$EXIT_CODE"
echo ""

echo "--- Test: Both timestamp and range ---"
OUT=$($PHP $MOOSH search:timestamp -p "$MOODLE_PATH" --from 100 --to 200 $KNOWN_TS 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for both modes" 1 "$EXIT_CODE"
assert_output_contains "Cannot use both" "Cannot use both" "$OUT"
echo ""

echo "--- Test: Only --from without --to ---"
OUT=$($PHP $MOOSH search:timestamp -p "$MOODLE_PATH" --from 100 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for missing --to" 1 "$EXIT_CODE"
assert_output_contains "Both required" "Both" "$OUT"
echo ""

# ── Help output ───────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH search:timestamp -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Search for a timestamp" "$OUT"
assert_output_contains "Help shows --from" "--from" "$OUT"
assert_output_contains "Help shows --to" "--to" "$OUT"
assert_output_contains "Help shows --limit" "--limit" "$OUT"
echo ""

# ── Alias ─────────────────────────────────────────────────────────


print_summary
