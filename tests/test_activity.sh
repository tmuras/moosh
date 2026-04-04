#!/usr/bin/env bash
#
# Integration test for moosh2 activity:create, activity:delete, activity:mod commands
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_activity.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 activity commands integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Test data: Course 2 (Algebra Fundamentals) has 1 resource activity (cmid=1), 4 sections

# ═══════════════════════════════════════════════════════════════════
# activity:create
# ═══════════════════════════════════════════════════════════════════

echo "========== activity:create =========="
echo ""

# ── Dry run ───────────────────────────────────────────────────────

echo "--- Test: activity:create dry run ---"
OUT=$($PHP $MOOSH activity:create -p "$MOODLE_PATH" forum 2)
echo "$OUT"
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows activity type" "forum" "$OUT"
echo ""

# ── Add forum ─────────────────────────────────────────────────────

echo "--- Test: Add forum to course 2 ---"
OUT=$($PHP $MOOSH activity:create -p "$MOODLE_PATH" --run --name "Discussion Forum" forum 2 -o csv)
echo "$OUT"
assert_output_contains "Header row" "cmid,module,instance,course,section" "$OUT"
assert_output_contains "Module is forum" ",forum," "$OUT"
assert_output_contains "Course is 2" ",2," "$OUT"
FORUM_CMID=$(echo "$OUT" | tail -1 | cut -d, -f1)
echo "  Created forum cmid=$FORUM_CMID"
echo ""

# ── Add assignment ────────────────────────────────────────────────

echo "--- Test: Add assignment to section 2 ---"
OUT=$($PHP $MOOSH activity:create -p "$MOODLE_PATH" --run --name "Homework 1" --section 2 assign 2 -o csv)
echo "$OUT"
assert_output_contains "Module is assign" ",assign," "$OUT"
assert_output_contains "Section is 2" ",2" "$OUT"
ASSIGN_CMID=$(echo "$OUT" | tail -1 | cut -d, -f1)
echo "  Created assign cmid=$ASSIGN_CMID"
echo ""

# ── Add page ──────────────────────────────────────────────────────

echo "--- Test: Add page with idnumber ---"
OUT=$($PHP $MOOSH activity:create -p "$MOODLE_PATH" --run --name "Welcome Page" --idnumber "PAGE001" page 2 -o csv)
echo "$OUT"
assert_output_contains "Module is page" ",page," "$OUT"
PAGE_CMID=$(echo "$OUT" | tail -1 | cut -d, -f1)
echo "  Created page cmid=$PAGE_CMID"
echo ""

# ── JSON output ───────────────────────────────────────────────────

echo "--- Test: activity:create JSON output ---"
OUT=$($PHP $MOOSH activity:create -p "$MOODLE_PATH" --run --name "JSON URL" url 2 -o json)
echo "$OUT"
assert_output_contains "JSON has cmid" '"cmid"' "$OUT"
assert_output_contains "JSON has module" '"module"' "$OUT"
URL_CMID=$(echo "$OUT" | grep -o '"cmid": [0-9]*' | head -1 | grep -o '[0-9]*')
echo "  Created url cmid=$URL_CMID"
echo ""

# ── Invalid module type ───────────────────────────────────────────

echo "--- Test: Invalid module type ---"
OUT=$($PHP $MOOSH activity:create -p "$MOODLE_PATH" --run nonexistent 2 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for invalid type" 1 "$EXIT_CODE"
assert_output_contains "Error for unknown type" "Unknown activity type" "$OUT"
echo ""

# ── Help output ───────────────────────────────────────────────────

echo "--- Test: activity:create help ---"
OUT=$($PHP $MOOSH activity:create -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Create an activity in a course" "$OUT"
assert_output_contains "Help shows --name" "--name" "$OUT"
assert_output_contains "Help shows --section" "--section" "$OUT"
echo ""

# ── Alias ─────────────────────────────────────────────────────────


# ═══════════════════════════════════════════════════════════════════
# activity:mod
# ═══════════════════════════════════════════════════════════════════

echo "========== activity:mod =========="
echo ""

# ── Dry run ───────────────────────────────────────────────────────

echo "--- Test: activity:mod dry run ---"
OUT=$($PHP $MOOSH activity:mod -p "$MOODLE_PATH" --name "New Name" $FORUM_CMID)
echo "$OUT"
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows name change" "name:" "$OUT"
echo ""

# ── Rename activity ───────────────────────────────────────────────

echo "--- Test: Rename activity ---"
OUT=$($PHP $MOOSH activity:mod -p "$MOODLE_PATH" --run --name "Renamed Forum" $FORUM_CMID -o csv)
echo "$OUT"
assert_output_contains "Shows renamed name" "Renamed Forum" "$OUT"
echo ""

# ── Change visibility ─────────────────────────────────────────────

echo "--- Test: Hide activity ---"
OUT=$($PHP $MOOSH activity:mod -p "$MOODLE_PATH" --run --visible 0 $FORUM_CMID -o csv)
echo "$OUT"
assert_output_contains "Shows visible 0" ",0," "$OUT"
echo ""

echo "--- Test: Show activity ---"
OUT=$($PHP $MOOSH activity:mod -p "$MOODLE_PATH" --run --visible 1 $FORUM_CMID -o csv)
echo "$OUT"
assert_output_contains "Shows visible 1" ",1," "$OUT"
echo ""

# ── Set idnumber ──────────────────────────────────────────────────

echo "--- Test: Set idnumber ---"
OUT=$($PHP $MOOSH activity:mod -p "$MOODLE_PATH" --run --idnumber "FORUM001" $FORUM_CMID -o csv)
echo "$OUT"
assert_output_contains "Shows idnumber" "FORUM001" "$OUT"
echo ""

# ── Move to different section ─────────────────────────────────────

echo "--- Test: Move to section 3 ---"
OUT=$($PHP $MOOSH activity:mod -p "$MOODLE_PATH" --run --section 3 $FORUM_CMID -o csv)
echo "$OUT"
assert_output_contains "Moved to section 3" ",3," "$OUT"
echo ""

echo "--- Test: Move back to section 1 ---"
OUT=$($PHP $MOOSH activity:mod -p "$MOODLE_PATH" --run --section 1 $FORUM_CMID -o csv)
echo "$OUT"
assert_output_contains "Moved to section 1" ",1," "$OUT"
echo ""

# ── Multiple changes at once ──────────────────────────────────────

echo "--- Test: Multiple changes at once ---"
OUT=$($PHP $MOOSH activity:mod -p "$MOODLE_PATH" --run --name "Final Forum" --visible 0 --idnumber "FIN001" $FORUM_CMID -o json)
echo "$OUT"
assert_output_contains "JSON name changed" '"Final Forum"' "$OUT"
assert_output_contains "JSON idnumber set" '"FIN001"' "$OUT"
echo ""

# ── No modification specified ─────────────────────────────────────

echo "--- Test: No modification specified ---"
OUT=$($PHP $MOOSH activity:mod -p "$MOODLE_PATH" --run $FORUM_CMID 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for no modification" 1 "$EXIT_CODE"
assert_output_contains "Error for no modification" "No modifications specified" "$OUT"
echo ""

# ── Invalid cmid ──────────────────────────────────────────────────

echo "--- Test: Invalid cmid ---"
OUT=$($PHP $MOOSH activity:mod -p "$MOODLE_PATH" --run --name "X" 99999 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for invalid cmid" 1 "$EXIT_CODE"
assert_output_contains "Error for invalid cmid" "not found" "$OUT"
echo ""

# ── Help ──────────────────────────────────────────────────────────

echo "--- Test: activity:mod help ---"
OUT=$($PHP $MOOSH activity:mod -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Modify an activity" "$OUT"
assert_output_contains "Help shows --name" "--name" "$OUT"
assert_output_contains "Help shows --visible" "--visible" "$OUT"
assert_output_contains "Help shows --section" "--section" "$OUT"
assert_output_contains "Help shows --before" "--before" "$OUT"
echo ""

# ── Alias ─────────────────────────────────────────────────────────


# ═══════════════════════════════════════════════════════════════════
# activity:delete
# ═══════════════════════════════════════════════════════════════════

echo "========== activity:delete =========="
echo ""

# ── Dry run ───────────────────────────────────────────────────────

echo "--- Test: activity:delete dry run ---"
OUT=$($PHP $MOOSH activity:delete -p "$MOODLE_PATH" $FORUM_CMID)
echo "$OUT"
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows cmid" "cmid=$FORUM_CMID" "$OUT"
assert_output_contains "Shows type" "forum" "$OUT"
echo ""

# ── Delete single activity ────────────────────────────────────────

echo "--- Test: Delete single activity ---"
OUT=$($PHP $MOOSH activity:delete -p "$MOODLE_PATH" --run $FORUM_CMID)
echo "$OUT"
assert_output_contains "Deleted message" "Deleted" "$OUT"
assert_output_contains "Shows forum type" "forum" "$OUT"
echo ""

# ── Delete multiple activities ────────────────────────────────────

echo "--- Test: Delete multiple activities ---"
OUT=$($PHP $MOOSH activity:delete -p "$MOODLE_PATH" --run $ASSIGN_CMID $PAGE_CMID)
echo "$OUT"
assert_output_contains "First deleted" "Deleted" "$OUT"
echo ""

# ── Invalid cmid ──────────────────────────────────────────────────

echo "--- Test: Delete invalid cmid ---"
OUT=$($PHP $MOOSH activity:delete -p "$MOODLE_PATH" --run 99999 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for invalid cmid" 1 "$EXIT_CODE"
assert_output_contains "Error for invalid cmid" "not found" "$OUT"
echo ""

# ── Help ──────────────────────────────────────────────────────────

echo "--- Test: activity:delete help ---"
OUT=$($PHP $MOOSH activity:delete -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Delete activities" "$OUT"
assert_output_contains "Help shows cmid" "cmid" "$OUT"
echo ""

# ── Alias ─────────────────────────────────────────────────────────


print_summary
