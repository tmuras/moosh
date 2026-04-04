#!/usr/bin/env bash
#
# Integration test for moosh2 context:info command
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_context_info.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 context:info integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Discover context IDs dynamically
echo "Discovering context IDs..."
SYSTEM_CTX=1
USER_CTX=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$c = \$DB->get_record_sql('SELECT id FROM {context} WHERE contextlevel = 30 AND instanceid = 2');
echo \$c->id;
" 2>/dev/null)
CAT_CTX=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$c = \$DB->get_record_sql('SELECT id FROM {context} WHERE contextlevel = 40 AND instanceid = 2');
echo \$c->id;
" 2>/dev/null)
COURSE_CTX=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$c = \$DB->get_record_sql('SELECT id FROM {context} WHERE contextlevel = 50 AND instanceid = 2');
echo \$c->id;
" 2>/dev/null)
MODULE_CTX=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$c = \$DB->get_record_sql('SELECT id FROM {context} WHERE contextlevel = 70 LIMIT 1');
echo \$c->id;
" 2>/dev/null)
BLOCK_CTX=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$c = \$DB->get_record_sql('SELECT id FROM {context} WHERE contextlevel = 80 LIMIT 1');
echo \$c->id;
" 2>/dev/null)
echo "  System=$SYSTEM_CTX, User(admin)=$USER_CTX, Category(Math)=$CAT_CTX, Course(Algebra)=$COURSE_CTX, Module=$MODULE_CTX, Block=$BLOCK_CTX"
echo ""

# ── System context ────────────────────────────────────────────────

echo "--- Test: System context (table) ---"
OUT=$($PHP $MOOSH context:info -p "$MOODLE_PATH" $SYSTEM_CTX)
echo "$OUT"
assert_output_contains "Shows Context ID" "Context ID" "$OUT"
assert_output_contains "Shows System level name" "System" "$OUT"
assert_output_contains "Shows context level 10" "10" "$OUT"
assert_output_contains "Shows Total users" "Total users" "$OUT"
assert_output_contains "Shows Total courses" "Total courses" "$OUT"
assert_output_contains "Shows Total categories" "Total categories" "$OUT"
assert_output_contains "Shows Child contexts" "Child contexts" "$OUT"
assert_output_contains "Shows Capability overrides" "Capability overrides" "$OUT"
assert_output_contains "Shows Log entries" "Log entries" "$OUT"
echo ""

echo "--- Test: System context (JSON) ---"
OUT=$($PHP $MOOSH context:info -p "$MOODLE_PATH" $SYSTEM_CTX -o json)
assert_output_contains "JSON Context level" '"Context level": 10' "$OUT"
assert_output_contains "JSON Context level name" '"Context level name": "System"' "$OUT"
assert_output_contains "JSON Total users 62" '"Total users": 62' "$OUT"
assert_output_contains "JSON Total courses 16" '"Total courses": 16' "$OUT"
assert_output_contains "JSON Total categories 5" '"Total categories": 5' "$OUT"
echo ""

# ── User context ──────────────────────────────────────────────────

echo "--- Test: User context (admin) ---"
OUT=$($PHP $MOOSH context:info -p "$MOODLE_PATH" $USER_CTX -o json)
echo "$OUT" | head -15
assert_output_contains "User level 30" '"Context level": 30' "$OUT"
assert_output_contains "User level name" '"Context level name": "User"' "$OUT"
assert_output_contains "Username admin" '"Username": "admin"' "$OUT"
assert_output_contains "Full name" '"Full name": "Admin User"' "$OUT"
assert_output_contains "Email" '"Email": "admin@example.com"' "$OUT"
assert_output_contains "Not suspended" '"Suspended": 0' "$OUT"
echo ""

# ── Category context ──────────────────────────────────────────────

echo "--- Test: Category context (Mathematics) ---"
OUT=$($PHP $MOOSH context:info -p "$MOODLE_PATH" $CAT_CTX -o json)
echo "$OUT" | head -15
assert_output_contains "Category level 40" '"Context level": 40' "$OUT"
assert_output_contains "Category level name" '"Course category"' "$OUT"
assert_output_contains "Category name Mathematics" '"Category name": "Mathematics"' "$OUT"
assert_output_contains "Category visible" '"Category visible": 1' "$OUT"
assert_output_contains "Category course count 3" '"Category course count": 3' "$OUT"
assert_output_contains "Children at Course level" '"Children at Course level"' "$OUT"
echo ""

# ── Course context ────────────────────────────────────────────────

echo "--- Test: Course context (Algebra Fundamentals) ---"
OUT=$($PHP $MOOSH context:info -p "$MOODLE_PATH" $COURSE_CTX -o json)
echo "$OUT" | head -20
assert_output_contains "Course level 50" '"Context level": 50' "$OUT"
assert_output_contains "Course shortname" '"Course shortname": "algebrafundamentals_2"' "$OUT"
assert_output_contains "Course fullname" '"Algebra Fundamentals"' "$OUT"
assert_output_contains "Course visible" '"Course visible": 1' "$OUT"
assert_output_contains "Enrolled users 60" '"Enrolled users": 60' "$OUT"
assert_output_contains "Course modules 1" '"Course modules": 1' "$OUT"
assert_output_contains "Course sections 4" '"Course sections": 4' "$OUT"
assert_output_contains "60 role assignments" '"Role assignments": 60' "$OUT"
assert_output_contains "50 students assigned" '"Assigned as student": 50' "$OUT"
assert_output_contains "10 teachers assigned" '"Assigned as editingteacher": 10' "$OUT"
echo ""

# ── Module context ────────────────────────────────────────────────

echo "--- Test: Module context ---"
OUT=$($PHP $MOOSH context:info -p "$MOODLE_PATH" $MODULE_CTX -o json)
echo "$OUT" | head -20
assert_output_contains "Module level 70" '"Context level": 70' "$OUT"
assert_output_contains "Module level name" '"Module"' "$OUT"
assert_output_contains "Module type resource" '"Module type": "resource"' "$OUT"
assert_output_contains "Has Activity name" '"Activity name"' "$OUT"
assert_output_contains "Has Course ID" '"Course ID"' "$OUT"
assert_output_contains "Has Section ID" '"Section ID"' "$OUT"
assert_output_contains "Module visible" '"Module visible": 1' "$OUT"
assert_output_contains "Has Added on" '"Added on"' "$OUT"
assert_output_contains "Completion tracking" '"Completion tracking"' "$OUT"
assert_output_contains "1 file in context" '"Files in context": 1' "$OUT"
echo ""

# ── Block context ─────────────────────────────────────────────────

echo "--- Test: Block context ---"
OUT=$($PHP $MOOSH context:info -p "$MOODLE_PATH" $BLOCK_CTX -o json)
echo "$OUT" | head -20
assert_output_contains "Block level 80" '"Context level": 80' "$OUT"
assert_output_contains "Block level name" '"Block"' "$OUT"
assert_output_contains "Has Block type" '"Block type"' "$OUT"
assert_output_contains "Has Parent context ID" '"Parent context ID"' "$OUT"
assert_output_contains "Has Parent context name" '"Parent context name"' "$OUT"
assert_output_contains "Has Block region" '"Block region"' "$OUT"
echo ""

# ── Path names ────────────────────────────────────────────────────

echo "--- Test: Path names resolution ---"
OUT=$($PHP $MOOSH context:info -p "$MOODLE_PATH" $MODULE_CTX -o json)
assert_output_contains "Path names has System" "System" "$OUT"
assert_output_contains "Path names has Mathematics" "Mathematics" "$OUT"
assert_output_contains "Path names has Algebra" "Algebra" "$OUT"
echo ""

# ── CSV output ────────────────────────────────────────────────────

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH context:info -p "$MOODLE_PATH" $SYSTEM_CTX -o csv)
echo "$OUT" | head -2
assert_output_contains "CSV has Context ID header" '"Context ID"' "$OUT"
assert_output_contains "CSV has Context level header" '"Context level"' "$OUT"
echo ""

# ── Invalid context ID ───────────────────────────────────────────

echo "--- Test: Invalid context ID ---"
OUT=$($PHP $MOOSH context:info -p "$MOODLE_PATH" 999 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code is 1 for invalid context" 1 "$EXIT_CODE"
assert_output_contains "Error message" "not found" "$OUT"
echo ""

# ── Missing contextid argument ───────────────────────────────────

echo "--- Test: Missing contextid argument ---"
OUT=$($PHP $MOOSH context:info -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Error mentions missing argument" "contextid" "$OUT"
echo ""

# ── Help output ───────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH context:info -p "$MOODLE_PATH" --help)
assert_output_contains "Help shows description" "Show detailed information about a context" "$OUT"
assert_output_contains "Help shows contextid argument" "contextid" "$OUT"
echo ""

# ── context-info alias ───────────────────────────────────────────


print_summary
