#!/usr/bin/env bash
#
# Integration test for moosh2 course:find-big-images
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_course_find_big_images.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 course:find-big-images integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

echo "========== course:find-big-images =========="
echo ""

echo "--- Test: Default threshold detects 2MB image ---"
OUT=$($PHP $MOOSH course:find-big-images -p "$MOODLE_PATH" -o csv)
echo "$OUT"
assert_output_contains "Header present" "courseid,shortname,fullname,filename,size_kb" "$OUT"
assert_output_contains "Detects big image file" "big_image_chickens_frog_2MB.png" "$OUT"
assert_output_contains "Big Media course detected" "Big Media" "$OUT"
echo ""

echo "--- Test: Small threshold to find any images ---"
OUT=$($PHP $MOOSH course:find-big-images -p "$MOODLE_PATH" --size 0 -o csv)
assert_output_contains "Header still present" "courseid" "$OUT"
assert_output_contains "Big image in small threshold" "big_image_chickens_frog_2MB.png" "$OUT"
echo ""

echo "--- Test: Large threshold excludes 2MB image ---"
OUT=$($PHP $MOOSH course:find-big-images -p "$MOODLE_PATH" --size 5000 -o csv)
DATA_LINES=$(echo "$OUT" | tail -n +2 | grep -c . || true)
if [ "$DATA_LINES" -eq 0 ]; then
    echo "  PASS: No images above 5MB threshold"
    ((PASS++))
else
    echo "  FAIL: Expected no images above 5MB threshold, got $DATA_LINES"
    ((FAIL++))
fi
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH course:find-big-images -p "$MOODLE_PATH" -o json)
assert_output_contains "JSON array" "[" "$OUT"
assert_output_contains "JSON has filename" '"filename"' "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH course:find-big-images -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Find courses with oversized overview images" "$OUT"
assert_output_contains "Help shows --size" "--size" "$OUT"
echo ""


print_summary
