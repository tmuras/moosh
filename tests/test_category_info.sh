#!/usr/bin/env bash
#
# Integration test for moosh2 category:info command
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_category_info.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 category:info integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Test data:
#   Category 1 (id=1): empty default category
#   Mathematics (id=2): 3 courses, 60 unique users, 180 enrolments, 3 activities
#   Sciences (id=3): 3 courses
#   Humanities (id=4): 3 courses
#   Computer Science (id=5): 6 courses (incl. empty course)

# ── Basic table output ────────────────────────────────────────────

echo "--- Test: Basic table output (Mathematics) ---"
OUT=$($PHP $MOOSH category:info -p "$MOODLE_PATH" 2)
echo "$OUT"
assert_output_contains "Shows Category ID" "Category ID" "$OUT"
assert_output_contains "Shows Name" "Mathematics" "$OUT"
assert_output_contains "Shows Visible" "Visible" "$OUT"
assert_output_contains "Shows Depth" "Depth" "$OUT"
assert_output_contains "Shows Path" "Path" "$OUT"
assert_output_contains "Shows Direct courses" "Direct courses" "$OUT"
assert_output_contains "Shows Unique enrolled users" "Unique enrolled users" "$OUT"
assert_output_contains "Shows Total enrolments" "Total enrolments" "$OUT"
assert_output_contains "Shows Total activities" "Total activities" "$OUT"
assert_output_contains "Shows Total files" "Total files" "$OUT"
echo ""

# ── CSV output ────────────────────────────────────────────────────

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH category:info -p "$MOODLE_PATH" 2 -o csv)
echo "$OUT" | head -2
assert_output_contains "CSV has Category ID header" '"Category ID"' "$OUT"
assert_output_contains "CSV has Name header" "Name" "$OUT"
assert_output_contains "CSV has Direct courses header" '"Direct courses"' "$OUT"
echo ""

# ── JSON output ───────────────────────────────────────────────────

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH category:info -p "$MOODLE_PATH" 2 -o json)
echo "$OUT" | head -10
assert_output_contains "JSON has Category ID key" '"Category ID"' "$OUT"
assert_output_contains "JSON has Name key" '"Name"' "$OUT"
assert_output_contains "JSON has Mathematics value" '"Mathematics"' "$OUT"
echo ""

# ── Mathematics category (id=2) ──────────────────────────────────

echo "--- Test: Mathematics category details ---"
OUT=$($PHP $MOOSH category:info -p "$MOODLE_PATH" 2 -o json)
assert_output_contains "3 direct courses" '"Direct courses": 3' "$OUT"
assert_output_contains "3 visible courses" '"Visible courses": 3' "$OUT"
assert_output_contains "0 hidden courses" '"Hidden courses": 0' "$OUT"
assert_output_contains "0 direct subcategories" '"Direct subcategories": 0' "$OUT"
assert_output_contains "3 total courses recursive" '"Total courses (recursive)": 3' "$OUT"
assert_output_contains "60 unique enrolled users" '"Unique enrolled users": 60' "$OUT"
assert_output_contains "180 total enrolments" '"Total enrolments": 180' "$OUT"
assert_output_contains "3 total activities" '"Total activities": 3' "$OUT"
assert_output_contains "Top-level parent" '"Parent": "none (top-level)"' "$OUT"
assert_output_contains "Depth 1" '"Depth": 1' "$OUT"
echo ""

# ── Computer Science category (id=5) ─────────────────────────────

echo "--- Test: Computer Science category ---"
OUT=$($PHP $MOOSH category:info -p "$MOODLE_PATH" 5 -o json)
assert_output_contains "CS name" '"Computer Science"' "$OUT"
assert_output_contains "7 direct courses" '"Direct courses": 7' "$OUT"
assert_output_contains "7 total courses" '"Total courses (recursive)": 7' "$OUT"
assert_output_contains "6 total activities" '"Total activities": 6' "$OUT"
echo ""

# ── Empty category (id=1) ────────────────────────────────────────

echo "--- Test: Empty category ---"
OUT=$($PHP $MOOSH category:info -p "$MOODLE_PATH" 1 -o json)
assert_output_contains "Category 1 name" '"Category 1"' "$OUT"
assert_output_contains "0 direct courses" '"Direct courses": 0' "$OUT"
assert_output_contains "0 total courses" '"Total courses (recursive)": 0' "$OUT"
assert_output_contains "0 enrolled users" '"Unique enrolled users": 0' "$OUT"
assert_output_contains "0 enrolments" '"Total enrolments": 0' "$OUT"
assert_output_contains "0 activities" '"Total activities": 0' "$OUT"
assert_output_contains "0 files" '"Total files": 0' "$OUT"
assert_output_contains "0 file size" '"Total file size (bytes)": 0' "$OUT"
echo ""

# ── Files in category ─────────────────────────────────────────────

echo "--- Test: Files in Mathematics ---"
OUT=$($PHP $MOOSH category:info -p "$MOODLE_PATH" 2 -o json)
assert_output_contains "3 files" '"Total files": 3' "$OUT"
# Each course has 1 file resource with 33 bytes
assert_output_contains "99 bytes total" '"Total file size (bytes)": 99' "$OUT"
echo ""

# ── Role assignments ──────────────────────────────────────────────

echo "--- Test: Category role assignments ---"
OUT=$($PHP $MOOSH category:info -p "$MOODLE_PATH" 2 -o json)
assert_output_contains "0 category role assignments" '"Category role assignments": 0' "$OUT"
echo ""

# ── Invalid category ID ──────────────────────────────────────────

echo "--- Test: Invalid category ID ---"
OUT=$($PHP $MOOSH category:info -p "$MOODLE_PATH" 999 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code is 1 for invalid category" 1 "$EXIT_CODE"
assert_output_contains "Error message for invalid category" "not found" "$OUT"
echo ""

# ── Missing categoryid argument ──────────────────────────────────

echo "--- Test: Missing categoryid argument ---"
OUT=$($PHP $MOOSH category:info -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Error mentions missing argument" "categoryid" "$OUT"
echo ""

# ── Help output ───────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH category:info -p "$MOODLE_PATH" --help)
assert_output_contains "Help shows description" "Show detailed information about a course category" "$OUT"
assert_output_contains "Help shows categoryid argument" "categoryid" "$OUT"
echo ""

# ── category-info alias ──────────────────────────────────────────


print_summary
