#!/usr/bin/env bash
#
# Integration test for moosh2 course:list command
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_course_list.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 course:list integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Test data summary:
#   4 categories: Mathematics, Sciences, Humanities, Computer Science
#   12 courses (3 per category), each with 1 file resource activity
#   1 empty course (no modules)
#   1 "Recently Active Course" with log entry at 2027-12-15
#   1 "Old Activity Course" with all log entries removed
#   50 students + 10 teachers enrolled in first 10 courses
#   No questions, no forums, no quizzes
#   Site course (id=1) has no enrolments and no activities

# Test 1: Basic listing (CSV output)
echo "--- Test: Basic course listing ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list -o csv 2>&1)
echo "$output"
assert_output_contains "Header row present" 'id,category,shortname,fullname,visible' "$output"
assert_output_contains "Site course listed" 'Moodle' "$output"
assert_output_contains "Algebra course listed" 'Algebra Fundamentals' "$output"
assert_output_contains "Chemistry course listed" 'General Chemistry' "$output"
assert_output_contains "Web Dev course listed" 'Web Development' "$output"
echo ""

# Test 2: ID-only output (space-separated single line)
echo "--- Test: ID-only output ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list -i 2>&1)
echo "$output"
assert_output_contains "Contains course ID 1" "1" "$output"
assert_output_not_empty "Output is not empty" "$output"
line_count=$(printf '%s' "$output" | wc -l)
if [ "$line_count" -le 1 ]; then
    echo "  PASS: Output is a single line"
    ((PASS++))
else
    echo "  FAIL: Expected single line, got $line_count lines"
    ((FAIL++))
fi
echo ""

# Test 3: Visible filter (yes) — all 12 courses are visible
echo "--- Test: Visible courses only ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --is visible -o csv 2>&1)
echo "$output"
assert_output_contains "Visible course present" 'Algebra Fundamentals' "$output"
assert_output_contains "Another visible course present" 'Data Structures' "$output"
echo ""

# Test 4: Visible filter (no = hidden) — no hidden courses in test data
echo "--- Test: Hidden courses only ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --is-not visible -o csv 2>&1)
echo "$output"
assert_output_not_contains "Algebra should not be in hidden list" "Algebra Fundamentals" "$output"
echo ""

# Test 5: Tab output with idnumber
echo "--- Test: Tab output with idnumber ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --idnumber -o tab 2>&1)
echo "$output"
assert_output_contains "Tab header has idnumber" "idnumber" "$output"
echo ""

# Test 6: Custom fields
echo "--- Test: Custom fields ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list -f id,shortname,fullname -o csv 2>&1)
echo "$output"
assert_output_contains "Custom fields header" 'id,shortname,fullname' "$output"
assert_output_contains "Course in custom output" 'Algebra Fundamentals' "$output"
echo ""

# Test 7: Active filter with MockupClock
# "Recently Active Course" has a log entry at 2027-12-15.
# Setting MOCKUP_DATE_TIME to 2028-01-01 means "1 month ago" = 2027-12-01,
# so the 2027-12-15 entry is within range → course is active.
# All auto-generated logs from ~2026 are older than the cutoff.
echo "--- Test: Active courses with MockupClock (--is active) ---"
output=$(MOCKUP_DATE_TIME="2028-01-01 00:00:00" $PHP "$MOOSH" -p "$MOODLE_PATH" course:list --is active 2>&1)
echo "$output"
assert_output_contains "Recently Active Course is active" "Recently Active Course" "$output"
assert_output_not_contains "Old Activity Course is not active" "Old Activity Course" "$output"
echo ""

# Test 8: Inactive filter with MockupClock
# Same clock: Old Activity Course has all logs deleted, so no recent activity → inactive.
echo "--- Test: Inactive courses with MockupClock (--is-not active) ---"
output=$(MOCKUP_DATE_TIME="2028-01-01 00:00:00" $PHP "$MOOSH" -p "$MOODLE_PATH" course:list --is-not active 2>&1)
echo "$output"
assert_output_contains "Old Activity Course is inactive" "Old Activity Course" "$output"
assert_output_not_contains "Recently Active Course is not inactive" "Recently Active Course" "$output"
echo ""

# Test 9: Help output
echo "--- Test: Help output ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --help 2>&1)
assert_output_contains "Help shows description" "List Moodle courses" "$output"
assert_output_contains "Help shows is/is-not options" "--is=" "$output"
assert_output_contains "Help shows category option" "--category" "$output"
assert_output_contains "Help mentions active flag" "active" "$output"
assert_output_contains "Help shows --sql option" "--sql" "$output"
echo ""

# Test 10: --sql option filters courses
echo "--- Test: --sql option ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --sql "c.fullname = 'Algebra Fundamentals'" -o csv 2>&1)
echo "$output"
assert_output_contains "SQL filter returns Algebra" "Algebra Fundamentals" "$output"
assert_output_not_contains "SQL filter excludes Chemistry" "General Chemistry" "$output"
echo ""

# Test 11: Pipe --sql -i into --stdin
echo "--- Test: Pipe --sql -i into course:list --stdin ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --sql "c.fullname = 'Algebra Fundamentals'" -i 2>&1 \
    | $PHP "$MOOSH" -p "$MOODLE_PATH" course:list --stdin -o csv 2>&1)
echo "$output"
assert_output_contains "Piped SQL output has header" "id,category,shortname,fullname,visible" "$output"
assert_output_contains "Piped SQL output contains Algebra" "Algebra Fundamentals" "$output"
assert_output_not_contains "Piped SQL excludes Chemistry" "General Chemistry" "$output"
echo ""

# Test 12: Pipe --id-only into --stdin
echo "--- Test: Pipe course:list -i into course:list --stdin ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --is visible -i 2>&1 \
    | $PHP "$MOOSH" -p "$MOODLE_PATH" course:list --stdin -o csv 2>&1)
echo "$output"
assert_output_contains "Piped output has header" "id,category,shortname,fullname,visible" "$output"
assert_output_contains "Piped output contains Algebra" "Algebra Fundamentals" "$output"
echo ""

# Test 13: --output=oneline
echo "--- Test: --output=oneline ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list -f id -o oneline 2>&1)
echo "$output"
assert_output_not_empty "Oneline output is not empty" "$output"
line_count=$(printf '%s' "$output" | wc -l)
if [ "$line_count" -le 1 ]; then
    echo "  PASS: Oneline output is a single line"
    ((PASS++))
else
    echo "  FAIL: Expected single line, got $line_count lines"
    ((FAIL++))
fi
echo ""

# Test 14: --number users-enrolled>0 (all 12 courses have 60 enrolled users)
echo "--- Test: --number users-enrolled>0 ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --number users-enrolled\>0 -o csv 2>&1)
echo "$output"
assert_output_contains "Enrolled course Algebra present" "Algebra Fundamentals" "$output"
assert_output_contains "Enrolled course Chemistry present" "General Chemistry" "$output"
echo ""

# Test 15: --number users-enrolled=0 (site course has no enrolments)
echo "--- Test: --number users-enrolled=0 ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --number users-enrolled=0 -o csv 2>&1)
echo "$output"
assert_output_not_contains "Algebra excluded from zero-enrolment" "Algebra Fundamentals" "$output"
assert_output_not_contains "Chemistry excluded from zero-enrolment" "General Chemistry" "$output"
echo ""

# Test 16: --number combined with --id-only pipe
echo "--- Test: --number with pipe ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --number users-enrolled\>0 -i 2>&1 \
    | $PHP "$MOOSH" -p "$MOODLE_PATH" course:list --stdin -o csv 2>&1)
echo "$output"
assert_output_contains "Piped --number output contains Algebra" "Algebra Fundamentals" "$output"
echo ""

# Test 17: --number questions>0 (no courses have questions)
echo "--- Test: --number questions>0 ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --number questions\>0 -o csv 2>&1)
echo "$output"
assert_output_not_contains "No course should appear for questions>0" "Algebra Fundamentals" "$output"
echo ""

# Test 18: --number questions=0 (all courses have 0 questions)
echo "--- Test: --number questions=0 ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --number questions=0 -o csv 2>&1)
echo "$output"
assert_output_contains "Algebra present with no questions" "Algebra Fundamentals" "$output"
assert_output_contains "Chemistry present with no questions" "General Chemistry" "$output"
echo ""

# Test 19: --number combining two metrics
echo "--- Test: --number with two metrics ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --number users-enrolled\>0 --number activities\>0 -o csv 2>&1)
echo "$output"
assert_output_contains "Combined metrics returns Algebra" "Algebra Fundamentals" "$output"
assert_output_contains "Combined metrics returns Chemistry" "General Chemistry" "$output"
echo ""

# Test 20: Help output shows --number
echo "--- Test: Help shows --number ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --help 2>&1)
assert_output_contains "Help shows --number option" "--number" "$output"
assert_output_contains "Help shows users-enrolled metric" "users-enrolled" "$output"
assert_output_contains "Help shows questions metric" "questions" "$output"
assert_output_contains "Help shows activities metric" "activities" "$output"
assert_output_contains "Help shows mod-NAME metric" "mod-NAME" "$output"
echo ""

# Test 21: --number activities>0 (all 12 courses have 1 resource)
echo "--- Test: --number activities>0 ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --number activities\>0 -o csv 2>&1)
echo "$output"
assert_output_contains "Course with activities Algebra present" "Algebra Fundamentals" "$output"
assert_output_contains "Course with activities Web Dev present" "Web Development" "$output"
echo ""

# Test 22: --number activities=0 (site course has no activities)
echo "--- Test: --number activities=0 ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --number activities=0 -o csv 2>&1)
echo "$output"
assert_output_not_contains "Algebra excluded from zero-activities" "Algebra Fundamentals" "$output"
echo ""

# Test 23: --number combining three metrics
echo "--- Test: --number with three metrics ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --number users-enrolled\>0 --number questions=0 --number activities\>0 -o csv 2>&1)
echo "$output"
assert_output_contains "Three combined metrics returns Algebra" "Algebra Fundamentals" "$output"
assert_output_contains "Three combined metrics returns Chemistry" "General Chemistry" "$output"
echo ""

# Test 24: --number mod-resource>0 (all 12 courses have a file resource)
echo "--- Test: --number mod-resource>0 ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --number mod-resource\>0 -o csv 2>&1)
echo "$output"
assert_output_contains "Course with resource Algebra present" "Algebra Fundamentals" "$output"
assert_output_contains "Course with resource Web Dev present" "Web Development" "$output"
echo ""

# Test 25: --number mod-resource=0 (site course has no resource)
echo "--- Test: --number mod-resource=0 ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --number mod-resource=0 -o csv 2>&1)
echo "$output"
assert_output_not_contains "Algebra excluded from zero-resource" "Algebra Fundamentals" "$output"
echo ""

# Test 26: --number mod-quiz=0 (no course has quizzes)
echo "--- Test: --number mod-quiz=0 (no quizzes anywhere) ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --number mod-quiz=0 -o csv 2>&1)
echo "$output"
assert_output_contains "Algebra present with no quizzes" "Algebra Fundamentals" "$output"
assert_output_contains "Chemistry present with no quizzes" "General Chemistry" "$output"
echo ""

# Test 27: --number combining mod-resource with activities
echo "--- Test: --number mod-resource>0 with activities>0 ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --number mod-resource\>0 --number activities\>0 -o csv 2>&1)
echo "$output"
assert_output_contains "Combined mod-resource+activities returns Algebra" "Algebra Fundamentals" "$output"
assert_output_contains "Combined mod-resource+activities returns Chemistry" "General Chemistry" "$output"
echo ""

# Test 28: Empty courses (--is empty, --is-not empty)
echo "--- Test: Inactive courses (--is empty, --is-not empty) ---"
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --is empty 2>&1)
echo "$output"
assert_output_contains "Empty course is shown" "Empty course" "$output"
echo ""
output=$($PHP "$MOOSH" -p "$MOODLE_PATH" course:list --is-not empty 2>&1)
echo "$output"
assert_output_not_contains "Empty course is not shown" "Empty course" "$output"
echo ""

print_summary
