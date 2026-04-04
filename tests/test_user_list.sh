#!/usr/bin/env bash
#
# Integration test for moosh2 user:list command
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_user_list.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 user:list integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Test data summary:
#   62 users total: guest (id=1), admin (id=2), student01-student50, teacher01-teacher10
#   50 students + 10 teachers enrolled in first 10 courses (ids 2-11)
#   All users are confirmed, not suspended, not deleted
#   Course 14 (Empty course) has no enrolments

# ── Basic listing ─────────────────────────────────────────────────

echo "--- Test: Basic user listing ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" -o csv)
echo "$OUT" | head -5
assert_output_contains "Header row present" "id,username,email" "$OUT"
assert_output_contains "Admin user listed" "admin" "$OUT"
assert_output_contains "Student user listed" "student01" "$OUT"
assert_output_contains "Teacher user listed" "teacher01" "$OUT"
echo ""

# ── ID-only output ────────────────────────────────────────────────

echo "--- Test: ID-only output ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" -i)
echo "$OUT"
assert_output_contains "Contains user ID 1" "1" "$OUT"
assert_output_contains "Contains user ID 2" "2" "$OUT"
assert_output_not_empty "Output is not empty" "$OUT"
LINE_COUNT=$(echo "$OUT" | wc -l)
assert_output_contains "Output is a single line" "1" "$LINE_COUNT"
echo ""

# ── Boolean filters ───────────────────────────────────────────────

echo "--- Test: --is-not deleted ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --is-not deleted -o csv)
assert_output_contains "Admin present when filtering non-deleted" "admin" "$OUT"
assert_output_contains "Student present when filtering non-deleted" "student01" "$OUT"
echo ""

echo "--- Test: --is confirmed ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --is confirmed -o csv)
assert_output_contains "Admin present when filtering confirmed" "admin" "$OUT"
assert_output_contains "Student present when filtering confirmed" "student01" "$OUT"
echo ""

echo "--- Test: --is-not suspended ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --is-not suspended -o csv)
assert_output_contains "Admin present when filtering non-suspended" "admin" "$OUT"
echo ""

# ── Custom fields ─────────────────────────────────────────────────

echo "--- Test: Custom fields ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" -f id,username,firstname,lastname -o csv)
echo "$OUT" | head -5
assert_output_contains "Custom fields header" "id,username,firstname,lastname" "$OUT"
assert_output_contains "Admin firstname" "Admin" "$OUT"
assert_output_contains "Student firstname" "Student" "$OUT"
echo ""

# ── Sorting ───────────────────────────────────────────────────────

echo "--- Test: Sort by username ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --sort username -o csv)
echo "$OUT" | head -5
# admin sorts before guest and students
FIRST_DATA=$(echo "$OUT" | sed -n '2p')
assert_output_contains "First user is admin" "admin" "$FIRST_DATA"
echo ""

echo "--- Test: Sort by username descending ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --sort username -d -o csv)
echo "$OUT" | head -5
FIRST_DATA=$(echo "$OUT" | sed -n '2p')
assert_output_contains "First user is teacher10" "teacher10" "$FIRST_DATA"
echo ""

# ── Limit ─────────────────────────────────────────────────────────

echo "--- Test: --limit 3 ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --limit 3 -o csv)
echo "$OUT"
LINE_COUNT=$(echo "$OUT" | wc -l)
assert_output_contains "Limit returns 4 lines (header + 3)" "4" "$LINE_COUNT"
echo ""

# ── Course filter ─────────────────────────────────────────────────

echo "--- Test: --course (enrolled users) ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --course 2 -o csv)
assert_output_contains "Student enrolled in course" "student01" "$OUT"
assert_output_contains "Teacher enrolled in course" "teacher01" "$OUT"
assert_output_not_contains "Admin not enrolled in course" "admin" "$OUT"
ENROLLED_COUNT=$(echo "$OUT" | tail -n +2 | wc -l)
assert_output_contains "60 users enrolled in course 2" "60" "$ENROLLED_COUNT"
echo ""

echo "--- Test: --course --course-role student ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --course 2 --course-role student -o csv)
assert_output_contains "Student present" "student01" "$OUT"
assert_output_not_contains "Teacher excluded" "teacher01" "$OUT"
STUDENT_COUNT=$(echo "$OUT" | tail -n +2 | wc -l)
assert_output_contains "50 students in course 2" "50" "$STUDENT_COUNT"
echo ""

echo "--- Test: --course --course-role editingteacher ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --course 2 --course-role editingteacher -o csv)
assert_output_contains "Teacher present" "teacher01" "$OUT"
assert_output_not_contains "Student excluded" "student01" "$OUT"
TEACHER_COUNT=$(echo "$OUT" | tail -n +2 | wc -l)
assert_output_contains "10 teachers in course 2" "10" "$TEACHER_COUNT"
echo ""

echo "--- Test: --course with empty course ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --course 14 -o csv)
LINE_COUNT=$(echo "$OUT" | wc -l)
# Empty course has no enrolments, output should be empty (no header when no rows)
assert_output_not_contains "No users in empty course" "student" "$OUT"
echo ""

echo "--- Test: --course --course-inactive ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --course 2 --course-inactive -o csv)
echo "$OUT" | head -5
# All enrolled users have never accessed course 2 in test data
assert_output_contains "Inactive student present" "student01" "$OUT"
echo ""

# ── SQL filter ────────────────────────────────────────────────────

echo "--- Test: --sql option ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --sql "u.username = 'admin'" -o csv)
echo "$OUT"
assert_output_contains "SQL filter returns admin" "admin" "$OUT"
assert_output_not_contains "SQL filter excludes students" "student01" "$OUT"
echo ""

# ── Pipe --sql -i into user:list --stdin ──────────────────────────

echo "--- Test: Pipe user:list --sql -i into user:list --stdin ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --sql "u.username = 'admin'" -i | $PHP $MOOSH user:list -p "$MOODLE_PATH" --stdin -o csv)
echo "$OUT"
assert_output_contains "Piped output has header" "id,username,email" "$OUT"
assert_output_contains "Piped output contains admin" "admin" "$OUT"
assert_output_not_contains "Piped output excludes students" "student01" "$OUT"
echo ""

# ── Pipe user:list -i into user:list --stdin ──────────────────────

echo "--- Test: Pipe user:list -i into user:list --stdin ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --limit 3 -i | $PHP $MOOSH user:list -p "$MOODLE_PATH" --stdin -o csv)
echo "$OUT"
assert_output_contains "Piped output has header" "id,username,email" "$OUT"
LINE_COUNT=$(echo "$OUT" | wc -l)
assert_output_contains "Piped output has 4 lines" "4" "$LINE_COUNT"
echo ""

# ── Numeric filters ───────────────────────────────────────────────

echo "--- Test: --number courses-enrolled>0 ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --number courses-enrolled\>0 -o csv)
assert_output_contains "Enrolled student present" "student01" "$OUT"
assert_output_contains "Enrolled teacher present" "teacher01" "$OUT"
assert_output_not_contains "Guest excluded" "guest" "$OUT"
echo ""

echo "--- Test: --number courses-enrolled=0 ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --number courses-enrolled=0 -o csv)
echo "$OUT"
assert_output_contains "Guest present with no enrolments" "guest" "$OUT"
assert_output_contains "Admin present with no enrolments" "admin" "$OUT"
assert_output_not_contains "Student excluded" "student01" "$OUT"
echo ""

# ── JSON output ───────────────────────────────────────────────────

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --sql "u.username = 'admin'" -o json)
echo "$OUT"
assert_output_contains "JSON has username key" '"username"' "$OUT"
assert_output_contains "JSON has admin value" '"admin"' "$OUT"
echo ""

# ── Table output ──────────────────────────────────────────────────

echo "--- Test: Table output ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --limit 3)
echo "$OUT"
assert_output_contains "Table has id header" "id" "$OUT"
assert_output_contains "Table has username header" "username" "$OUT"
assert_output_contains "Table has email header" "email" "$OUT"
echo ""

# ── Help output ───────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --help)
assert_output_contains "Help shows description" "List Moodle users" "$OUT"
assert_output_contains "Help shows is/is-not options" "--is" "$OUT"
assert_output_contains "Help shows course option" "--course" "$OUT"
assert_output_contains "Help shows course-role option" "--course-role" "$OUT"
assert_output_contains "Help shows sort option" "--sort" "$OUT"
assert_output_contains "Help shows limit option" "--limit" "$OUT"
assert_output_contains "Help shows --number option" "--number" "$OUT"
assert_output_contains "Help shows --sql option" "--sql" "$OUT"
assert_output_contains "Help shows courses-enrolled metric" "courses-enrolled" "$OUT"
echo ""

# ── Course-enrol-plugin ───────────────────────────────────────────

echo "--- Test: --course --course-enrol-plugin manual ---"
OUT=$($PHP $MOOSH user:list -p "$MOODLE_PATH" --course 2 --course-enrol-plugin manual -o csv)
assert_output_contains "Manual enrolment student present" "student01" "$OUT"
assert_output_contains "Manual enrolment teacher present" "teacher01" "$OUT"
echo ""

# ── user-list alias ───────────────────────────────────────────────


print_summary
