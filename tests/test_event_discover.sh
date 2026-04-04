#!/usr/bin/env bash
#
# Integration test for moosh2 event:discover command
# Requires Moodle source at ~/git/moodle/public
#
# Usage: bash tests/test_event_discover.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 event:discover integration tests ==="
echo "moosh path:  $MOOSH"
echo ""

MOODLE_SRC="$HOME/git/moodle/public"

# ── Help output ───────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH event:discover --help)
assert_output_contains "Help description" "Discover all event" "$OUT"
assert_output_contains "Help shows path argument" "path" "$OUT"
echo ""

# ── Invalid path ──────────────────────────────────────────────────

echo "--- Test: Invalid path ---"
OUT=$($PHP $MOOSH event:discover /nonexistent/path 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for invalid path" 1 "$EXIT_CODE"
assert_output_contains "Directory not found" "not found" "$OUT"
echo ""

# ── Discover events ──────────────────────────────────────────────

echo "--- Test: Discover events from Moodle source ---"
OUT=$($PHP $MOOSH event:discover "$MOODLE_SRC" 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 0" 0 "$EXIT_CODE"
assert_output_contains "Discovered message" "Discovered" "$OUT"
assert_output_contains "Wrote to event_map" "event_map.php" "$OUT"
echo ""

# ── Verify event map file ────────────────────────────────────────

echo "--- Test: Verify event_map.php ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
EVENT_MAP="$SCRIPT_DIR/../src/Data/event_map.php"

if [ -f "$EVENT_MAP" ]; then
    echo "  PASS: event_map.php exists"
    ((PASS++))
else
    echo "  FAIL: event_map.php not found at $EVENT_MAP"
    ((FAIL++))
fi

# Check it contains known events
CONTENT=$(cat "$EVENT_MAP")
assert_output_contains "Contains course_viewed" "course_viewed" "$CONTENT"
assert_output_contains "Contains user_loggedin" "user_loggedin" "$CONTENT"
assert_output_contains "Contains user_created" "user_created" "$CONTENT"

# Check event count is reasonable (>600)
EVENT_COUNT=$(grep -c "=>" "$EVENT_MAP")
if [ "$EVENT_COUNT" -gt 600 ]; then
    echo "  PASS: Event count is reasonable ($EVENT_COUNT events)"
    ((PASS++))
else
    echo "  FAIL: Event count too low ($EVENT_COUNT events)"
    ((FAIL++))
fi

# Check it's valid PHP
$PHP -l "$EVENT_MAP" > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "  PASS: event_map.php is valid PHP"
    ((PASS++))
else
    echo "  FAIL: event_map.php has syntax errors"
    ((FAIL++))
fi
echo ""

# ── event-discover alias ─────────────────────────────────────────


print_summary
