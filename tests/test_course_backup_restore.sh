#!/usr/bin/env bash
#
# Integration test for moosh2 course:backup, course:restore
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_course_backup_restore.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 course:backup/restore integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

BACKUP_DIR=$(mktemp -d)

# ═══════════════════════════════════════════════════════════════════
# course:backup
# ═══════════════════════════════════════════════════════════════════

echo "========== course:backup =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH course:backup -p "$MOODLE_PATH" 2 2>&1)
echo "$OUT"
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows course" "algebrafundamentals" "$OUT"
assert_output_contains "Shows output path" ".mbz" "$OUT"
echo ""

echo "--- Test: Backup with --run ---"
OUT=$($PHP $MOOSH course:backup -p "$MOODLE_PATH" --run --path "$BACKUP_DIR" 2 2>&1)
echo "$OUT"
assert_output_contains "Shows mbz path" ".mbz" "$OUT"
BACKUP_FILE=$(ls "$BACKUP_DIR"/backup_2_*.mbz 2>/dev/null | head -1)
if [ -n "$BACKUP_FILE" ] && [ -f "$BACKUP_FILE" ]; then
    FILE_SIZE=$(stat -c%s "$BACKUP_FILE")
    if [ "$FILE_SIZE" -gt 1000 ]; then
        echo "  PASS: Backup file created ($FILE_SIZE bytes)"
        ((PASS++))
    else
        echo "  FAIL: Backup file too small ($FILE_SIZE bytes)"
        ((FAIL++))
    fi
else
    echo "  FAIL: Backup file not created"
    ((FAIL++))
fi
echo ""

echo "--- Test: Template backup ---"
OUT=$($PHP $MOOSH course:backup -p "$MOODLE_PATH" --template 2 2>&1)
assert_output_contains "Template mode" "template" "$OUT"
echo ""

echo "--- Test: Nonexistent course ---"
OUT=$($PHP $MOOSH course:backup -p "$MOODLE_PATH" 99999 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for bad course" 1 "$EXIT_CODE"
assert_output_contains "Not found" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH course:backup -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Create a backup" "$OUT"
assert_output_contains "Help shows --template" "--template" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
# course:restore
# ═══════════════════════════════════════════════════════════════════

echo "========== course:restore =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH course:restore -p "$MOODLE_PATH" "$BACKUP_FILE" 2 2>&1)
echo "$OUT"
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows source" "$BACKUP_FILE" "$OUT"
assert_output_contains "Shows course name" "Algebra Fundamentals" "$OUT"
echo ""

echo "--- Test: Restore new course ---"
OUT=$($PHP $MOOSH course:restore -p "$MOODLE_PATH" --run "$BACKUP_FILE" 2 2>&1)
echo "$OUT"
assert_output_contains "Shows restored" "Restored course" "$OUT"
assert_output_contains "Shows category" "category 2" "$OUT"
echo ""

echo "--- Test: Restore into existing course ---"
OUT=$($PHP $MOOSH course:restore -p "$MOODLE_PATH" --existing "$BACKUP_FILE" 3 2>&1)
assert_output_contains "Existing dry run" "add to existing" "$OUT"
echo ""

echo "--- Test: Nonexistent file ---"
OUT=$($PHP $MOOSH course:restore -p "$MOODLE_PATH" /tmp/nonexistent.mbz 2 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for bad file" 1 "$EXIT_CODE"
assert_output_contains "File not found" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH course:restore -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Restore a course" "$OUT"
assert_output_contains "Help shows --existing" "--existing" "$OUT"
assert_output_contains "Help shows --overwrite" "--overwrite" "$OUT"
echo ""


# ── Cleanup ──────────────────────────────────────────────────────

rm -rf "$BACKUP_DIR"

print_summary
