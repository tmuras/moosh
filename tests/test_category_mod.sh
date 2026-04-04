#!/usr/bin/env bash
#
# Integration tests for moosh2 category:mod, category:export, category:import
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_category_mod.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 category mod/export/import integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

TMPDIR=$(mktemp -d)
trap "rm -rf $TMPDIR" EXIT

# Test data: categories 1-5 exist, courses in categories 2-5

# ═══════════════════════════════════════════════════════════════════
#  category:mod
# ═══════════════════════════════════════════════════════════════════

echo "========== category:mod =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH category:mod 2 --name "Renamed" -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Rename ---"
OUT=$($PHP $MOOSH category:mod 2 --name "Math Renamed" -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Rename exit code 0" 0 $EC
assert_output_contains "Shows renamed" "Math Renamed" "$OUT"
echo ""

echo "--- Test: Set idnumber ---"
OUT=$($PHP $MOOSH category:mod 2 --idnumber MATH -p "$MOODLE_PATH" --run -o csv 2>&1)
EC=$?
assert_exit_code "Idnumber exit code 0" 0 $EC
assert_output_contains "CSV has MATH" "MATH" "$OUT"
echo ""

echo "--- Test: Set visibility ---"
OUT=$($PHP $MOOSH category:mod 2 --visible 0 -p "$MOODLE_PATH" --run -o csv 2>&1)
EC=$?
assert_exit_code "Visible exit code 0" 0 $EC
# Restore
$PHP $MOOSH category:mod 2 --visible 1 -p "$MOODLE_PATH" --run > /dev/null 2>&1
echo ""

echo "--- Test: Move to parent ---"
# Create a temp parent
PARENT_OUT=$($PHP $MOOSH category:create "TempParent" -p "$MOODLE_PATH" --run -o csv 2>&1)
PARENT_ID=$(echo "$PARENT_OUT" | tail -1 | cut -d, -f1)
OUT=$($PHP $MOOSH category:mod 2 --parent $PARENT_ID -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Move parent exit code 0" 0 $EC
assert_output_contains "Shows parent" "$PARENT_ID" "$OUT"
# Move back to top
$PHP $MOOSH category:mod 2 --parent 0 -p "$MOODLE_PATH" --run > /dev/null 2>&1
echo ""

echo "--- Test: Sort order ---"
OUT=$($PHP $MOOSH category:mod 2 --sortorder first -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Sortorder exit code 0" 0 $EC
echo ""

echo "--- Test: Resort subcategories ---"
# Create subcategories
$PHP $MOOSH category:create "ZZZ Sub" -p "$MOODLE_PATH" --parent 2 --run > /dev/null 2>&1
$PHP $MOOSH category:create "AAA Sub" -p "$MOODLE_PATH" --parent 2 --run > /dev/null 2>&1
OUT=$($PHP $MOOSH category:mod 2 --resort name -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Resort exit code 0" 0 $EC
assert_output_contains "Shows subcategories" "2" "$OUT"
echo ""

echo "--- Test: Resort courses ---"
OUT=$($PHP $MOOSH category:mod 2 --resort-courses fullname -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Resort courses exit code 0" 0 $EC
echo ""

echo "--- Test: Move courses ---"
OUT=$($PHP $MOOSH category:mod 2 --move-courses 3 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Move courses exit code 0" 0 $EC
assert_output_contains "Shows moved" "Moved" "$OUT"
# Move back
$PHP $MOOSH category:mod 3 --move-courses 2 -p "$MOODLE_PATH" --run > /dev/null 2>&1
echo ""

echo "--- Test: No modification ---"
OUT=$($PHP $MOOSH category:mod 2 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for no mod" 1 $EC
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH category:mod 2 --description "Updated" -p "$MOODLE_PATH" --run -o json 2>&1)
assert_output_contains "JSON has name" '"name"' "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH category:mod -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Modify, move, resort" "$OUT"
assert_output_contains "Help shows --parent" "--parent" "$OUT"
assert_output_contains "Help shows --sortorder" "--sortorder" "$OUT"
assert_output_contains "Help shows --resort" "--resort" "$OUT"
assert_output_contains "Help shows --move-courses" "--move-courses" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  category:export
# ═══════════════════════════════════════════════════════════════════

echo "========== category:export =========="
echo ""

echo "--- Test: Export all ---"
OUT=$($PHP $MOOSH category:export 0 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Export all exit code 0" 0 $EC
assert_output_contains "XML has categories tag" "<categories>" "$OUT"
assert_output_contains "XML has category tag" "<category>" "$OUT"
assert_output_contains "XML has name" "<name>" "$OUT"
echo ""

echo "--- Test: Export single ---"
OUT=$($PHP $MOOSH category:export 2 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Export single exit code 0" 0 $EC
assert_output_contains "Shows Math" "Math" "$OUT"
echo ""

echo "--- Test: Export includes subcategories ---"
OUT=$($PHP $MOOSH category:export 2 -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Has subcategories" "subcategories" "$OUT"
echo ""

echo "--- Test: Export to file ---"
$PHP $MOOSH category:export 0 -p "$MOODLE_PATH" > "$TMPDIR/cats.xml" 2>&1
if [ -s "$TMPDIR/cats.xml" ]; then
    echo "  PASS: Export file not empty"
    ((PASS++))
else
    echo "  FAIL: Export file is empty"
    ((FAIL++))
fi
echo ""

echo "--- Test: Invalid category ---"
OUT=$($PHP $MOOSH category:export 99999 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid category" 1 $EC
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH category:export -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Export category tree" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  category:import
# ═══════════════════════════════════════════════════════════════════

echo "========== category:import =========="
echo ""

# Create a simple XML for import
cat > "$TMPDIR/import.xml" << 'XMLEOF'
<?xml version="1.0" encoding="UTF-8"?>
<categories>
  <category>
    <name>Imported Category</name>
    <idnumber>IMP01</idnumber>
    <description>Test import</description>
    <visible>1</visible>
    <subcategories>
      <category>
        <name>Imported Sub</name>
        <idnumber>IMP02</idnumber>
        <description>Sub category</description>
        <visible>1</visible>
      </category>
    </subcategories>
  </category>
</categories>
XMLEOF

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH category:import "$TMPDIR/import.xml" -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows count" "2" "$OUT"
assert_output_contains "Shows preview" "Imported Category" "$OUT"
echo ""

echo "--- Test: Import ---"
OUT=$($PHP $MOOSH category:import "$TMPDIR/import.xml" -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Import exit code 0" 0 $EC
assert_output_contains "Shows imported" "Imported 2" "$OUT"
echo ""

echo "--- Test: Import skips existing ---"
OUT=$($PHP $MOOSH category:import "$TMPDIR/import.xml" -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Skip existing exit code 0" 0 $EC
assert_output_contains "Shows skipped" "skipped 2" "$OUT"
echo ""

echo "--- Test: Import with parent ---"
cat > "$TMPDIR/import_parent.xml" << 'XMLEOF2'
<?xml version="1.0" encoding="UTF-8"?>
<categories>
  <category>
    <name>Under Parent</name>
    <idnumber></idnumber>
    <description>Imported under parent</description>
    <visible>1</visible>
  </category>
</categories>
XMLEOF2
OUT=$($PHP $MOOSH category:import "$TMPDIR/import_parent.xml" --parent 2 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Parent import exit code 0" 0 $EC
assert_output_contains "Shows imported" "Imported 1" "$OUT"
echo ""

echo "--- Test: Round-trip export/import ---"
# Export all, then re-import should skip everything
$PHP $MOOSH category:export 0 -p "$MOODLE_PATH" > "$TMPDIR/roundtrip.xml" 2>&1
OUT=$($PHP $MOOSH category:import "$TMPDIR/roundtrip.xml" -p "$MOODLE_PATH" --run 2>&1)
assert_output_contains "Round-trip skips all" "skipped" "$OUT"
echo ""

echo "--- Test: Invalid file ---"
OUT=$($PHP $MOOSH category:import /nonexistent.xml -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid file" 1 $EC
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH category:import -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Import categories" "$OUT"
assert_output_contains "Help shows --parent" "--parent" "$OUT"
echo ""


print_summary
