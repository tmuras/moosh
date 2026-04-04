#!/usr/bin/env bash
#
# Integration test for moosh2 report:concurrency command
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_report_concurrency.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 report:concurrency integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ── Default summary (table) ──────────────────────────────────────

echo "--- Test: Default summary (table) ---"
OUT=$($PHP $MOOSH report:concurrency -p "$MOODLE_PATH")
echo "$OUT"
assert_output_contains "Shows Period from" "Period from" "$OUT"
assert_output_contains "Shows Period to" "Period to" "$OUT"
assert_output_contains "Shows Timezone" "Timezone" "$OUT"
assert_output_contains "Shows Active users" "Active users" "$OUT"
assert_output_contains "Shows Total log entries" "Total log entries" "$OUT"
assert_output_contains "Shows Max concurrent" "Max concurrent users" "$OUT"
assert_output_contains "Shows Global average" "Global average concurrent" "$OUT"
assert_output_contains "Shows Work-hours average" "Work-hours average concurrent" "$OUT"
echo ""

# ── JSON output ───────────────────────────────────────────────────

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH report:concurrency -p "$MOODLE_PATH" -o json)
echo "$OUT"
assert_output_contains "JSON has Period from" '"Period from"' "$OUT"
assert_output_contains "JSON has Active users" '"Active users"' "$OUT"
assert_output_contains "JSON has Max concurrent" '"Max concurrent users"' "$OUT"
assert_output_contains "JSON has UTC timezone" '"UTC"' "$OUT"
echo ""

# ── CSV output ────────────────────────────────────────────────────

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH report:concurrency -p "$MOODLE_PATH" -o csv)
echo "$OUT" | head -2
assert_output_contains "CSV has headers" '"Period from"' "$OUT"
assert_output_contains "CSV has Active users header" '"Active users"' "$OUT"
echo ""

# ── Custom date range ─────────────────────────────────────────────

echo "--- Test: Custom date range ---"
OUT=$($PHP $MOOSH report:concurrency -p "$MOODLE_PATH" --from 2026-03-28 --to 2026-03-29 -o json)
echo "$OUT"
assert_output_contains "From date set" '"2026-03-28 00:00:00"' "$OUT"
assert_output_contains "To date set" '"2026-03-29 23:59:59"' "$OUT"
echo ""

# ── Custom timezone ───────────────────────────────────────────────

echo "--- Test: Custom timezone ---"
OUT=$($PHP $MOOSH report:concurrency -p "$MOODLE_PATH" --timezone "Europe/Warsaw" -o json)
assert_output_contains "Timezone Europe/Warsaw" 'Europe' "$OUT"
echo ""

# ── Custom period ─────────────────────────────────────────────────

echo "--- Test: Custom period ---"
OUT=$($PHP $MOOSH report:concurrency -p "$MOODLE_PATH" --period 30 -o json)
# Should still work, just different aggregation
assert_output_contains "Still has Active users" '"Active users"' "$OUT"
echo ""

# ── Timeseries output ─────────────────────────────────────────────

echo "--- Test: Timeseries output (CSV) ---"
OUT=$($PHP $MOOSH report:concurrency -p "$MOODLE_PATH" --timeseries -o csv)
echo "$OUT" | head -5
assert_output_contains "Timeseries header" "datetime,users,actions" "$OUT"
echo ""

echo "--- Test: Timeseries output (JSON) ---"
OUT=$($PHP $MOOSH report:concurrency -p "$MOODLE_PATH" --timeseries -o json)
# May be empty array if no web/ws activity
assert_output_contains "Timeseries is valid JSON" "[" "$OUT"
echo ""

# ── Work hours/days filter ────────────────────────────────────────

echo "--- Test: Work hours filter ---"
OUT=$($PHP $MOOSH report:concurrency -p "$MOODLE_PATH" --work-hours-from 9 --work-hours-to 17 -o json)
assert_output_contains "Has work-hours average" '"Work-hours average concurrent"' "$OUT"
echo ""

echo "--- Test: Work days filter ---"
OUT=$($PHP $MOOSH report:concurrency -p "$MOODLE_PATH" --work-days 12345 -o json)
assert_output_contains "Has work-hours average with work days" '"Work-hours average concurrent"' "$OUT"
echo ""

# ── Active users count ────────────────────────────────────────────

echo "--- Test: Active users present ---"
OUT=$($PHP $MOOSH report:concurrency -p "$MOODLE_PATH" -o json)
# Test data has admin user activity
assert_output_contains "Has active users" '"Active users":' "$OUT"
echo ""

# ── Invalid date range ────────────────────────────────────────────

echo "--- Test: Invalid date range ---"
OUT=$($PHP $MOOSH report:concurrency -p "$MOODLE_PATH" --from 2026-03-30 --to 2026-03-01 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for invalid range" 1 "$EXIT_CODE"
assert_output_contains "Error for invalid range" "later" "$OUT"
echo ""

# ── Help output ───────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH report:concurrency -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Report on concurrent user activity" "$OUT"
assert_output_contains "Help shows --from" "--from" "$OUT"
assert_output_contains "Help shows --to" "--to" "$OUT"
assert_output_contains "Help shows --period" "--period" "$OUT"
assert_output_contains "Help shows --timezone" "--timezone" "$OUT"
assert_output_contains "Help shows --timeseries" "--timeseries" "$OUT"
assert_output_contains "Help shows --work-hours-from" "--work-hours-from" "$OUT"
assert_output_contains "Help shows --work-days" "--work-days" "$OUT"
echo ""

# ── report-concurrency alias ─────────────────────────────────────


print_summary
