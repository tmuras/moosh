#!/usr/bin/env bash
#
# Integration test for moosh2 log:unpack command
# Requires a working Moodle 5.1 installation
#
# Usage: bash tests/test_log_unpack.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 log:unpack integration tests ==="
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
OUT=$($PHP $MOOSH log:unpack --help)
assert_output_contains "Help description" "Restore IDs" "$OUT"
assert_output_contains "Help shows file argument" "file" "$OUT"
assert_output_contains "Help shows output argument" "output" "$OUT"
echo ""

# ── Round-trip: compact export then unpack ────────────────────────

echo "--- Test: Round-trip compact export + unpack ---"

# Do compact export first
COMPACT_DIR="$TMPDIR/compact"
mkdir -p "$COMPACT_DIR"
$PHP $MOOSH log:export -p "$MOODLE_PATH" --compact --from="2020-01-01" --to="2030-12-31" "$COMPACT_DIR/logs.csv" 2>&1

# Then do a normal export of the same range (may have 1 extra log entry from above)
$PHP $MOOSH log:export -p "$MOODLE_PATH" --from="2020-01-01" --to="2030-12-31" "$TMPDIR/normal.csv" 2>&1

# Unpack
OUT=$($PHP $MOOSH log:unpack "$COMPACT_DIR/logs.csv" "$TMPDIR/restored.csv" 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 0 for unpack" 0 "$EXIT_CODE"
assert_output_contains "Unpacked message" "Unpacked" "$OUT"

# Check restored CSV has id as first column
RESTORED_HEADER=$(head -1 "$TMPDIR/restored.csv")
assert_output_contains "Restored CSV has id column" "id" "$RESTORED_HEADER"

# Compact CSV row count should match restored CSV row count
COMPACT_DATA_LINES=$(tail -n +2 "$COMPACT_DIR/logs.csv" | wc -l)
RESTORED_DATA_LINES=$(tail -n +2 "$TMPDIR/restored.csv" | wc -l)
if [ "$COMPACT_DATA_LINES" -eq "$RESTORED_DATA_LINES" ]; then
    echo "  PASS: Restored row count matches compact ($COMPACT_DATA_LINES rows)"
    ((PASS++))
else
    echo "  FAIL: Row count mismatch (compact=$COMPACT_DATA_LINES, restored=$RESTORED_DATA_LINES)"
    ((FAIL++))
fi

# Check first_id from metadata matches first row of restored CSV
FIRST_ID_META=$(cat "$COMPACT_DIR/metadata.json" | grep first_id | tr -dc '0-9')
FIRST_ID_RESTORED=$(tail -n +2 "$TMPDIR/restored.csv" | head -1 | cut -d',' -f1 | tr -d '"')
if [ "$FIRST_ID_META" -eq "$FIRST_ID_RESTORED" ]; then
    echo "  PASS: First ID matches metadata ($FIRST_ID_META)"
    ((PASS++))
else
    echo "  FAIL: First ID mismatch (metadata=$FIRST_ID_META, restored=$FIRST_ID_RESTORED)"
    ((FAIL++))
fi

# Check that restored last ID = first_id + row_count - 1
LAST_ID_RESTORED=$(tail -1 "$TMPDIR/restored.csv" | cut -d',' -f1 | tr -d '"')
EXPECTED_LAST_ID=$((FIRST_ID_META + RESTORED_DATA_LINES - 1))
if [ "$LAST_ID_RESTORED" -eq "$EXPECTED_LAST_ID" ]; then
    echo "  PASS: Last ID is correct ($LAST_ID_RESTORED)"
    ((PASS++))
else
    echo "  FAIL: Last ID mismatch (expected=$EXPECTED_LAST_ID, restored=$LAST_ID_RESTORED)"
    ((FAIL++))
fi

# Check that eventname column contains backslash-prefixed strings, not numeric IDs
EVENTNAME_COL=$(head -1 "$TMPDIR/restored.csv" | tr ',' '\n' | grep -n "eventname" | cut -d: -f1)
if [ -n "$EVENTNAME_COL" ]; then
    FIRST_EVENT=$(tail -n +2 "$TMPDIR/restored.csv" | head -1 | cut -d',' -f"$EVENTNAME_COL" | tr -d '"')
    if echo "$FIRST_EVENT" | grep -q '\\'; then
        echo "  PASS: Restored eventname is a string ($FIRST_EVENT)"
        ((PASS++))
    else
        echo "  FAIL: Restored eventname should contain backslash, got: $FIRST_EVENT"
        ((FAIL++))
    fi
else
    echo "  FAIL: eventname column not found in restored CSV"
    ((FAIL++))
fi

# Check that origin column contains string values, not empty or numeric
ORIGIN_COL=$(head -1 "$TMPDIR/restored.csv" | tr ',' '\n' | grep -n "^origin$" | cut -d: -f1)
if [ -n "$ORIGIN_COL" ]; then
    FIRST_ORIGIN=$(tail -n +2 "$TMPDIR/restored.csv" | head -1 | cut -d',' -f"$ORIGIN_COL" | tr -d '"')
    if echo "$FIRST_ORIGIN" | grep -qE '^(web|cli|ws|cron|restore)$'; then
        echo "  PASS: Restored origin is a string ($FIRST_ORIGIN)"
        ((PASS++))
    else
        echo "  FAIL: Restored origin should be a known string, got: $FIRST_ORIGIN"
        ((FAIL++))
    fi
else
    echo "  FAIL: origin column not found in restored CSV"
    ((FAIL++))
fi

# Check that action column contains string values, not numeric IDs
FIRST_ACTION=$($PHP -r '
    $fh = fopen($argv[1], "r");
    $headers = fgetcsv($fh);
    $idx = array_search("action", $headers);
    $row = fgetcsv($fh);
    echo $row[$idx] ?? "";
    fclose($fh);
' "$TMPDIR/restored.csv")
if echo "$FIRST_ACTION" | grep -qE '^[a-z]+$'; then
    echo "  PASS: Restored action is a string ($FIRST_ACTION)"
    ((PASS++))
else
    echo "  FAIL: Restored action should be a string, got: $FIRST_ACTION"
    ((FAIL++))
fi

# Check that timecreated values are full timestamps (not deltas)
# Use PHP to properly parse CSV and extract timecreated from second data row
SECOND_TIME=$($PHP -r '
    $fh = fopen($argv[1], "r");
    $headers = fgetcsv($fh);
    $idx = array_search("timecreated", $headers);
    fgetcsv($fh); // skip first data row
    $row = fgetcsv($fh); // second data row
    echo $row[$idx] ?? "";
    fclose($fh);
' "$TMPDIR/restored.csv")
if [ -n "$SECOND_TIME" ] && [ "$SECOND_TIME" -gt 1000000000 ]; then
    echo "  PASS: Restored timecreated is a full timestamp ($SECOND_TIME)"
    ((PASS++))
else
    echo "  FAIL: Restored timecreated should be a full timestamp, got: $SECOND_TIME"
    ((FAIL++))
fi
echo ""

# ── Missing metadata.json ────────────────────────────────────────

echo "--- Test: Missing metadata.json ---"
NO_META_DIR=$(mktemp -d)
echo "eventname,timecreated" > "$NO_META_DIR/logs.csv"
echo "test_event,1234567890" >> "$NO_META_DIR/logs.csv"
OUT=$($PHP $MOOSH log:unpack "$NO_META_DIR/logs.csv" "$NO_META_DIR/out.csv" 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for missing metadata" 1 "$EXIT_CODE"
assert_output_contains "Error about missing metadata" "metadata.json not found" "$OUT"
rm -rf "$NO_META_DIR"
echo ""

# ── Missing input file ───────────────────────────────────────────

echo "--- Test: Missing input file ---"
OUT=$($PHP $MOOSH log:unpack "$TMPDIR/nonexistent.csv" "$TMPDIR/out.csv" 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for missing input" 1 "$EXIT_CODE"
assert_output_contains "Error about missing file" "not found" "$OUT"
echo ""

# ── log-unpack alias ──────────────────────────────────────────────


# Clean up
rm -rf "$TMPDIR"

print_summary
