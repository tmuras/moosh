#!/usr/bin/env bash
#
# Integration test for moosh2 log:export command
# Requires a working Moodle 5.2 installation
#
# Usage: bash tests/test_log_export.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 log:export integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

TMPDIR=$(mktemp -d)

# ── Help output ───────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH log:export -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Export entries" "$OUT"
assert_output_contains "Help shows --from" "--from" "$OUT"
assert_output_contains "Help shows --to" "--to" "$OUT"
assert_output_contains "Help shows file argument" "file" "$OUT"
echo ""

# ── Missing --from and --to ───────────────────────────────────────

echo "--- Test: Missing --from and --to ---"
OUT=$($PHP $MOOSH log:export -p "$MOODLE_PATH" "$TMPDIR/missing.csv" 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for missing options" 1 "$EXIT_CODE"
assert_output_contains "Error about required options" "required" "$OUT"
echo ""

# ── Invalid from date ─────────────────────────────────────────────

echo "--- Test: Invalid --from date ---"
OUT=$($PHP $MOOSH log:export -p "$MOODLE_PATH" --from="not-a-date" --to="2025-12-31" "$TMPDIR/invalid.csv" 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for invalid from" 1 "$EXIT_CODE"
assert_output_contains "Invalid from error" "Invalid --from" "$OUT"
echo ""

# ── Invalid to date ───────────────────────────────────────────────

echo "--- Test: Invalid --to date ---"
OUT=$($PHP $MOOSH log:export -p "$MOODLE_PATH" --from="2025-01-01" --to="not-a-date" "$TMPDIR/invalid.csv" 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for invalid to" 1 "$EXIT_CODE"
assert_output_contains "Invalid to error" "Invalid --to" "$OUT"
echo ""

# ── to before from ────────────────────────────────────────────────

echo "--- Test: to before from ---"
OUT=$($PHP $MOOSH log:export -p "$MOODLE_PATH" --from="2025-12-31" --to="2025-01-01" "$TMPDIR/reversed.csv" 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for reversed dates" 1 "$EXIT_CODE"
assert_output_contains "Date order error" "later" "$OUT"
echo ""

# ── Empty result range ────────────────────────────────────────────

echo "--- Test: Empty result for distant past ---"
OUT=$($PHP $MOOSH log:export -p "$MOODLE_PATH" --from="2000-01-01" --to="2000-01-02" "$TMPDIR/empty.csv" 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 0 for empty result" 0 "$EXIT_CODE"
assert_output_contains "No entries message" "No log entries" "$OUT"
echo ""

# ── Export with wide date range ───────────────────────────────────

echo "--- Test: Export log entries ---"
OUT=$($PHP $MOOSH log:export -p "$MOODLE_PATH" --from="2020-01-01" --to="2030-12-31" "$TMPDIR/all_logs.csv" 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 0 for export" 0 "$EXIT_CODE"
assert_output_contains "Exported message" "Exported" "$OUT"

# Check the CSV file exists and has content
if [ -f "$TMPDIR/all_logs.csv" ]; then
    echo "  PASS: CSV file created"
    ((PASS++))

    # Check header row
    HEADER=$(head -1 "$TMPDIR/all_logs.csv")
    assert_output_contains "CSV has id column" "id" "$HEADER"
    assert_output_contains "CSV has timecreated column" "timecreated" "$HEADER"
    assert_output_contains "CSV has eventname column" "eventname" "$HEADER"

    # Check that IDs are in ascending order
    IDS=$(tail -n +2 "$TMPDIR/all_logs.csv" | cut -d',' -f1 | tr -d '"')
    SORTED_IDS=$(echo "$IDS" | sort -n)
    if [ "$IDS" = "$SORTED_IDS" ]; then
        echo "  PASS: IDs are in ascending order"
        ((PASS++))
    else
        echo "  FAIL: IDs are not in ascending order"
        ((FAIL++))
    fi
else
    echo "  FAIL: CSV file not created"
    ((FAIL++))
fi
echo ""

# ── Compact export ────────────────────────────────────────────────

echo "--- Test: Compact export ---"
COMPACT_DIR=$(mktemp -d)
OUT=$($PHP $MOOSH log:export -p "$MOODLE_PATH" --compact --from="2020-01-01" --to="2030-12-31" "$COMPACT_DIR/logs.csv" 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 0 for compact export" 0 "$EXIT_CODE"
assert_output_contains "Compact exported message" "Exported" "$OUT"

# Check CSV does not have id as first header
COMPACT_HEADER=$(head -1 "$COMPACT_DIR/logs.csv")
FIRST_COL=$(echo "$COMPACT_HEADER" | cut -d',' -f1 | tr -d '"')
if [ "$FIRST_COL" != "id" ]; then
    echo "  PASS: Compact CSV does not start with id column"
    ((PASS++))
else
    echo "  FAIL: Compact CSV should not start with id column"
    echo "    Got header: $COMPACT_HEADER"
    ((FAIL++))
fi

# Check metadata.json exists and has first_id and event_map
if [ -f "$COMPACT_DIR/metadata.json" ]; then
    echo "  PASS: metadata.json created"
    ((PASS++))
    METADATA=$(cat "$COMPACT_DIR/metadata.json")
    assert_output_contains "metadata.json has first_id" "first_id" "$METADATA"
    assert_output_contains "metadata.json has event_map" "event_map" "$METADATA"
    assert_output_contains "metadata.json has default_origin" "default_origin" "$METADATA"
    assert_output_contains "metadata.json has origin_map" "origin_map" "$METADATA"
    assert_output_contains "metadata.json has action_map" "action_map" "$METADATA"
    assert_output_contains "metadata.json has timecreated_delta" "timecreated_delta" "$METADATA"
else
    echo "  FAIL: metadata.json not created"
    ((FAIL++))
fi

# Check that origin column uses empty string for default origin
ORIGIN_COL=$(head -1 "$COMPACT_DIR/logs.csv" | tr ',' '\n' | grep -n "^origin$" | cut -d: -f1)
if [ -n "$ORIGIN_COL" ]; then
    # Count how many rows have empty origin (the default)
    EMPTY_ORIGINS=$(tail -n +2 "$COMPACT_DIR/logs.csv" | cut -d',' -f"$ORIGIN_COL" | tr -d '"' | grep -c '^$')
    if [ "$EMPTY_ORIGINS" -gt 0 ]; then
        echo "  PASS: Compact CSV has empty origin fields for default ($EMPTY_ORIGINS rows)"
        ((PASS++))
    else
        echo "  FAIL: Expected some empty origin fields for default origin"
        ((FAIL++))
    fi
else
    echo "  FAIL: origin column not found in compact CSV"
    ((FAIL++))
fi

# Check that eventname column contains numeric IDs, not backslash-prefixed strings
# Find eventname column index in compact CSV
EVENTNAME_COL=$(head -1 "$COMPACT_DIR/logs.csv" | tr ',' '\n' | grep -n "eventname" | cut -d: -f1)
if [ -n "$EVENTNAME_COL" ]; then
    FIRST_EVENT=$(tail -n +2 "$COMPACT_DIR/logs.csv" | head -1 | cut -d',' -f"$EVENTNAME_COL" | tr -d '"')
    if echo "$FIRST_EVENT" | grep -qE '^[0-9]+$'; then
        echo "  PASS: Compact eventname is numeric ID ($FIRST_EVENT)"
        ((PASS++))
    else
        echo "  FAIL: Compact eventname should be numeric, got: $FIRST_EVENT"
        ((FAIL++))
    fi
else
    echo "  FAIL: eventname column not found in compact CSV"
    ((FAIL++))
fi
rm -rf "$COMPACT_DIR"
echo ""

# ── Help shows --compact ─────────────────────────────────────────

echo "--- Test: Help shows --compact ---"
OUT=$($PHP $MOOSH log:export -p "$MOODLE_PATH" --help)
assert_output_contains "Help shows --compact" "--compact" "$OUT"
echo ""

# ── log-export alias ──────────────────────────────────────────────


# Clean up
rm -rf "$TMPDIR"

print_summary
