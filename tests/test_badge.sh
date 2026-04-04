#!/usr/bin/env bash
#
# Integration test for moosh2 badge:create, badge:info, badge:mod, badge:delete commands
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_badge.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 badge commands integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ═══════════════════════════════════════════════════════════════════
# badge:create
# ═══════════════════════════════════════════════════════════════════

echo "========== badge:create =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH badge:create -p "$MOODLE_PATH" "Dry Run Badge")
echo "$OUT"
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows badge name" "Dry Run Badge" "$OUT"
assert_output_contains "Shows site type" "site" "$OUT"
echo ""

echo "--- Test: Create site badge ---"
OUT=$($PHP $MOOSH badge:create -p "$MOODLE_PATH" --run -d "A site-level badge" "Site Badge" -o csv)
echo "$OUT"
assert_output_contains "Header row" "id,name,type,courseid,status" "$OUT"
assert_output_contains "Badge name" "Site Badge" "$OUT"
assert_output_contains "Type is site" ",site," "$OUT"
assert_output_contains "Status is inactive" "inactive" "$OUT"
SITE_BADGE_ID=$(echo "$OUT" | tail -1 | cut -d, -f1)
echo "  Created site badge ID=$SITE_BADGE_ID"
echo ""

echo "--- Test: Create course badge ---"
OUT=$($PHP $MOOSH badge:create -p "$MOODLE_PATH" --run --course 2 -d "Course-level badge" "Course Badge" -o csv)
echo "$OUT"
assert_output_contains "Course badge type" ",course," "$OUT"
assert_output_contains "Course ID 2" ",2," "$OUT"
COURSE_BADGE_ID=$(echo "$OUT" | tail -1 | cut -d, -f1)
echo "  Created course badge ID=$COURSE_BADGE_ID"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH badge:create -p "$MOODLE_PATH" --run "JSON Badge" -o json)
echo "$OUT"
assert_output_contains "JSON has name" '"name"' "$OUT"
assert_output_contains "JSON has JSON Badge" '"JSON Badge"' "$OUT"
JSON_BADGE_ID=$(echo "$OUT" | grep -o '"id": [0-9]*' | head -1 | grep -o '[0-9]*')
echo ""

echo "--- Test: Invalid course ---"
OUT=$($PHP $MOOSH badge:create -p "$MOODLE_PATH" --run --course 99999 "Bad Badge" 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for invalid course" 1 "$EXIT_CODE"
assert_output_contains "Course not found" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH badge:create -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Create a badge" "$OUT"
assert_output_contains "Help shows --course" "--course" "$OUT"
assert_output_contains "Help shows --description" "--description" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
# badge:info
# ═══════════════════════════════════════════════════════════════════

echo "========== badge:info =========="
echo ""

echo "--- Test: Site badge info ---"
OUT=$($PHP $MOOSH badge:info -p "$MOODLE_PATH" $SITE_BADGE_ID -o json)
echo "$OUT" | head -15
assert_output_contains "Badge ID" "\"Badge ID\": \"$SITE_BADGE_ID\"" "$OUT"
assert_output_contains "Name" '"Site Badge"' "$OUT"
assert_output_contains "Description" '"A site-level badge"' "$OUT"
assert_output_contains "Type site" '"Type": "site"' "$OUT"
assert_output_contains "Status inactive" '"Status": "inactive"' "$OUT"
assert_output_contains "Created by admin" '"Created by": "admin"' "$OUT"
assert_output_contains "Times awarded 0" '"Times awarded": 0' "$OUT"
assert_output_contains "Criteria count 0" '"Criteria count": 0' "$OUT"
assert_output_contains "Expiry never" '"Expiry": "never"' "$OUT"
echo ""

echo "--- Test: Course badge info ---"
OUT=$($PHP $MOOSH badge:info -p "$MOODLE_PATH" $COURSE_BADGE_ID -o json)
assert_output_contains "Type course" '"Type": "course"' "$OUT"
assert_output_contains "Course ID" '"Course ID"' "$OUT"
assert_output_contains "Course name" '"Algebra Fundamentals"' "$OUT"
echo ""

echo "--- Test: Table output ---"
OUT=$($PHP $MOOSH badge:info -p "$MOODLE_PATH" $SITE_BADGE_ID)
assert_output_contains "Table has Metric header" "Metric" "$OUT"
assert_output_contains "Table has badge name" "Site Badge" "$OUT"
echo ""

echo "--- Test: Invalid badge ---"
OUT=$($PHP $MOOSH badge:info -p "$MOODLE_PATH" 99999 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for invalid badge" 1 "$EXIT_CODE"
assert_output_contains "Not found error" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH badge:info -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Show detailed information about a badge" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
# badge:mod
# ═══════════════════════════════════════════════════════════════════

echo "========== badge:mod =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH badge:mod -p "$MOODLE_PATH" --name "New Name" $SITE_BADGE_ID)
echo "$OUT"
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows name change" "name:" "$OUT"
echo ""

echo "--- Test: Rename badge ---"
OUT=$($PHP $MOOSH badge:mod -p "$MOODLE_PATH" --run --name "Renamed Badge" $SITE_BADGE_ID -o csv)
echo "$OUT"
assert_output_contains "Renamed" "Renamed Badge" "$OUT"
echo ""

echo "--- Test: Update description ---"
OUT=$($PHP $MOOSH badge:mod -p "$MOODLE_PATH" --run -d "New description" $SITE_BADGE_ID -o csv)
assert_output_contains "Badge still there" "Renamed Badge" "$OUT"
# Verify via badge:info
VERIFY=$($PHP $MOOSH badge:info -p "$MOODLE_PATH" $SITE_BADGE_ID -o json)
assert_output_contains "Description updated" '"New description"' "$VERIFY"
echo ""

echo "--- Test: No modification ---"
OUT=$($PHP $MOOSH badge:mod -p "$MOODLE_PATH" --run $SITE_BADGE_ID 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for no modification" 1 "$EXIT_CODE"
assert_output_contains "No modifications error" "No modifications" "$OUT"
echo ""

echo "--- Test: Invalid status ---"
OUT=$($PHP $MOOSH badge:mod -p "$MOODLE_PATH" --run --status invalid $SITE_BADGE_ID 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for invalid status" 1 "$EXIT_CODE"
assert_output_contains "Invalid status error" "Invalid status" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH badge:mod -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Modify a badge" "$OUT"
assert_output_contains "Help shows --name" "--name" "$OUT"
assert_output_contains "Help shows --status" "--status" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
# badge:delete
# ═══════════════════════════════════════════════════════════════════

echo "========== badge:delete =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH badge:delete -p "$MOODLE_PATH" $COURSE_BADGE_ID)
echo "$OUT"
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows badge ID" "ID=$COURSE_BADGE_ID" "$OUT"
echo ""

echo "--- Test: Delete single badge ---"
OUT=$($PHP $MOOSH badge:delete -p "$MOODLE_PATH" --run $COURSE_BADGE_ID)
echo "$OUT"
assert_output_contains "Deleted message" "Deleted" "$OUT"
assert_output_contains "Shows badge name" "Course Badge" "$OUT"
# Verify gone
OUT2=$($PHP $MOOSH badge:info -p "$MOODLE_PATH" $COURSE_BADGE_ID 2>&1)
assert_output_contains "Badge is gone" "not found" "$OUT2"
echo ""

echo "--- Test: Delete multiple badges ---"
OUT=$($PHP $MOOSH badge:delete -p "$MOODLE_PATH" --run $JSON_BADGE_ID)
echo "$OUT"
assert_output_contains "First deleted" "Deleted" "$OUT"
echo ""

echo "--- Test: Invalid badge ID ---"
OUT=$($PHP $MOOSH badge:delete -p "$MOODLE_PATH" --run 99999 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for invalid badge" 1 "$EXIT_CODE"
assert_output_contains "Not found error" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH badge:delete -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Delete badges" "$OUT"
echo ""


print_summary
