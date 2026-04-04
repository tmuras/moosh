#!/usr/bin/env bash
#
# Integration test for moosh2 context:freeze and context:unfreeze commands
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_context_freeze.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 context:freeze / context:unfreeze integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Test data summary:
#   Uses existing test data — 4 categories, 12+ courses, enrolled users.
#   All contexts start unfrozen (locked=0).
#   We freeze/unfreeze course (id=2, "Algebra Fundamentals") and category contexts.

# Discover context IDs dynamically
echo "Discovering context IDs..."
COURSE_CTX=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$c = \$DB->get_record_sql('SELECT id FROM {context} WHERE contextlevel = 50 AND instanceid = 2');
echo \$c->id;
" 2>/dev/null)
CAT_CTX=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$c = \$DB->get_record_sql('SELECT id FROM {context} WHERE contextlevel = 40 AND instanceid = 2');
echo \$c->id;
" 2>/dev/null)
COURSE_CHILD_COUNT=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$ctx = \$DB->get_record('context', ['id' => $COURSE_CTX]);
echo \$DB->count_records_sql('SELECT COUNT(*) FROM {context} WHERE path LIKE ?', [\$ctx->path . '/%']);
" 2>/dev/null)
echo "  Course(Algebra) ctx=$COURSE_CTX, Category(Math) ctx=$CAT_CTX, Course children=$COURSE_CHILD_COUNT"
echo ""

# ── context:freeze dry run ───────────────────────────────────────

echo "--- Test: Freeze dry run (no --run) ---"
OUT=$($PHP $MOOSH context:freeze -p "$MOODLE_PATH" $COURSE_CTX 2>&1)
echo "$OUT"
assert_output_contains "Shows dry run message" "Dry run" "$OUT"
assert_output_contains "Shows context ID" "ID=$COURSE_CTX" "$OUT"
assert_output_contains "Shows Course level" "Course" "$OUT"
assert_output_contains "Shows currently unfrozen" "currently unfrozen" "$OUT"
assert_output_contains "Shows total count" "Total: 1 context(s)" "$OUT"
echo ""

# Verify context is still unfrozen after dry run
echo "--- Test: Context still unfrozen after dry run ---"
LOCKED=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$c = \$DB->get_record('context', ['id' => $COURSE_CTX]);
echo \$c->locked;
" 2>/dev/null)
if [ "$LOCKED" = "0" ]; then
    echo "  PASS: Context still unfrozen after dry run"
    ((PASS++))
else
    echo "  FAIL: Context was modified during dry run (locked=$LOCKED)"
    ((FAIL++))
fi
echo ""

# ── context:freeze with --run ────────────────────────────────────

echo "--- Test: Freeze with --run ---"
OUT=$($PHP $MOOSH context:freeze -p "$MOODLE_PATH" $COURSE_CTX --run 2>&1)
echo "$OUT"
assert_output_contains "Shows frozen message" "Frozen" "$OUT"
assert_output_contains "Shows context ID in output" "ID=$COURSE_CTX" "$OUT"
assert_output_contains "Shows done summary" "1 frozen, 0 already frozen" "$OUT"
echo ""

# Verify context is now frozen
echo "--- Test: Context is frozen in DB ---"
LOCKED=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$c = \$DB->get_record('context', ['id' => $COURSE_CTX]);
echo \$c->locked;
" 2>/dev/null)
if [ "$LOCKED" = "1" ]; then
    echo "  PASS: Context is now frozen in database"
    ((PASS++))
else
    echo "  FAIL: Context not frozen (locked=$LOCKED)"
    ((FAIL++))
fi
echo ""

# ── Freeze already frozen context ────────────────────────────────

echo "--- Test: Freeze already frozen context ---"
OUT=$($PHP $MOOSH context:freeze -p "$MOODLE_PATH" $COURSE_CTX --run 2>&1)
echo "$OUT"
assert_output_contains "Shows already frozen summary" "0 frozen, 1 already frozen" "$OUT"
echo ""

# ── context:unfreeze dry run ─────────────────────────────────────

echo "--- Test: Unfreeze dry run ---"
OUT=$($PHP $MOOSH context:unfreeze -p "$MOODLE_PATH" $COURSE_CTX 2>&1)
echo "$OUT"
assert_output_contains "Unfreeze dry run message" "Dry run" "$OUT"
assert_output_contains "Shows currently frozen" "currently frozen" "$OUT"
echo ""

# ── context:unfreeze with --run ──────────────────────────────────

echo "--- Test: Unfreeze with --run ---"
OUT=$($PHP $MOOSH context:unfreeze -p "$MOODLE_PATH" $COURSE_CTX --run 2>&1)
echo "$OUT"
assert_output_contains "Shows unfrozen message" "Unfrozen" "$OUT"
assert_output_contains "Shows done summary" "1 unfrozen, 0 already unfrozen" "$OUT"
echo ""

# Verify context is unfrozen again
echo "--- Test: Context is unfrozen in DB ---"
LOCKED=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$c = \$DB->get_record('context', ['id' => $COURSE_CTX]);
echo \$c->locked;
" 2>/dev/null)
if [ "$LOCKED" = "0" ]; then
    echo "  PASS: Context is now unfrozen in database"
    ((PASS++))
else
    echo "  FAIL: Context not unfrozen (locked=$LOCKED)"
    ((FAIL++))
fi
echo ""

# ── Unfreeze already unfrozen context ────────────────────────────

echo "--- Test: Unfreeze already unfrozen context ---"
OUT=$($PHP $MOOSH context:unfreeze -p "$MOODLE_PATH" $COURSE_CTX --run 2>&1)
echo "$OUT"
assert_output_contains "Shows already unfrozen summary" "0 unfrozen, 1 already unfrozen" "$OUT"
echo ""

# ── --level option (instance ID mode) ────────────────────────────

echo "--- Test: Freeze by course instance ID with --level ---"
OUT=$($PHP $MOOSH context:freeze -p "$MOODLE_PATH" 2 --level=course --run 2>&1)
echo "$OUT"
assert_output_contains "Frozen via --level" "Frozen" "$OUT"
assert_output_contains "Level output shows Course" "Course" "$OUT"
echo ""

# Unfreeze it back
$PHP $MOOSH context:unfreeze -p "$MOODLE_PATH" 2 --level=course --run >/dev/null 2>&1

# ── --children option ────────────────────────────────────────────

echo "--- Test: Freeze with --children (dry run) ---"
OUT=$($PHP $MOOSH context:freeze -p "$MOODLE_PATH" $COURSE_CTX --children 2>&1)
echo "$OUT"
assert_output_contains "Children dry run shows Dry run" "Dry run" "$OUT"
# Total should be 1 (the course) + children
EXPECTED_TOTAL=$((1 + COURSE_CHILD_COUNT))
assert_output_contains "Children dry run shows total" "Total: $EXPECTED_TOTAL context(s)" "$OUT"
echo ""

echo "--- Test: Freeze with --children (--run) ---"
OUT=$($PHP $MOOSH context:freeze -p "$MOODLE_PATH" $COURSE_CTX --children --run 2>&1)
echo "$OUT"
assert_output_contains "Children freeze done summary" "frozen" "$OUT"
echo ""

# Verify children are effectively frozen (Moodle propagates parent lock via context object)
echo "--- Test: Child contexts are effectively frozen ---"
CHILD_LOCKED=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
require_once(\$CFG->libdir . '/accesslib.php');
\$ctx = \\context::instance_by_id($COURSE_CTX, MUST_EXIST);
\$children = \$DB->get_fieldset_sql('SELECT id FROM {context} WHERE path LIKE ?', [\$ctx->path . '/%']);
\$allLocked = true;
foreach (\$children as \$cid) {
    \$child = \\context::instance_by_id(\$cid, MUST_EXIST);
    if (!\$child->locked) { \$allLocked = false; break; }
}
echo \$allLocked ? '1' : '0';
" 2>/dev/null)
if [ "$CHILD_LOCKED" = "1" ]; then
    echo "  PASS: All child contexts are effectively frozen (parent lock propagates)"
    ((PASS++))
else
    echo "  FAIL: Child contexts not effectively frozen"
    ((FAIL++))
fi
echo ""

# Unfreeze with children
echo "--- Test: Unfreeze with --children (--run) ---"
OUT=$($PHP $MOOSH context:unfreeze -p "$MOODLE_PATH" $COURSE_CTX --children --run 2>&1)
echo "$OUT"
assert_output_contains "Children unfreeze done summary" "unfrozen" "$OUT"
echo ""

# ── Multiple IDs ─────────────────────────────────────────────────

echo "--- Test: Freeze multiple contexts ---"
OUT=$($PHP $MOOSH context:freeze -p "$MOODLE_PATH" $COURSE_CTX $CAT_CTX --run 2>&1)
echo "$OUT"
assert_output_contains "Multiple freeze shows 2 frozen" "2 frozen" "$OUT"
echo ""

# Unfreeze them both
$PHP $MOOSH context:unfreeze -p "$MOODLE_PATH" $COURSE_CTX $CAT_CTX --run >/dev/null 2>&1

# ── Invalid level name ───────────────────────────────────────────

echo "--- Test: Invalid level name ---"
OUT=$($PHP $MOOSH context:freeze -p "$MOODLE_PATH" 1 --level=invalid --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code is 1 for invalid level" 1 "$EXIT_CODE"
assert_output_contains "Error mentions unknown level" "Unknown context level" "$OUT"
echo ""

# ── Invalid context ID ───────────────────────────────────────────

echo "--- Test: Invalid context ID ---"
OUT=$($PHP $MOOSH context:freeze -p "$MOODLE_PATH" 999999 --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code is 1 for invalid ID" 1 "$EXIT_CODE"
assert_output_contains "Error mentions not found" "not found" "$OUT"
echo ""

# ── Help output ──────────────────────────────────────────────────

echo "--- Test: context:freeze help ---"
OUT=$($PHP $MOOSH context:freeze -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help shows description" "Freeze" "$OUT"
assert_output_contains "Help shows --level" "--level" "$OUT"
assert_output_contains "Help shows --children" "--children" "$OUT"
assert_output_contains "Help shows --run" "--run" "$OUT"
echo ""

echo "--- Test: context:unfreeze help ---"
OUT=$($PHP $MOOSH context:unfreeze -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Unfreeze help shows description" "Unfreeze" "$OUT"
assert_output_contains "Unfreeze help shows --level" "--level" "$OUT"
assert_output_contains "Unfreeze help shows --children" "--children" "$OUT"
echo ""

# ── Aliases ──────────────────────────────────────────────────────



print_summary
