#!/usr/bin/env bash
#
# Integration test for moosh2 context:rebuild command
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_context_rebuild.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 context:rebuild integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Test data summary:
#   Uses existing test data. context:rebuild operates on all contexts globally.
#   No special test data needed — we verify it runs and reports stats.

# ── Basic rebuild ────────────────────────────────────────────────

echo "--- Test: Basic rebuild ---"
OUT=$($PHP $MOOSH context:rebuild -p "$MOODLE_PATH" 2>&1)
echo "$OUT"
assert_output_contains "Shows Before rebuild" "Before rebuild" "$OUT"
assert_output_contains "Shows Total contexts" "Total contexts" "$OUT"
assert_output_contains "Shows empty paths before" "Contexts with empty paths" "$OUT"
assert_output_contains "Shows Frozen contexts" "Frozen contexts" "$OUT"
assert_output_contains "Shows cleanup message" "Cleaned up orphaned context instances" "$OUT"
assert_output_contains "Shows rebuild message" "Rebuilt all context paths" "$OUT"
assert_output_contains "Shows After rebuild" "After rebuild" "$OUT"
echo ""

# ── Level breakdown ──────────────────────────────────────────────

echo "--- Test: Level breakdown in output ---"
assert_output_contains "Shows System level" "System" "$OUT"
assert_output_contains "Shows User level" "User" "$OUT"
assert_output_contains "Shows Course category level" "Course category" "$OUT"
assert_output_contains "Shows Course level" "Course:" "$OUT"
assert_output_contains "Shows Module level" "Module" "$OUT"
assert_output_contains "Shows Block level" "Block" "$OUT"
echo ""

# ── No changes needed on clean DB ────────────────────────────────

echo "--- Test: No changes on clean database ---"
assert_output_contains "Reports no changes needed" "No changes were needed" "$OUT"
echo ""

# ── Rebuild after corrupting a path ──────────────────────────────

echo "--- Test: Rebuild fixes empty paths ---"
# Corrupt a context path
$PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$ctx = \$DB->get_record_sql('SELECT id FROM {context} WHERE contextlevel = 50 LIMIT 1');
\$DB->set_field('context', 'path', '', ['id' => \$ctx->id]);
\$DB->set_field('context', 'depth', 0, ['id' => \$ctx->id]);
" 2>/dev/null

OUT=$($PHP $MOOSH context:rebuild -p "$MOODLE_PATH" 2>&1)
echo "$OUT"
assert_output_contains "Shows empty paths before fix" "Contexts with empty paths: 1" "$OUT"
assert_output_contains "Shows empty paths fixed after" "Empty paths fixed: 1" "$OUT"
echo ""

# ── Help output ──────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH context:rebuild -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help shows description" "Rebuild all context paths" "$OUT"
assert_output_contains "Help shows description keyword" "context" "$OUT"
echo ""

# ── Alias ────────────────────────────────────────────────────────


print_summary
