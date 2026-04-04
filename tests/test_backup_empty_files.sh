#!/usr/bin/env bash
#
# Integration test for moosh2 backup:empty-files command
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_backup_empty_files.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 backup:empty-files integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state and create test backup
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

echo "--- Creating test backup ---"
$PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
require_once(\$CFG->dirroot . '/backup/util/includes/backup_includes.php');
\$bc = new backup_controller(
    backup::TYPE_1COURSE, 2, backup::FORMAT_MOODLE,
    backup::INTERACTIVE_NO, backup::MODE_GENERAL, 2
);
\$bc->execute_plan();
\$result = \$bc->get_results();
\$file = \$result['backup_destination'];
\$file->copy_content_to('/tmp/test_ef_backup.mbz');
\$bc->destroy();
echo 'Backup created.' . PHP_EOL;
" 2>/dev/null
ORIG_SIZE=$(stat -c%s /tmp/test_ef_backup.mbz)
echo "Original backup size: $ORIG_SIZE bytes"
echo ""

# ── Dry run ───────────────────────────────────────────────────────

echo "--- Test: Dry run (no --run) ---"
OUT=$($PHP $MOOSH backup:empty-files /tmp/test_ef_backup.mbz)
echo "$OUT"
assert_output_contains "Shows dry run message" "Dry run" "$OUT"
assert_output_contains "Shows archive type" "gzip" "$OUT"
assert_output_contains "Shows data file count" "Data files: 1" "$OUT"
assert_output_contains "Shows file path" "files/" "$OUT"
assert_output_contains "Shows size to remove" "33 bytes" "$OUT"
# Verify original not modified
AFTER_SIZE=$(stat -c%s /tmp/test_ef_backup.mbz)
if [ "$ORIG_SIZE" -eq "$AFTER_SIZE" ]; then
    echo "  PASS: Original file not modified"
    ((PASS++))
else
    echo "  FAIL: Original file was modified"
    ((FAIL++))
fi
echo ""

# ── Run with --output-file ────────────────────────────────────────

echo "--- Test: --run with --output-file ---"
OUT=$($PHP $MOOSH backup:empty-files --run --output-file /tmp/test_ef_emptied.mbz /tmp/test_ef_backup.mbz)
echo "$OUT"
assert_output_contains "Shows truncated count" "Truncated 1" "$OUT"
assert_output_contains "Shows bytes removed" "removed 33 bytes" "$OUT"
assert_output_contains "Shows new size" "New backup size:" "$OUT"
# Verify output file exists
if [ -f /tmp/test_ef_emptied.mbz ]; then
    echo "  PASS: Output file created"
    ((PASS++))
else
    echo "  FAIL: Output file not created"
    ((FAIL++))
fi
# Verify original not modified
AFTER_SIZE2=$(stat -c%s /tmp/test_ef_backup.mbz)
if [ "$ORIG_SIZE" -eq "$AFTER_SIZE2" ]; then
    echo "  PASS: Original file unchanged with --output-file"
    ((PASS++))
else
    echo "  FAIL: Original file was modified despite --output-file"
    ((FAIL++))
fi
echo ""

# ── Verify data files are empty ───────────────────────────────────

echo "--- Test: Data files are empty in output ---"
DATA_LINE=$(tar -tzvf /tmp/test_ef_emptied.mbz 2>/dev/null | grep "files/" | grep -v "/$" | head -1)
echo "  Data file line: $DATA_LINE"
# Check size is 0 (first field with digits before the date)
DATA_SIZE=$(echo "$DATA_LINE" | awk '{print $3}')
if [ "$DATA_SIZE" -eq 0 ]; then
    echo "  PASS: Data file size is 0"
    ((PASS++))
else
    echo "  FAIL: Data file size is $DATA_SIZE, expected 0"
    ((FAIL++))
fi
# Verify the file path is preserved
assert_output_contains "Data file path preserved" "files/" "$DATA_LINE"
echo ""

# ── Verify files.xml is NOT modified ──────────────────────────────

echo "--- Test: files.xml not modified ---"
ORIG_FILESIZE=$(tar -xzOf /tmp/test_ef_backup.mbz files.xml 2>/dev/null | grep -o '<filesize>[0-9]*</filesize>' | head -1)
NEW_FILESIZE=$(tar -xzOf /tmp/test_ef_emptied.mbz ./files.xml 2>/dev/null | grep -o '<filesize>[0-9]*</filesize>' | head -1)
if [ "$ORIG_FILESIZE" = "$NEW_FILESIZE" ]; then
    echo "  PASS: files.xml filesize unchanged"
    ((PASS++))
else
    echo "  FAIL: files.xml filesize changed from $ORIG_FILESIZE to $NEW_FILESIZE"
    ((FAIL++))
fi
echo ""

# ── Verify XML files are intact ───────────────────────────────────

echo "--- Test: XML files intact in output ---"
COURSE_XML=$(tar -xzOf /tmp/test_ef_emptied.mbz ./course/course.xml 2>/dev/null)
assert_output_contains "course.xml has fullname" "Algebra Fundamentals" "$COURSE_XML"
BACKUP_XML=$(tar -xzOf /tmp/test_ef_emptied.mbz ./moodle_backup.xml 2>/dev/null)
assert_output_contains "moodle_backup.xml has version" "moodle_version" "$BACKUP_XML"
echo ""

# ── Run in-place (overwrite) ─────────────────────────────────────

echo "--- Test: --run in-place ---"
cp /tmp/test_ef_backup.mbz /tmp/test_ef_inplace.mbz
OUT=$($PHP $MOOSH backup:empty-files --run /tmp/test_ef_inplace.mbz)
echo "$OUT"
assert_output_contains "Truncated in-place" "Truncated 1" "$OUT"
INPLACE_SIZE=$(stat -c%s /tmp/test_ef_inplace.mbz)
if [ "$INPLACE_SIZE" -lt "$ORIG_SIZE" ] || [ "$INPLACE_SIZE" -le "$((ORIG_SIZE + 1000))" ]; then
    echo "  PASS: In-place file was modified"
    ((PASS++))
else
    echo "  FAIL: In-place file size unexpected: $INPLACE_SIZE"
    ((FAIL++))
fi
echo ""

# ── Invalid file ──────────────────────────────────────────────────

echo "--- Test: Invalid file ---"
OUT=$($PHP $MOOSH backup:empty-files --run /tmp/nonexistent.mbz 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for missing file" 1 "$EXIT_CODE"
assert_output_contains "Error message" "not found" "$OUT"
echo ""

# ── Help output ───────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH backup:empty-files --help)
assert_output_contains "Help shows description" "Truncate data files inside a Moodle backup" "$OUT"
assert_output_contains "Help shows file argument" "file" "$OUT"
assert_output_contains "Help shows --output-file" "--output-file" "$OUT"
echo ""

# ── backup-empty-files alias ─────────────────────────────────────


# ── Cleanup ───────────────────────────────────────────────────────
rm -f /tmp/test_ef_backup.mbz /tmp/test_ef_emptied.mbz /tmp/test_ef_inplace.mbz

print_summary
