#!/usr/bin/env bash
#
# Integration tests for moosh2 file commands
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_file.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 file commands integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

TMPDIR=$(mktemp -d)
trap "rm -rf $TMPDIR" EXIT

# ═══════════════════════════════════════════════════════════════════
#  file:stats
# ═══════════════════════════════════════════════════════════════════

echo "========== file:stats =========="
echo ""

echo "--- Test: Basic stats ---"
OUT=$($PHP $MOOSH file:stats -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Stats exit code 0" 0 $EC
assert_output_contains "Shows total" "Total file records" "$OUT"
assert_output_contains "Shows unique" "Unique content" "$OUT"
echo ""

echo "--- Test: By component ---"
OUT=$($PHP $MOOSH file:stats --by-component -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Shows by component" "By Component" "$OUT"
assert_output_contains "Shows a component" "component" "$OUT"
echo ""

echo "--- Test: Top largest ---"
OUT=$($PHP $MOOSH file:stats --top 5 -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Shows top files" "Top 5 Largest" "$OUT"
echo ""

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH file:stats -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "CSV has metric" "Metric,Value" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH file:stats -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Show file storage statistics" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  file:list
# ═══════════════════════════════════════════════════════════════════

echo "========== file:list =========="
echo ""

echo "--- Test: List by course ---"
OUT=$($PHP $MOOSH file:list --courseid 2 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "List exit code 0" 0 $EC
assert_output_contains "Shows filename" "coursefile" "$OUT"
echo ""

echo "--- Test: List by component ---"
OUT=$($PHP $MOOSH file:list --component mod_resource -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Shows resource files" "mod_resource" "$OUT"
echo ""

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH file:list --courseid 2 -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "CSV header" "id,filename,component" "$OUT"
echo ""

echo "--- Test: ID-only ---"
OUT=$($PHP $MOOSH file:list --courseid 2 -p "$MOODLE_PATH" --id-only 2>&1)
assert_output_not_empty "ID-only not empty" "$OUT"
FILE_ID=$(echo "$OUT" | tr ' ' '\n' | head -1)
echo "  First file ID: $FILE_ID"
echo ""

echo "--- Test: No filter ---"
OUT=$($PHP $MOOSH file:list -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for no filter" 1 $EC
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH file:list -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "List files" "$OUT"
assert_output_contains "Help shows --courseid" "--courseid" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  file:info
# ═══════════════════════════════════════════════════════════════════

echo "========== file:info =========="
echo ""

echo "--- Test: Info by ID ---"
OUT=$($PHP $MOOSH file:info $FILE_ID -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Info exit code 0" 0 $EC
assert_output_contains "Shows file ID" "File ID" "$OUT"
assert_output_contains "Shows content hash" "Content hash" "$OUT"
assert_output_contains "Shows physical path" "Physical path" "$OUT"
assert_output_contains "Shows exists" "Exists on disk" "$OUT"
assert_output_contains "Shows component" "Component" "$OUT"
echo ""

# Get the hash for hash lookup test
HASH=$($PHP $MOOSH file:info $FILE_ID -p "$MOODLE_PATH" -o csv 2>&1 | grep "Content hash" | cut -d, -f2)
echo "  File hash: $HASH"

echo "--- Test: Info by hash ---"
OUT=$($PHP $MOOSH file:info --hash "$HASH" -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Hash info exit code 0" 0 $EC
assert_output_contains "Shows file info" "File ID" "$OUT"
echo ""

echo "--- Test: Invalid ID ---"
OUT=$($PHP $MOOSH file:info 99999 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid ID" 1 $EC
echo ""

echo "--- Test: No args ---"
OUT=$($PHP $MOOSH file:info -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for no args" 1 $EC
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH file:info -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Show detailed file information" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  file:check
# ═══════════════════════════════════════════════════════════════════

echo "========== file:check =========="
echo ""

echo "--- Test: Check missing ---"
OUT=$($PHP $MOOSH file:check --missing -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Check missing exit code 0" 0 $EC
assert_output_contains "Shows checked" "Checked" "$OUT"
assert_output_contains "Shows missing result" "missing" "$OUT"
echo ""

echo "--- Test: Check orphaned ---"
OUT=$($PHP $MOOSH file:check --orphaned -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Check orphaned exit code 0" 0 $EC
assert_output_contains "Shows orphaned result" "Checked" "$OUT"
echo ""

echo "--- Test: Default (missing) ---"
OUT=$($PHP $MOOSH file:check -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Default check exit code 0" 0 $EC
assert_output_contains "Shows missing section" "Missing Files" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH file:check -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Check file storage consistency" "$OUT"
assert_output_contains "Help shows --missing" "--missing" "$OUT"
assert_output_contains "Help shows --orphaned" "--orphaned" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  file:upload
# ═══════════════════════════════════════════════════════════════════

echo "========== file:upload =========="
echo ""

# Create a test file
echo "Hello from moosh2 test" > "$TMPDIR/testfile.txt"

# Get context ID for course 2
CTX_ID=$($PHP $MOOSH sql:select -p "$MOODLE_PATH" "SELECT id FROM mdl_context WHERE contextlevel=50 AND instanceid=2" -o csv 2>&1 | tail -1)
echo "  Course 2 context ID: $CTX_ID"

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH file:upload "$TMPDIR/testfile.txt" --contextid $CTX_ID --component course --filearea summary -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Upload file ---"
OUT=$($PHP $MOOSH file:upload "$TMPDIR/testfile.txt" --contextid $CTX_ID --component course --filearea summary -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Upload exit code 0" 0 $EC
assert_output_contains "Shows filename" "testfile.txt" "$OUT"
assert_output_contains "Shows component" "course" "$OUT"
echo ""

echo "--- Test: Missing file ---"
OUT=$($PHP $MOOSH file:upload /nonexistent.txt --contextid $CTX_ID --component course --filearea summary -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for missing file" 1 $EC
echo ""

echo "--- Test: Missing options ---"
OUT=$($PHP $MOOSH file:upload "$TMPDIR/testfile.txt" -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for missing options" 1 $EC
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH file:upload -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Upload a file" "$OUT"
assert_output_contains "Help shows --contextid" "--contextid" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  file:delete
# ═══════════════════════════════════════════════════════════════════

echo "========== file:delete =========="
echo ""

# Upload a file to delete
echo "delete me" > "$TMPDIR/deleteme.txt"
DEL_OUT=$($PHP $MOOSH file:upload "$TMPDIR/deleteme.txt" --contextid $CTX_ID --component course --filearea summary --itemid 999 -p "$MOODLE_PATH" --run -o csv 2>&1)
DEL_FILE_ID=$(echo "$DEL_OUT" | tail -1 | cut -d, -f1)
echo "  File to delete ID: $DEL_FILE_ID"

echo "--- Test: Delete dry run ---"
OUT=$($PHP $MOOSH file:delete $DEL_FILE_ID -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Delete by ID ---"
OUT=$($PHP $MOOSH file:delete $DEL_FILE_ID -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Delete exit code 0" 0 $EC
assert_output_contains "Shows deleted" "Deleted" "$OUT"
echo ""

echo "--- Test: No args ---"
OUT=$($PHP $MOOSH file:delete -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for no args" 1 $EC
echo ""

echo "--- Test: Invalid ID ---"
OUT=$($PHP $MOOSH file:delete 99999 -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid ID" 1 $EC
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH file:delete -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Delete files" "$OUT"
assert_output_contains "Help shows --hash" "--hash" "$OUT"
echo ""


print_summary
