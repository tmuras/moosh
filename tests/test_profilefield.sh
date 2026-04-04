#!/usr/bin/env bash
#
# Integration test for moosh2 profilefield:create, :info, :delete, :export, :import
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_profilefield.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 profilefield commands integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ═══════════════════════════════════════════════════════════════════
# profilefield:create
# ═══════════════════════════════════════════════════════════════════

echo "========== profilefield:create =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH profilefield:create -p "$MOODLE_PATH" testfield)
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Create text field ---"
OUT=$($PHP $MOOSH profilefield:create -p "$MOODLE_PATH" --run --name "Employee ID" --category "HR" employeeid -o csv)
echo "$OUT"
assert_output_contains "Header" "id,shortname,name,datatype,category" "$OUT"
assert_output_contains "Shortname" "employeeid" "$OUT"
assert_output_contains "Category" "HR" "$OUT"
assert_output_contains "Type text" ",text," "$OUT"
FIELD1_ID=$(echo "$OUT" | tail -1 | cut -d, -f1)
echo ""

echo "--- Test: Create datetime field ---"
OUT=$($PHP $MOOSH profilefield:create -p "$MOODLE_PATH" --run --name "Start Date" --datatype datetime --category "HR" startdate -o csv)
assert_output_contains "Datetime type" ",datetime," "$OUT"
FIELD2_ID=$(echo "$OUT" | tail -1 | cut -d, -f1)
echo ""

echo "--- Test: Create checkbox field ---"
OUT=$($PHP $MOOSH profilefield:create -p "$MOODLE_PATH" --run --name "Active" --datatype checkbox --required 1 --visible 1 isactive -o csv)
assert_output_contains "Checkbox type" ",checkbox," "$OUT"
FIELD3_ID=$(echo "$OUT" | tail -1 | cut -d, -f1)
echo ""

echo "--- Test: Duplicate shortname rejected ---"
OUT=$($PHP $MOOSH profilefield:create -p "$MOODLE_PATH" --run employeeid 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for duplicate" 1 "$EXIT_CODE"
assert_output_contains "Already exists error" "already exists" "$OUT"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH profilefield:create -p "$MOODLE_PATH" --run --name "JSON Field" jsonfield -o json)
assert_output_contains "JSON has shortname" '"shortname"' "$OUT"
assert_output_contains "JSON has jsonfield" '"jsonfield"' "$OUT"
FIELD4_ID=$(echo "$OUT" | grep -o '"id": [0-9]*' | head -1 | grep -o '[0-9]*')
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH profilefield:create -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Create a user profile field" "$OUT"
assert_output_contains "Help shows --datatype" "--datatype" "$OUT"
assert_output_contains "Help shows --category" "--category" "$OUT"
echo ""

# ═══════════════════════════════════════════════════════════════════
# profilefield:info
# ═══════════════════════════════════════════════════════════════════

echo "========== profilefield:info =========="
echo ""

echo "--- Test: Info for text field ---"
OUT=$($PHP $MOOSH profilefield:info -p "$MOODLE_PATH" $FIELD1_ID -o json)
echo "$OUT" | head -10
assert_output_contains "Shortname" '"Shortname": "employeeid"' "$OUT"
assert_output_contains "Name" '"Employee ID"' "$OUT"
assert_output_contains "Data type" '"Data type": "text"' "$OUT"
assert_output_contains "Category HR" '"Category": "HR"' "$OUT"
assert_output_contains "Visible" '"Visible"' "$OUT"
assert_output_contains "Users with data" '"Users with data": 0' "$OUT"
echo ""

echo "--- Test: Table output ---"
OUT=$($PHP $MOOSH profilefield:info -p "$MOODLE_PATH" $FIELD1_ID)
assert_output_contains "Table has Metric" "Metric" "$OUT"
assert_output_contains "Table has employeeid" "employeeid" "$OUT"
echo ""

echo "--- Test: Invalid field ---"
OUT=$($PHP $MOOSH profilefield:info -p "$MOODLE_PATH" 99999 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1" 1 "$EXIT_CODE"
assert_output_contains "Not found" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH profilefield:info -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Show detailed information" "$OUT"
echo ""

# ═══════════════════════════════════════════════════════════════════
# profilefield:export
# ═══════════════════════════════════════════════════════════════════

echo "========== profilefield:export =========="
echo ""

echo "--- Test: Export to stdout (CSV) ---"
OUT=$($PHP $MOOSH profilefield:export -p "$MOODLE_PATH" -o csv)
echo "$OUT" | head -3
assert_output_contains "Header has shortname" "shortname" "$OUT"
assert_output_contains "Header has categoryname" "categoryname" "$OUT"
assert_output_contains "Has employeeid" "employeeid" "$OUT"
assert_output_contains "Has startdate" "startdate" "$OUT"
echo ""

echo "--- Test: Export to file ---"
OUT=$($PHP $MOOSH profilefield:export -p "$MOODLE_PATH" --file /tmp/test_pf.csv)
assert_output_contains "Export message" "Exported" "$OUT"
if [ -f /tmp/test_pf.csv ]; then
    echo "  PASS: File created"
    ((PASS++))
else
    echo "  FAIL: File not created"
    ((FAIL++))
fi
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH profilefield:export -p "$MOODLE_PATH" -o json)
assert_output_contains "JSON has shortname" '"shortname"' "$OUT"
assert_output_contains "JSON has employeeid" '"employeeid"' "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH profilefield:export -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Export user profile fields" "$OUT"
assert_output_contains "Help shows --file" "--file" "$OUT"
echo ""

# ═══════════════════════════════════════════════════════════════════
# profilefield:delete
# ═══════════════════════════════════════════════════════════════════

echo "========== profilefield:delete =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH profilefield:delete -p "$MOODLE_PATH" $FIELD4_ID)
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows shortname" "jsonfield" "$OUT"
echo ""

echo "--- Test: Delete single field ---"
OUT=$($PHP $MOOSH profilefield:delete -p "$MOODLE_PATH" --run $FIELD4_ID)
assert_output_contains "Deleted message" "Deleted" "$OUT"
assert_output_contains "Shows jsonfield" "jsonfield" "$OUT"
# Verify gone
OUT2=$($PHP $MOOSH profilefield:info -p "$MOODLE_PATH" $FIELD4_ID 2>&1)
assert_output_contains "Field is gone" "not found" "$OUT2"
echo ""

echo "--- Test: Invalid field ---"
OUT=$($PHP $MOOSH profilefield:delete -p "$MOODLE_PATH" --run 99999 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1" 1 "$EXIT_CODE"
assert_output_contains "Not found" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH profilefield:delete -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Delete user profile fields" "$OUT"
echo ""

# ═══════════════════════════════════════════════════════════════════
# profilefield:import
# ═══════════════════════════════════════════════════════════════════

echo "========== profilefield:import =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH profilefield:import -p "$MOODLE_PATH" /tmp/test_pf.csv)
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows SKIP for existing" "SKIP" "$OUT"
echo ""

echo "--- Test: Delete all and reimport ---"
$PHP $MOOSH profilefield:delete -p "$MOODLE_PATH" --run $FIELD1_ID $FIELD2_ID $FIELD3_ID > /dev/null 2>&1
OUT=$($PHP $MOOSH profilefield:import -p "$MOODLE_PATH" --run /tmp/test_pf.csv)
echo "$OUT"
assert_output_contains "Import message" "Imported" "$OUT"
assert_output_contains "Shows count" "field(s)" "$OUT"
# Verify fields exist again
OUT2=$($PHP $MOOSH profilefield:export -p "$MOODLE_PATH" -o csv)
assert_output_contains "Reimported employeeid" "employeeid" "$OUT2"
assert_output_contains "Reimported startdate" "startdate" "$OUT2"
echo ""

echo "--- Test: Skip existing on reimport ---"
OUT=$($PHP $MOOSH profilefield:import -p "$MOODLE_PATH" --run /tmp/test_pf.csv)
assert_output_contains "Skipped existing" "skipped" "$OUT"
echo ""

echo "--- Test: Missing file ---"
OUT=$($PHP $MOOSH profilefield:import -p "$MOODLE_PATH" --run /tmp/nonexistent.csv 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for missing file" 1 "$EXIT_CODE"
assert_output_contains "File not found" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH profilefield:import -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Import user profile fields" "$OUT"
echo ""

# ── Aliases ───────────────────────────────────────────────────────


# ── Cleanup ───────────────────────────────────────────────────────
rm -f /tmp/test_pf.csv

print_summary
