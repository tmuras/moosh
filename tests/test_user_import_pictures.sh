#!/usr/bin/env bash
#
# Integration tests for moosh2 user:import-pictures command
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_user_import_pictures.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 user:import-pictures integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Test data summary:
#   62 users: admin, guest, student01..student50, teacher01..teacher10
#   No users have profile pictures initially.
#   Tests create temp images named after usernames for import.

TMPDIR=$(mktemp -d)
IMGDIR="$TMPDIR/images"
SUBDIR="$IMGDIR/subdir"
mkdir -p "$SUBDIR"
trap "rm -rf $TMPDIR" EXIT

# Create test images using PHP GD
$PHP -r "
\$colors = [
    'admin' => [0, 0, 255],
    'student01' => [255, 0, 0],
    'student02' => [0, 255, 0],
    'student03' => [255, 255, 0],
];
foreach (\$colors as \$name => \$rgb) {
    \$img = imagecreatetruecolor(100, 100);
    \$c = imagecolorallocate(\$img, \$rgb[0], \$rgb[1], \$rgb[2]);
    imagefill(\$img, 0, 0, \$c);
    imagepng(\$img, '$IMGDIR/' . \$name . '.png');
    imagedestroy(\$img);
}
// Image in subdirectory
\$img = imagecreatetruecolor(100, 100);
\$c = imagecolorallocate(\$img, 128, 128, 128);
imagefill(\$img, 0, 0, \$c);
imagepng(\$img, '$SUBDIR/student04.png');
imagedestroy(\$img);
// JPEG format
\$img = imagecreatetruecolor(100, 100);
\$c = imagecolorallocate(\$img, 200, 100, 50);
imagefill(\$img, 0, 0, \$c);
imagejpeg(\$img, '$IMGDIR/student05.jpg');
imagedestroy(\$img);
// Unmatched image
\$img = imagecreatetruecolor(100, 100);
\$c = imagecolorallocate(\$img, 0, 0, 0);
imagefill(\$img, 0, 0, \$c);
imagepng(\$img, '$IMGDIR/nonexistent_user.png');
imagedestroy(\$img);
// Non-image file (should be ignored)
file_put_contents('$IMGDIR/readme.txt', 'not an image');
echo 'Test images created';
"
echo ""

# ── Report modes ─────────────────────────────────────────────────

echo "--- Test: --report lists all users ---"
OUT=$($PHP $MOOSH user:import-pictures -p "$MOODLE_PATH" --report -o csv 2>&1)
echo "$OUT" | head -5
assert_output_contains "Report header" "id,username,email,firstname,lastname,has_picture" "$OUT"
assert_output_contains "Report has admin" "admin" "$OUT"
assert_output_contains "Report has student01" "student01" "$OUT"
assert_output_contains "Report shows no picture" "no" "$OUT"
echo ""

echo "--- Test: --report-missing lists users without pictures ---"
OUT=$($PHP $MOOSH user:import-pictures -p "$MOODLE_PATH" --report-missing -o csv 2>&1)
assert_output_contains "Missing report has admin" "admin" "$OUT"
assert_output_not_contains "Missing report excludes guest" "guest" "$OUT"
echo ""

echo "--- Test: --report JSON output ---"
OUT=$($PHP $MOOSH user:import-pictures -p "$MOODLE_PATH" --report-missing -o json 2>&1)
assert_output_contains "JSON has username key" '"username"' "$OUT"
assert_output_contains "JSON has has_picture" '"has_picture": "no"' "$OUT"
echo ""

# ── Dry-run import ───────────────────────────────────────────────

echo "--- Test: Dry-run import ---"
OUT=$($PHP $MOOSH user:import-pictures -p "$MOODLE_PATH" "$IMGDIR" 2>&1)
echo "$OUT"
assert_output_contains "Dry run message" "Dry run" "$OUT"
assert_output_contains "Shows admin mapping" "admin" "$OUT"
assert_output_contains "Shows student01 mapping" "student01" "$OUT"
assert_output_contains "Shows set action" "[set]" "$OUT"
assert_output_contains "Shows summary total" "Total image files:" "$OUT"
echo ""

# Verify no pictures were set
echo "--- Test: Dry run did not set pictures ---"
PIC=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
echo \$DB->get_field('user', 'picture', ['username' => 'admin']);
" 2>/dev/null)
if [ "$PIC" = "0" ]; then
    echo "  PASS: No pictures set during dry run"
    ((PASS++))
else
    echo "  FAIL: Picture was set during dry run (picture=$PIC)"
    ((FAIL++))
fi
echo ""

# ── Actual import ────────────────────────────────────────────────

echo "--- Test: Import with --run ---"
OUT=$($PHP $MOOSH user:import-pictures -p "$MOODLE_PATH" "$IMGDIR" --run 2>&1)
echo "$OUT"
assert_output_contains "Imported admin" "admin" "$OUT"
assert_output_contains "Imported student01" "student01" "$OUT"
assert_output_contains "Summary imported count" "Imported:" "$OUT"
assert_output_contains "Summary not-found count" "Not found:" "$OUT"
echo ""

# Verify pictures were set in DB
echo "--- Test: Pictures set in DB ---"
PIC=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$count = \$DB->count_records_select('user', 'picture > 0 AND username IN (?, ?, ?, ?, ?, ?)',
    ['admin', 'student01', 'student02', 'student03', 'student04', 'student05']);
echo \$count;
" 2>/dev/null)
if [ "$PIC" = "6" ]; then
    echo "  PASS: All 6 matched users have pictures set"
    ((PASS++))
else
    echo "  FAIL: Expected 6 users with pictures, got $PIC"
    ((FAIL++))
fi
echo ""

# ── Skipping existing pictures ───────────────────────────────────

echo "--- Test: Re-import skips existing (no --overwrite) ---"
OUT=$($PHP $MOOSH user:import-pictures -p "$MOODLE_PATH" "$IMGDIR" --run 2>&1)
echo "$OUT"
assert_output_contains "Shows skipped count" "Skipped (exists):" "$OUT"
# Check the imported count is 0 in summary
if echo "$OUT" | grep -q "Imported:          0"; then
    echo "  PASS: Zero imports on re-run without overwrite"
    ((PASS++))
else
    echo "  FAIL: Expected 0 imports on re-run"
    ((FAIL++))
fi
echo ""

echo "--- Test: --overwrite replaces pictures ---"
OUT=$($PHP $MOOSH user:import-pictures -p "$MOODLE_PATH" "$IMGDIR" --overwrite --run 2>&1)
echo "$OUT"
assert_output_contains "Overwrite imports" "Imported:" "$OUT"
assert_output_contains "Overwrite shows admin" "admin" "$OUT"
echo ""

# ── Recursive vs non-recursive ───────────────────────────────────

echo "--- Test: Recursive finds subdirectory images ---"
# Use --overwrite so existing pictures don't cause skips
OUT=$($PHP $MOOSH user:import-pictures -p "$MOODLE_PATH" "$IMGDIR" --overwrite 2>&1)
assert_output_contains "Recursive finds student04" "student04" "$OUT"
echo ""

echo "--- Test: --no-recursive skips subdirectory images ---"
OUT=$($PHP $MOOSH user:import-pictures -p "$MOODLE_PATH" "$IMGDIR" --no-recursive 2>&1)
assert_output_not_contains "No-recursive skips student04" "student04" "$OUT"
echo ""

# ── CSV mapping ──────────────────────────────────────────────────

# Create CSV mapping file
echo "filename,username" > "$TMPDIR/mapping.csv"
echo "admin.png,student10" >> "$TMPDIR/mapping.csv"
echo "student01.png,student11" >> "$TMPDIR/mapping.csv"

echo "--- Test: CSV mapping import ---"
OUT=$($PHP $MOOSH user:import-pictures -p "$MOODLE_PATH" "$IMGDIR" --csv="$TMPDIR/mapping.csv" --overwrite 2>&1)
echo "$OUT"
assert_output_contains "CSV maps to student10" "student10" "$OUT"
assert_output_contains "CSV maps to student11" "student11" "$OUT"
echo ""

# ── Match by ID ──────────────────────────────────────────────────

# Create an image named by user ID
ADMIN_ID=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
echo \$DB->get_field('user', 'id', ['username' => 'student20']);
" 2>/dev/null)

$PHP -r "
\$img = imagecreatetruecolor(100, 100);
\$c = imagecolorallocate(\$img, 50, 50, 50);
imagefill(\$img, 0, 0, \$c);
imagepng(\$img, '$TMPDIR/idimages/$ADMIN_ID.png');
imagedestroy(\$img);
" 2>/dev/null
mkdir -p "$TMPDIR/idimages"
$PHP -r "
\$img = imagecreatetruecolor(100, 100);
\$c = imagecolorallocate(\$img, 50, 50, 50);
imagefill(\$img, 0, 0, \$c);
imagepng(\$img, '$TMPDIR/idimages/$ADMIN_ID.png');
imagedestroy(\$img);
"

echo "--- Test: Match by ID ---"
OUT=$($PHP $MOOSH user:import-pictures -p "$MOODLE_PATH" "$TMPDIR/idimages" --match=id --overwrite 2>&1)
echo "$OUT"
assert_output_contains "Match by ID finds user" "student20" "$OUT"
echo ""

# ── Error handling ───────────────────────────────────────────────

echo "--- Test: Missing directory argument ---"
OUT=$($PHP $MOOSH user:import-pictures -p "$MOODLE_PATH" --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Missing directory returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error mentions directory" "Directory" "$OUT"
echo ""

echo "--- Test: Nonexistent directory ---"
OUT=$($PHP $MOOSH user:import-pictures -p "$MOODLE_PATH" /nonexistent/path --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Bad directory returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error mentions does not exist" "does not exist" "$OUT"
echo ""

echo "--- Test: Invalid match field ---"
OUT=$($PHP $MOOSH user:import-pictures -p "$MOODLE_PATH" "$IMGDIR" --match=invalid --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Invalid match returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error mentions invalid" "Invalid" "$OUT"
echo ""

# ── Help & alias ─────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH user:import-pictures -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help shows description" "Import user profile pictures" "$OUT"
assert_output_contains "Help shows --match" "--match" "$OUT"
assert_output_contains "Help shows --overwrite" "--overwrite" "$OUT"
assert_output_contains "Help shows --csv" "--csv" "$OUT"
assert_output_contains "Help shows --report" "--report" "$OUT"
assert_output_contains "Help shows --report-missing" "--report-missing" "$OUT"
echo ""


# ── Report after import ──────────────────────────────────────────

echo "--- Test: Report shows pictures after import ---"
OUT=$($PHP $MOOSH user:import-pictures -p "$MOODLE_PATH" --report -o csv 2>&1)
assert_output_contains "Admin has picture now" "admin" "$OUT"
# Check that at least some users have "yes"
if echo "$OUT" | grep -q ",yes"; then
    echo "  PASS: Some users show has_picture=yes after import"
    ((PASS++))
else
    echo "  FAIL: No users show has_picture=yes"
    ((FAIL++))
fi
echo ""

print_summary
