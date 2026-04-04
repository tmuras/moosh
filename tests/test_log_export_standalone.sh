#!/usr/bin/env bash
#
# Integration test comparing stand_alone_scripts/log-export.php against moosh log:export.
# Both tools should produce identical output for the same database state.
#
# Usage: bash tests/test_log_export_standalone.sh
#

source "$(dirname "$0")/common.sh"

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
STANDALONE="$SCRIPT_DIR/../stand_alone_scripts/log-export.php"
EVENT_MAP="$SCRIPT_DIR/../src/Data/event_map.php"

echo "=== moosh2 log:export vs standalone comparison tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo "standalone:  $STANDALONE"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
bash "$SCRIPT_DIR/clear.sh"
echo ""

TMPDIR=$(mktemp -d)

# ── Normal export: compare CSV output ─────────────────────────────

echo "--- Test: Normal export — CSV comparison ---"

$PHP $MOOSH log:export -l -p "$MOODLE_PATH" --from="2020-01-01" --to="2030-12-31" "$TMPDIR/moosh_normal.csv" 2>/dev/null

$PHP $STANDALONE "$MOODLE_PATH" --from=2020-01-01 --to=2030-12-31 "$TMPDIR/standalone_normal.csv" 2>/dev/null

# Compare row counts
MOOSH_LINES=$(wc -l < "$TMPDIR/moosh_normal.csv")
STANDALONE_LINES=$(wc -l < "$TMPDIR/standalone_normal.csv")
if [ "$MOOSH_LINES" -eq "$STANDALONE_LINES" ]; then
    echo "  PASS: Normal CSV row count matches ($MOOSH_LINES lines)"
    ((PASS++))
else
    echo "  FAIL: Normal CSV row count mismatch (moosh=$MOOSH_LINES, standalone=$STANDALONE_LINES)"
    ((FAIL++))
fi

# Compare headers
MOOSH_HEADER=$(head -1 "$TMPDIR/moosh_normal.csv")
STANDALONE_HEADER=$(head -1 "$TMPDIR/standalone_normal.csv")
if [ "$MOOSH_HEADER" = "$STANDALONE_HEADER" ]; then
    echo "  PASS: Normal CSV headers match"
    ((PASS++))
else
    echo "  FAIL: Normal CSV headers differ"
    echo "    moosh:      $MOOSH_HEADER"
    echo "    standalone:  $STANDALONE_HEADER"
    ((FAIL++))
fi

# Full diff
DIFF=$(diff "$TMPDIR/moosh_normal.csv" "$TMPDIR/standalone_normal.csv")
if [ -z "$DIFF" ]; then
    echo "  PASS: Normal CSV files are identical"
    ((PASS++))
else
    echo "  FAIL: Normal CSV files differ"
    echo "$DIFF" | head -10
    ((FAIL++))
fi
echo ""

# ── Compact export: compare CSV and metadata.json ─────────────────

echo "--- Test: Compact export — CSV comparison ---"

MOOSH_DIR=$(mktemp -d)
STANDALONE_DIR=$(mktemp -d)

$PHP $MOOSH log:export -l -p "$MOODLE_PATH" --compact --from="2020-01-01" --to="2030-12-31" "$MOOSH_DIR/logs.csv" 2>/dev/null

$PHP $STANDALONE "$MOODLE_PATH" --compact --event-map="$EVENT_MAP" --from=2020-01-01 --to=2030-12-31 "$STANDALONE_DIR/logs.csv" 2>/dev/null

# Compare compact CSV row counts
MOOSH_LINES=$(wc -l < "$MOOSH_DIR/logs.csv")
STANDALONE_LINES=$(wc -l < "$STANDALONE_DIR/logs.csv")
if [ "$MOOSH_LINES" -eq "$STANDALONE_LINES" ]; then
    echo "  PASS: Compact CSV row count matches ($MOOSH_LINES lines)"
    ((PASS++))
else
    echo "  FAIL: Compact CSV row count mismatch (moosh=$MOOSH_LINES, standalone=$STANDALONE_LINES)"
    ((FAIL++))
fi

# Compare compact CSV headers
MOOSH_HEADER=$(head -1 "$MOOSH_DIR/logs.csv")
STANDALONE_HEADER=$(head -1 "$STANDALONE_DIR/logs.csv")
if [ "$MOOSH_HEADER" = "$STANDALONE_HEADER" ]; then
    echo "  PASS: Compact CSV headers match"
    ((PASS++))
else
    echo "  FAIL: Compact CSV headers differ"
    echo "    moosh:      $MOOSH_HEADER"
    echo "    standalone:  $STANDALONE_HEADER"
    ((FAIL++))
fi

# Full diff of compact CSV
DIFF=$(diff "$MOOSH_DIR/logs.csv" "$STANDALONE_DIR/logs.csv")
if [ -z "$DIFF" ]; then
    echo "  PASS: Compact CSV files are identical"
    ((PASS++))
else
    echo "  FAIL: Compact CSV files differ"
    echo "$DIFF" | head -10
    ((FAIL++))
fi
echo ""

# ── Compare metadata.json ────────────────────────────────────────

echo "--- Test: Compact export — metadata.json comparison ---"

if [ -f "$MOOSH_DIR/metadata.json" ] && [ -f "$STANDALONE_DIR/metadata.json" ]; then
    echo "  PASS: Both metadata.json files exist"
    ((PASS++))

    DIFF=$(diff "$MOOSH_DIR/metadata.json" "$STANDALONE_DIR/metadata.json")
    if [ -z "$DIFF" ]; then
        echo "  PASS: metadata.json files are identical"
        ((PASS++))
    else
        echo "  FAIL: metadata.json files differ"
        echo "$DIFF" | head -10
        ((FAIL++))
    fi
else
    if [ ! -f "$MOOSH_DIR/metadata.json" ]; then
        echo "  FAIL: moosh metadata.json not found"
        ((FAIL++))
    fi
    if [ ! -f "$STANDALONE_DIR/metadata.json" ]; then
        echo "  FAIL: standalone metadata.json not found"
        ((FAIL++))
    fi
fi
echo ""

# ── Cross-unpack: unpack standalone output with moosh ─────────────

echo "--- Test: Cross-unpack — standalone compact unpacked by moosh ---"

$PHP $MOOSH log:unpack -l "$STANDALONE_DIR/logs.csv" "$TMPDIR/cross_unpacked.csv" 2>/dev/null
EXIT_CODE=$?
assert_exit_code "moosh unpack of standalone compact succeeds" 0 "$EXIT_CODE"

# Compare the unpacked output against moosh normal export
DIFF=$(diff "$TMPDIR/moosh_normal.csv" "$TMPDIR/cross_unpacked.csv")
if [ -z "$DIFF" ]; then
    echo "  PASS: Unpacked standalone compact matches moosh normal export"
    ((PASS++))
else
    echo "  FAIL: Unpacked standalone compact differs from moosh normal export"
    echo "$DIFF" | head -10
    ((FAIL++))
fi
echo ""

# ── Cross-unpack: unpack moosh output with moosh ─────────────────

echo "--- Test: Cross-unpack — moosh compact unpacked by moosh ---"

$PHP $MOOSH log:unpack -l "$MOOSH_DIR/logs.csv" "$TMPDIR/moosh_unpacked.csv" 2>/dev/null
EXIT_CODE=$?
assert_exit_code "moosh unpack of moosh compact succeeds" 0 "$EXIT_CODE"

# Compare the unpacked output against moosh normal export
DIFF=$(diff "$TMPDIR/moosh_normal.csv" "$TMPDIR/moosh_unpacked.csv")
if [ -z "$DIFF" ]; then
    echo "  PASS: Unpacked moosh compact matches moosh normal export"
    ((PASS++))
else
    echo "  FAIL: Unpacked moosh compact differs from moosh normal export"
    echo "$DIFF" | head -10
    ((FAIL++))
fi
echo ""

# Clean up
rm -rf "$TMPDIR" "$MOOSH_DIR" "$STANDALONE_DIR"

print_summary
