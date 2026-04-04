#!/usr/bin/env bash
#
# Integration tests for moosh2 role commands:
#   role:list, role:create, role:delete, role:export, role:import, role:reset, role:mod
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_role.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 role commands integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Test data summary:
#   Uses Moodle's built-in roles: manager, coursecreator, editingteacher, teacher, student, guest, user, frontpage
#   50 students + 10 teachers enrolled in first 10 courses (role_assignments exist)
#   No custom roles at start — tests create/delete their own

TMPDIR=$(mktemp -d)
trap "rm -rf $TMPDIR" EXIT

# ═══════════════════════════════════════════════════════════════════
#  role:list
# ═══════════════════════════════════════════════════════════════════

echo "--- Test: role:list basic (table) ---"
OUT=$($PHP $MOOSH role:list -p "$MOODLE_PATH" 2>&1)
echo "$OUT"
assert_output_contains "Shows manager role" "manager" "$OUT"
assert_output_contains "Shows student role" "student" "$OUT"
assert_output_contains "Shows editingteacher role" "editingteacher" "$OUT"
assert_output_contains "Shows header id" "id" "$OUT"
assert_output_contains "Shows header shortname" "shortname" "$OUT"
echo ""

echo "--- Test: role:list CSV output ---"
OUT=$($PHP $MOOSH role:list -p "$MOODLE_PATH" -o csv 2>&1)
echo "$OUT"
assert_output_contains "CSV header present" "id,shortname,name,archetype,context_levels,assignments" "$OUT"
assert_output_contains "CSV has manager" "manager" "$OUT"
assert_output_contains "CSV has student" "student" "$OUT"
echo ""

echo "--- Test: role:list JSON output ---"
OUT=$($PHP $MOOSH role:list -p "$MOODLE_PATH" -o json 2>&1)
assert_output_contains "JSON has manager shortname" '"shortname": "manager"' "$OUT"
assert_output_contains "JSON has student shortname" '"shortname": "student"' "$OUT"
echo ""

echo "--- Test: role:list --id-only ---"
OUT=$($PHP $MOOSH role:list -p "$MOODLE_PATH" -i 2>&1)
echo "$OUT"
assert_output_not_empty "ID-only output not empty" "$OUT"
line_count=$(printf '%s' "$OUT" | wc -l)
if [ "$line_count" -le 1 ]; then
    echo "  PASS: ID-only output is a single line"
    ((PASS++))
else
    echo "  FAIL: Expected single line, got $line_count lines"
    ((FAIL++))
fi
echo ""

echo "--- Test: role:list shows context levels ---"
OUT=$($PHP $MOOSH role:list -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "Student has course context" "course" "$OUT"
echo ""

echo "--- Test: role:list shows assignment counts ---"
# Students are enrolled, so student role should have assignments > 0
OUT=$($PHP $MOOSH role:list -p "$MOODLE_PATH" -o json 2>&1)
assert_output_contains "JSON has assignments field" '"assignments"' "$OUT"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  role:list help & alias
# ═══════════════════════════════════════════════════════════════════

echo "--- Test: role:list help ---"
OUT=$($PHP $MOOSH role:list -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help shows description" "List all roles" "$OUT"
assert_output_contains "Help shows --id-only" "--id-only" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  role:create
# ═══════════════════════════════════════════════════════════════════

echo "--- Test: role:create dry run ---"
OUT=$($PHP $MOOSH role:create -p "$MOODLE_PATH" testrole 2>&1)
echo "$OUT"
assert_output_contains "Dry run message" "Dry run" "$OUT"
assert_output_contains "Shows shortname" "testrole" "$OUT"
echo ""

echo "--- Test: role:create with --run ---"
OUT=$($PHP $MOOSH role:create -p "$MOODLE_PATH" testrole --name="Test Role" --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Created role shortname" "testrole" "$OUT"
assert_output_contains "Created role name" "Test Role" "$OUT"
echo ""

# Verify role exists in list
echo "--- Test: Created role appears in role:list ---"
OUT=$($PHP $MOOSH role:list -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "New role in list" "testrole" "$OUT"
echo ""

echo "--- Test: role:create duplicate fails ---"
OUT=$($PHP $MOOSH role:create -p "$MOODLE_PATH" testrole --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Duplicate returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error says already exists" "already exists" "$OUT"
echo ""

echo "--- Test: role:create with archetype ---"
OUT=$($PHP $MOOSH role:create -p "$MOODLE_PATH" testrole_arch --archetype=teacher --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Archetype role created" "testrole_arch" "$OUT"
assert_output_contains "Archetype is teacher" "teacher" "$OUT"
echo ""

echo "--- Test: role:create with context levels ---"
OUT=$($PHP $MOOSH role:create -p "$MOODLE_PATH" testrole_ctx --context=course,module --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Context role created" "testrole_ctx" "$OUT"
echo ""

echo "--- Test: role:create archetype+context mutually exclusive ---"
OUT=$($PHP $MOOSH role:create -p "$MOODLE_PATH" testrole_bad --archetype=teacher --context=course --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Mutual exclusion returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error says mutually exclusive" "mutually exclusive" "$OUT"
echo ""

echo "--- Test: role:create invalid context level ---"
OUT=$($PHP $MOOSH role:create -p "$MOODLE_PATH" testrole_bad2 --context=invalid --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Invalid context returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error says unknown level" "Unknown context level" "$OUT"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  role:mod
# ═══════════════════════════════════════════════════════════════════

echo "--- Test: role:mod change name dry run ---"
OUT=$($PHP $MOOSH role:mod -p "$MOODLE_PATH" testrole --name="Renamed Role" 2>&1)
echo "$OUT"
assert_output_contains "Mod dry run message" "Dry run" "$OUT"
assert_output_contains "Shows name change" 'name:' "$OUT"
echo ""

echo "--- Test: role:mod change name --run ---"
OUT=$($PHP $MOOSH role:mod -p "$MOODLE_PATH" testrole --name="Renamed Role" --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Updated name in output" "Renamed Role" "$OUT"
echo ""

echo "--- Test: role:mod change description ---"
OUT=$($PHP $MOOSH role:mod -p "$MOODLE_PATH" testrole --description="A test description" --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Role still in output" "testrole" "$OUT"
echo ""

echo "--- Test: role:mod set capability ---"
OUT=$($PHP $MOOSH role:mod -p "$MOODLE_PATH" testrole --capability "mod/forum:addnews=allow" --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Capability mod output" "testrole" "$OUT"
echo ""

# Verify capability was set in DB
echo "--- Test: Capability was applied in DB ---"
CAP_SET=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$role = \$DB->get_record('role', ['shortname' => 'testrole']);
\$cap = \$DB->get_record('role_capabilities', ['roleid' => \$role->id, 'capability' => 'mod/forum:addnews']);
echo \$cap ? \$cap->permission : 'NOT_FOUND';
" 2>/dev/null)
if [ "$CAP_SET" = "1" ]; then
    echo "  PASS: Capability mod/forum:addnews set to allow (1)"
    ((PASS++))
else
    echo "  FAIL: Expected permission=1 (allow), got '$CAP_SET'"
    ((FAIL++))
fi
echo ""

echo "--- Test: role:mod multiple capabilities ---"
OUT=$($PHP $MOOSH role:mod -p "$MOODLE_PATH" testrole \
    --capability "mod/forum:addnews=prevent" \
    --capability "mod/forum:addquestion=prohibit" \
    --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Multiple cap mod output" "testrole" "$OUT"
echo ""

echo "--- Test: role:mod context-on ---"
OUT=$($PHP $MOOSH role:mod -p "$MOODLE_PATH" testrole --context-on=course,module --run -o csv 2>&1)
echo "$OUT"
assert_output_contains "Context-on shows course" "course" "$OUT"
assert_output_contains "Context-on shows module" "module" "$OUT"
echo ""

echo "--- Test: role:mod context-off ---"
OUT=$($PHP $MOOSH role:mod -p "$MOODLE_PATH" testrole --context-off=module --run -o csv 2>&1)
echo "$OUT"
assert_output_not_contains "Context-off removed module" "module" "$OUT"
echo ""

echo "--- Test: role:mod no modifications error ---"
OUT=$($PHP $MOOSH role:mod -p "$MOODLE_PATH" testrole --run 2>&1)
EXIT_CODE=$?
assert_exit_code "No mods returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error says no modifications" "No modifications" "$OUT"
echo ""

echo "--- Test: role:mod invalid capability format ---"
OUT=$($PHP $MOOSH role:mod -p "$MOODLE_PATH" testrole --capability "badformat" --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Bad capability format returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error says invalid format" "Invalid capability format" "$OUT"
echo ""

echo "--- Test: role:mod invalid permission ---"
OUT=$($PHP $MOOSH role:mod -p "$MOODLE_PATH" testrole --capability "mod/forum:addnews=badperm" --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Bad permission returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error says unknown permission" "Unknown permission" "$OUT"
echo ""

echo "--- Test: role:mod nonexistent role ---"
OUT=$($PHP $MOOSH role:mod -p "$MOODLE_PATH" nonexistentrole --name="X" --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Nonexistent role returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error says not found" "not found" "$OUT"
echo ""

echo "--- Test: role:mod help ---"
OUT=$($PHP $MOOSH role:mod -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help shows description" "Modify" "$OUT"
assert_output_contains "Help shows --capability" "--capability" "$OUT"
assert_output_contains "Help shows --context-on" "--context-on" "$OUT"
assert_output_contains "Help shows --context-off" "--context-off" "$OUT"
assert_output_contains "Help shows --name" "--name" "$OUT"
echo ""



# ═══════════════════════════════════════════════════════════════════
#  role:export
# ═══════════════════════════════════════════════════════════════════

echo "--- Test: role:export to stdout ---"
OUT=$($PHP $MOOSH role:export -p "$MOODLE_PATH" student 2>&1)
assert_output_contains "Export contains XML" "<?xml" "$OUT"
assert_output_contains "Export contains role shortname" "student" "$OUT"
assert_output_contains "Export contains permissions" "permission" "$OUT"
echo ""

echo "--- Test: role:export --pretty ---"
OUT=$($PHP $MOOSH role:export -p "$MOODLE_PATH" student --pretty 2>&1)
assert_output_contains "Pretty export contains XML" "<?xml" "$OUT"
# Pretty output has indented tags
assert_output_contains "Pretty export is indented" "  <" "$OUT"
echo ""

echo "--- Test: role:export to file ---"
$PHP $MOOSH role:export -p "$MOODLE_PATH" student --file="$TMPDIR/student_export.xml" 2>&1
OUT=$(cat "$TMPDIR/student_export.xml" 2>/dev/null)
assert_output_contains "File contains XML" "<?xml" "$OUT"
assert_output_contains "File contains student shortname" "student" "$OUT"
echo ""

echo "--- Test: role:export by role ID ---"
STUDENT_ID=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$r = \$DB->get_record('role', ['shortname' => 'student']);
echo \$r->id;
" 2>/dev/null)
OUT=$($PHP $MOOSH role:export -p "$MOODLE_PATH" "$STUDENT_ID" 2>&1)
assert_output_contains "Export by ID has XML" "<?xml" "$OUT"
assert_output_contains "Export by ID has student" "student" "$OUT"
echo ""

echo "--- Test: role:export nonexistent role ---"
OUT=$($PHP $MOOSH role:export -p "$MOODLE_PATH" nonexistentrole 2>&1)
EXIT_CODE=$?
assert_exit_code "Nonexistent export returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error says not found" "not found" "$OUT"
echo ""

echo "--- Test: role:export help ---"
OUT=$($PHP $MOOSH role:export -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help shows description" "Export" "$OUT"
assert_output_contains "Help shows --file" "--file" "$OUT"
assert_output_contains "Help shows --pretty" "--pretty" "$OUT"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  role:import
# ═══════════════════════════════════════════════════════════════════

# First export the testrole we created
echo "--- Test: role:import dry run ---"
$PHP $MOOSH role:export -p "$MOODLE_PATH" testrole --file="$TMPDIR/testrole_export.xml" 2>&1
OUT=$($PHP $MOOSH role:import -p "$MOODLE_PATH" "$TMPDIR/testrole_export.xml" 2>&1)
echo "$OUT"
assert_output_contains "Import dry run shows update" "Dry run" "$OUT"
assert_output_contains "Import dry run shows role name" "testrole" "$OUT"
echo ""

# Delete testrole, then reimport
echo "--- Test: role:import creates new role ---"
$PHP $MOOSH role:delete -p "$MOODLE_PATH" testrole --run >/dev/null 2>&1
OUT=$($PHP $MOOSH role:import -p "$MOODLE_PATH" "$TMPDIR/testrole_export.xml" --run --skip-validate 2>&1)
echo "$OUT"
assert_output_contains "Import created role" "Created" "$OUT"
assert_output_contains "Import shows role name" "testrole" "$OUT"
echo ""

# Verify it's back in list
echo "--- Test: Imported role in list ---"
OUT=$($PHP $MOOSH role:list -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "Reimported testrole in list" "testrole" "$OUT"
echo ""

echo "--- Test: role:import updates existing role ---"
OUT=$($PHP $MOOSH role:import -p "$MOODLE_PATH" "$TMPDIR/testrole_export.xml" --run --skip-validate 2>&1)
echo "$OUT"
assert_output_contains "Import updated role" "Updated" "$OUT"
echo ""

echo "--- Test: role:import no file error ---"
OUT=$($PHP $MOOSH role:import -p "$MOODLE_PATH" --run 2>&1)
EXIT_CODE=$?
assert_exit_code "No file returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error mentions file or stdin" "file" "$OUT"
echo ""

echo "--- Test: role:import help ---"
OUT=$($PHP $MOOSH role:import -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help shows description" "Import" "$OUT"
assert_output_contains "Help shows --stdin" "--stdin" "$OUT"
assert_output_contains "Help shows --skip-validate" "--skip-validate" "$OUT"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  role:reset
# ═══════════════════════════════════════════════════════════════════

# Export student role as baseline
$PHP $MOOSH role:export -p "$MOODLE_PATH" student --file="$TMPDIR/student_baseline.xml" 2>&1

echo "--- Test: role:reset dry run ---"
OUT=$($PHP $MOOSH role:reset -p "$MOODLE_PATH" student "$TMPDIR/student_baseline.xml" 2>&1)
echo "$OUT"
assert_output_contains "Reset dry run message" "Dry run" "$OUT"
assert_output_contains "Reset dry run shows role" "student" "$OUT"
echo ""

echo "--- Test: role:reset --run ---"
OUT=$($PHP $MOOSH role:reset -p "$MOODLE_PATH" student "$TMPDIR/student_baseline.xml" --run 2>&1)
echo "$OUT"
assert_output_contains "Reset confirms role" "student" "$OUT"
assert_output_contains "Reset output" "Reset role" "$OUT"
echo ""

echo "--- Test: role:reset nonexistent role ---"
OUT=$($PHP $MOOSH role:reset -p "$MOODLE_PATH" nonexistentrole "$TMPDIR/student_baseline.xml" --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Nonexistent reset returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error says not found" "not found" "$OUT"
echo ""

echo "--- Test: role:reset nonexistent file ---"
OUT=$($PHP $MOOSH role:reset -p "$MOODLE_PATH" student /nonexistent/file.xml --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Bad file returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error says cannot read" "Cannot read" "$OUT"
echo ""

echo "--- Test: role:reset help ---"
OUT=$($PHP $MOOSH role:reset -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help shows description" "Reset" "$OUT"
assert_output_contains "Help shows role argument" "role" "$OUT"
assert_output_contains "Help shows file argument" "file" "$OUT"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  role:delete
# ═══════════════════════════════════════════════════════════════════

echo "--- Test: role:delete dry run ---"
OUT=$($PHP $MOOSH role:delete -p "$MOODLE_PATH" testrole 2>&1)
echo "$OUT"
assert_output_contains "Delete dry run message" "Dry run" "$OUT"
assert_output_contains "Delete dry run shows role" "testrole" "$OUT"
assert_output_contains "Delete dry run shows assignment count" "assignment(s)" "$OUT"
echo ""

echo "--- Test: role:delete --run ---"
OUT=$($PHP $MOOSH role:delete -p "$MOODLE_PATH" testrole --run 2>&1)
echo "$OUT"
assert_output_contains "Delete confirms role" "testrole" "$OUT"
assert_output_contains "Delete output" "Deleted" "$OUT"
echo ""

# Verify it's gone (check for exact shortname, not substring — testrole_arch may still exist)
echo "--- Test: Deleted role not in list ---"
OUT=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
echo \$DB->get_record('role', ['shortname' => 'testrole']) ? 'EXISTS' : 'GONE';
" 2>/dev/null)
if [ "$OUT" = "GONE" ]; then
    echo "  PASS: testrole no longer exists in DB"
    ((PASS++))
else
    echo "  FAIL: testrole still exists in DB"
    ((FAIL++))
fi
echo ""

echo "--- Test: role:delete multiple roles ---"
# Create two roles, then delete both
$PHP $MOOSH role:create -p "$MOODLE_PATH" delme1 --run >/dev/null 2>&1
$PHP $MOOSH role:create -p "$MOODLE_PATH" delme2 --run >/dev/null 2>&1
OUT=$($PHP $MOOSH role:delete -p "$MOODLE_PATH" delme1 delme2 --run 2>&1)
echo "$OUT"
assert_output_contains "Deleted delme1" "delme1" "$OUT"
assert_output_contains "Deleted delme2" "delme2" "$OUT"
echo ""

echo "--- Test: role:delete nonexistent role ---"
OUT=$($PHP $MOOSH role:delete -p "$MOODLE_PATH" nonexistentrole --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Nonexistent delete returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error says not found" "not found" "$OUT"
echo ""

echo "--- Test: role:delete help ---"
OUT=$($PHP $MOOSH role:delete -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help shows description" "Delete" "$OUT"
assert_output_contains "Help shows role argument" "role" "$OUT"
echo ""

# Clean up remaining test roles
$PHP $MOOSH role:delete -p "$MOODLE_PATH" testrole_arch testrole_ctx --run >/dev/null 2>&1

# ═══════════════════════════════════════════════════════════════════
#  role:export → role:import roundtrip
# ═══════════════════════════════════════════════════════════════════

echo "--- Test: Export/import roundtrip ---"
# Export teacher role
$PHP $MOOSH role:export -p "$MOODLE_PATH" editingteacher --file="$TMPDIR/et_export.xml" 2>&1

# Get capability count before
CAP_BEFORE=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$role = \$DB->get_record('role', ['shortname' => 'editingteacher']);
echo \$DB->count_records('role_capabilities', ['roleid' => \$role->id]);
" 2>/dev/null)

# Modify the role
$PHP $MOOSH role:mod -p "$MOODLE_PATH" editingteacher --name="Modified Teacher" --run >/dev/null 2>&1

# Reset from export
$PHP $MOOSH role:reset -p "$MOODLE_PATH" editingteacher "$TMPDIR/et_export.xml" --run >/dev/null 2>&1

# Check name was restored
RESTORED_NAME=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$role = \$DB->get_record('role', ['shortname' => 'editingteacher']);
echo \$role->name;
" 2>/dev/null)
# The name comes from the XML export which had the original name
if [[ "$RESTORED_NAME" != "Modified Teacher" ]]; then
    echo "  PASS: Role name was restored from export (got: $RESTORED_NAME)"
    ((PASS++))
else
    echo "  FAIL: Role name still shows modified value"
    ((FAIL++))
fi
echo ""

print_summary
