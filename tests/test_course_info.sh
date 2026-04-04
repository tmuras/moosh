#!/usr/bin/env bash
#
# Integration test for moosh2 course:info command
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_course_info.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 course:info integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Discover course IDs dynamically using course:list
# Pick a course with enrollments (one of the first 10 regular courses)
ENROLLED_COURSE_ID=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --sql "c.fullname = 'Algebra Fundamentals'" -i 2>&1)
# Pick the empty course (no modules)
EMPTY_COURSE_ID=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --sql "c.fullname = 'Empty course'" -i 2>&1)
# Site course
SITE_COURSE_ID=1

echo "Test course IDs: enrolled=$ENROLLED_COURSE_ID, empty=$EMPTY_COURSE_ID, site=$SITE_COURSE_ID"
echo ""

# Test data summary:
#   Using "Algebra Fundamentals" (enrolled course): has 50 students + 10 teachers,
#     1 resource activity, 4 sections (3 + general), no questions, no groups, no badges
#   Using "Empty course": no modules, no enrollments
#   Using site course (id=1): minimal data
#   No groups, no badges, no grade entries in test data

# Test 1: Basic table output for enrolled course
echo "--- Test: Basic table output ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:info "$ENROLLED_COURSE_ID" 2>&1)
echo "$output"
assert_output_contains "Shows Course ID" "Course ID" "$output"
assert_output_contains "Shows number of contexts" "Number of contexts" "$output"
assert_output_contains "Shows enrolled users" "Enrolled users" "$output"
assert_output_contains "Shows number of sections" "Number of sections" "$output"
assert_output_contains "Shows number of files" "Number of files" "$output"
assert_output_contains "Shows number of log entries" "Number of log entries" "$output"
echo ""

# Test 2: CSV output format
echo "--- Test: CSV output ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:info -o csv "$ENROLLED_COURSE_ID" 2>&1)
echo "$output"
assert_output_contains "CSV has Course ID header" "Course ID" "$output"
assert_output_contains "CSV has Enrolled users header" "Enrolled users" "$output"
assert_output_contains "CSV has Number of sections header" "Number of sections" "$output"
echo ""

# Test 3: JSON output format
echo "--- Test: JSON output ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:info -o json "$ENROLLED_COURSE_ID" 2>&1)
echo "$output"
assert_output_contains "JSON has Course ID key" '"Course ID"' "$output"
assert_output_contains "JSON has Enrolled users key" '"Enrolled users"' "$output"
assert_output_contains "JSON has Number of sections key" '"Number of sections"' "$output"
echo ""

# Test 4: Enrolled users count (50 students + 10 teachers = 60)
echo "--- Test: Enrolled users count ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:info -o json "$ENROLLED_COURSE_ID" 2>&1)
echo "$output"
assert_output_contains "60 enrolled users" '"Enrolled users": 60' "$output"
assert_output_contains "Users in role student" "Users in role student" "$output"
assert_output_contains "Users in role editingteacher" "Users in role editingteacher" "$output"
echo ""

# Test 5: Number of sections (numsections=3, plus general = 4)
echo "--- Test: Sections count ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:info -o json "$ENROLLED_COURSE_ID" 2>&1)
echo "$output"
assert_output_contains "4 sections" '"Number of sections": 4' "$output"
echo ""

# Test 6: Empty course has 0 enrolled users and 0 contexts for modules
echo "--- Test: Empty course ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:info -o json "$EMPTY_COURSE_ID" 2>&1)
echo "$output"
assert_output_contains "Empty course has 0 enrolled users" '"Enrolled users": 0' "$output"
assert_output_contains "Empty course has 0 questions" '"Question bank questions": 0' "$output"
assert_output_contains "Empty course has 0 badges" '"Number of badges": 0' "$output"
assert_output_contains "Empty course has 0 grades" '"Number of grades": 0' "$output"
echo ""

# Test 7: Site course (id=1)
echo "--- Test: Site course ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:info -o json "$SITE_COURSE_ID" 2>&1)
echo "$output"
assert_output_contains "Site course ID is 1" '"Course ID": "1"' "$output"
assert_output_contains "Site course has 0 enrolled users" '"Enrolled users": 0' "$output"
echo ""

# Test 8: Groups data (no groups in test data)
echo "--- Test: Groups data ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:info -o json "$ENROLLED_COURSE_ID" 2>&1)
echo "$output"
assert_output_contains "0 groups" '"Number of groups": 0' "$output"
assert_output_contains "Group members min is 0" '"Group members min": 0' "$output"
assert_output_contains "Group members max is 0" '"Group members max": 0' "$output"
assert_output_contains "Group members avg is 0" '"Group members avg": 0' "$output"
echo ""

# Test 9: No cache build time without --run
echo "--- Test: No cache build time without --run ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:info -o json "$ENROLLED_COURSE_ID" 2>&1)
echo "$output"
assert_output_not_contains "No cache build time without --run" "Cache build time" "$output"
echo ""

# Test 10: Cache build time with --run
echo "--- Test: Cache build time with --run ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:info --run -o json "$ENROLLED_COURSE_ID" 2>&1)
echo "$output"
assert_output_contains "Cache build time present with --run" "Cache build time" "$output"
echo ""

# Test 11: Help output
echo "--- Test: Help output ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:info --help 2>&1)
echo "$output"
assert_output_contains "Help shows description" "Show detailed information about a course" "$output"
assert_output_contains "Help shows courseid argument" "courseid" "$output"
echo ""

# Test 12: Question bank questions (0 in test data)
echo "--- Test: Question bank questions ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:info -o json "$ENROLLED_COURSE_ID" 2>&1)
echo "$output"
assert_output_contains "0 question bank questions" '"Question bank questions": 0' "$output"
echo ""

# Test 13: Modinfo size is positive
echo "--- Test: Modinfo size is positive ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:info "$ENROLLED_COURSE_ID" 2>&1)
echo "$output"
assert_output_contains "Modinfo size reported" "Modinfo size (bytes)" "$output"
assert_output_not_contains "Modinfo size is not zero" "Modinfo size (bytes) | 0" "$output"
echo ""

# Test 14: Contexts include module-level context for resource
echo "--- Test: Contexts for mod resource ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:info "$ENROLLED_COURSE_ID" 2>&1)
echo "$output"
assert_output_contains "Has module contexts" "Contexts for mod resource" "$output"
echo ""

# Test 15: Missing courseid argument produces error
echo "--- Test: Missing courseid argument ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:info 2>&1)
rc=$?
assert_output_contains "Error mentions missing argument" "courseid" "$output"
echo ""

print_summary
