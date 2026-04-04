#!/usr/bin/env bash
#
# Integration test for moosh2 backup:info command
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_backup_info.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 backup:info integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Step 2: Create a test backup of course 2 (Algebra Fundamentals)
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
\$file->copy_content_to('/tmp/test_backup_algebra.mbz');
\$bc->destroy();
echo 'Backup created.' . PHP_EOL;
" 2>/dev/null
echo ""

# Zip-format backup from Moodle fixtures
ZIP_BACKUP="$MOODLE_PATH/admin/tool/uploadcourse/tests/fixtures/backup.mbz"

# ── Basic table output (gzip) ────────────────────────────────────

echo "--- Test: Basic table output (gzip backup) ---"
OUT=$($PHP $MOOSH backup:info -p "$MOODLE_PATH" /tmp/test_backup_algebra.mbz)
echo "$OUT"
assert_output_contains "Shows File" "test_backup_algebra.mbz" "$OUT"
assert_output_contains "Shows Archive type gzip" "gzip" "$OUT"
assert_output_contains "Shows Moodle version" "Moodle version" "$OUT"
MOODLE_BASENAME="$(basename "${MOODLE_DIR:-/var/www/html/moodle52}")"
MOODLE_VER="${MOODLE_BASENAME#moodle}"
MOODLE_VER_DOT="${MOODLE_VER:0:1}.${MOODLE_VER:1}"
assert_output_contains "Shows Moodle release" "$MOODLE_VER_DOT" "$OUT"
assert_output_contains "Shows Backup date" "Backup date" "$OUT"
assert_output_contains "Shows Course fullname" "Algebra Fundamentals" "$OUT"
assert_output_contains "Shows Course shortname" "algebrafundamentals_2" "$OUT"
assert_output_contains "Shows Course format" "topics" "$OUT"
assert_output_contains "Shows Activities" "Activities" "$OUT"
assert_output_contains "Shows Sections" "Sections" "$OUT"
assert_output_contains "Shows Users" "Users" "$OUT"
assert_output_contains "Shows Roles" "Roles defined" "$OUT"
assert_output_contains "Shows Enrolments" "Total enrolments" "$OUT"
echo ""

# ── JSON output ───────────────────────────────────────────────────

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH backup:info -p "$MOODLE_PATH" /tmp/test_backup_algebra.mbz -o json)
assert_output_contains "JSON has File key" '"File"' "$OUT"
assert_output_contains "JSON has Moodle release" '"Moodle release"' "$OUT"
assert_output_contains "JSON has Course fullname" '"Algebra Fundamentals"' "$OUT"
assert_output_contains "JSON has Course shortname" '"algebrafundamentals_2"' "$OUT"
echo ""

# ── CSV output ────────────────────────────────────────────────────

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH backup:info -p "$MOODLE_PATH" /tmp/test_backup_algebra.mbz -o csv)
echo "$OUT" | head -2
assert_output_contains "CSV has File header" "File" "$OUT"
assert_output_contains "CSV has backup name" "test_backup_algebra.mbz" "$OUT"
echo ""

# ── Course details ────────────────────────────────────────────────

echo "--- Test: Course details ---"
OUT=$($PHP $MOOSH backup:info -p "$MOODLE_PATH" /tmp/test_backup_algebra.mbz -o json)
assert_output_contains "Original course ID is 2" '"Original course ID": "2"' "$OUT"
assert_output_contains "Has Course start date" '"Course start date"' "$OUT"
assert_output_contains "Course end date none" '"Course end date": "none"' "$OUT"
echo ""

# ── Activities and sections ───────────────────────────────────────

echo "--- Test: Activities and sections ---"
OUT=$($PHP $MOOSH backup:info -p "$MOODLE_PATH" /tmp/test_backup_algebra.mbz -o json)
assert_output_contains "1 activity" '"Activities": 1' "$OUT"
assert_output_contains "1 resource activity" '"Activities: resource": 1' "$OUT"
assert_output_contains "4 sections" '"Sections": 4' "$OUT"
echo ""

# ── Users and enrolments ──────────────────────────────────────────

echo "--- Test: Users and enrolments ---"
OUT=$($PHP $MOOSH backup:info -p "$MOODLE_PATH" /tmp/test_backup_algebra.mbz -o json)
assert_output_contains "60 users" '"Users": 60' "$OUT"
assert_output_contains "60 total enrolments" '"Total enrolments": 60' "$OUT"
assert_output_contains "60 manual enrolments" '"Enrolments: manual": 60' "$OUT"
assert_output_contains "Roles include student" "student" "$OUT"
assert_output_contains "Roles include editingteacher" "editingteacher" "$OUT"
echo ""

# ── Backup settings ───────────────────────────────────────────────

echo "--- Test: Backup settings ---"
OUT=$($PHP $MOOSH backup:info -p "$MOODLE_PATH" /tmp/test_backup_algebra.mbz -o json)
assert_output_contains "Includes users 1" '"Includes users": "1"' "$OUT"
assert_output_contains "Includes activities 1" '"Includes activities": "1"' "$OUT"
assert_output_contains "Includes blocks 1" '"Includes blocks": "1"' "$OUT"
assert_output_contains "Includes groups 1" '"Includes groups": "1"' "$OUT"
echo ""

# ── Files ─────────────────────────────────────────────────────────

echo "--- Test: Files ---"
OUT=$($PHP $MOOSH backup:info -p "$MOODLE_PATH" /tmp/test_backup_algebra.mbz -o json)
assert_output_contains "1 file in backup" '"Files in backup": 1' "$OUT"
assert_output_contains "33 bytes total" '"Files total size (bytes)": 33' "$OUT"
echo ""

# ── Zip-format backup ────────────────────────────────────────────

echo "--- Test: Zip-format backup ---"
OUT=$($PHP $MOOSH backup:info -p "$MOODLE_PATH" "$ZIP_BACKUP")
echo "$OUT"
assert_output_contains "Zip archive type" "zip" "$OUT"
assert_output_contains "Course Import name" "Course Import" "$OUT"
assert_output_contains "Glossary activity" "glossary" "$OUT"
assert_output_contains "Moodle 2.6 release" "2.6" "$OUT"
echo ""

# ── File size ─────────────────────────────────────────────────────

echo "--- Test: File size ---"
OUT=$($PHP $MOOSH backup:info -p "$MOODLE_PATH" /tmp/test_backup_algebra.mbz -o json)
assert_output_contains "File size present" '"File size (bytes)"' "$OUT"
assert_output_not_contains "File size not zero" '"File size (bytes)": 0' "$OUT"
echo ""

# ── Invalid file ──────────────────────────────────────────────────

echo "--- Test: Invalid file ---"
OUT=$($PHP $MOOSH backup:info -p "$MOODLE_PATH" /tmp/nonexistent.mbz 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code is 1 for missing file" 1 "$EXIT_CODE"
assert_output_contains "Error message" "not found" "$OUT"
echo ""

# ── Missing file argument ─────────────────────────────────────────

echo "--- Test: Missing file argument ---"
OUT=$($PHP $MOOSH backup:info -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Error mentions missing argument" "file" "$OUT"
echo ""

# ── Help output ───────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH backup:info -p "$MOODLE_PATH" --help)
assert_output_contains "Help shows description" "Show detailed information about a Moodle backup file" "$OUT"
assert_output_contains "Help shows file argument" "file" "$OUT"
echo ""

# ── backup-info alias ────────────────────────────────────────────


# ── Cleanup ───────────────────────────────────────────────────────
rm -f /tmp/test_backup_algebra.mbz

print_summary
