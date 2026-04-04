#!/usr/bin/env bash
#
# Integration tests for moosh2 utility commands:
#   maintenance:on/off, debug:on/off, dashboard:reset,
#   system:check, session:kill, database:check, php:eval
#
# Usage: bash tests/test_utility.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 utility commands integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  maintenance:on / maintenance:off
# ═══════════════════════════════════════════════════════════════════

echo "========== maintenance:on / maintenance:off =========="
echo ""

echo "--- Test: Enable maintenance ---"
OUT=$($PHP $MOOSH maintenance:on -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "On exit code 0" 0 $EC
assert_output_contains "Shows enabled" "enabled" "$OUT"
echo ""

echo "--- Test: Enable with message ---"
OUT=$($PHP $MOOSH maintenance:on --message "Back soon" -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Shows enabled" "enabled" "$OUT"
echo ""

echo "--- Test: Disable maintenance ---"
OUT=$($PHP $MOOSH maintenance:off -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Off exit code 0" 0 $EC
assert_output_contains "Shows disabled" "disabled" "$OUT"
echo ""

echo "--- Test: maintenance:on help ---"
OUT=$($PHP $MOOSH maintenance:on -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Enable maintenance mode" "$OUT"
assert_output_contains "Help shows --message" "--message" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  debug:on / debug:off
# ═══════════════════════════════════════════════════════════════════

echo "========== debug:on / debug:off =========="
echo ""

echo "--- Test: Enable debug ---"
OUT=$($PHP $MOOSH debug:on -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "On exit code 0" 0 $EC
assert_output_contains "Shows enabled" "enabled" "$OUT"
echo ""

echo "--- Test: Disable debug ---"
OUT=$($PHP $MOOSH debug:off -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Off exit code 0" 0 $EC
assert_output_contains "Shows disabled" "disabled" "$OUT"
echo ""

echo "--- Test: debug:on help ---"
OUT=$($PHP $MOOSH debug:on -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Enable developer debug" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  dashboard:reset
# ═══════════════════════════════════════════════════════════════════

echo "========== dashboard:reset =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH dashboard:reset -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Reset dashboards ---"
OUT=$($PHP $MOOSH dashboard:reset -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Reset exit code 0" 0 $EC
assert_output_contains "Shows reset" "reset" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH dashboard:reset -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Reset all user dashboards" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  system:check
# ═══════════════════════════════════════════════════════════════════

echo "========== system:check =========="
echo ""

echo "--- Test: Run all checks ---"
OUT=$($PHP $MOOSH system:check -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Check exit code 0" 0 $EC
assert_output_contains "Shows status column" "status" "$OUT"
assert_output_contains "Shows summary" "Summary" "$OUT"
echo ""

echo "--- Test: Filter by status ---"
OUT=$($PHP $MOOSH system:check --status ok -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Shows ok checks" "ok" "$OUT"
echo ""

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH system:check -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "CSV header" "status,component,check,info" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH system:check -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Run system health" "$OUT"
assert_output_contains "Help shows --status" "--status" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  session:kill
# ═══════════════════════════════════════════════════════════════════

echo "========== session:kill =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH session:kill -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Kill sessions ---"
OUT=$($PHP $MOOSH session:kill -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Kill exit code 0" 0 $EC
assert_output_contains "Shows destroyed" "destroyed" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH session:kill -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Destroy all user sessions" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  database:check
# ═══════════════════════════════════════════════════════════════════

echo "========== database:check =========="
echo ""

echo "--- Test: Check schema ---"
OUT=$($PHP $MOOSH database:check -p "$MOODLE_PATH" 2>&1)
EC=$?
# May find issues or not, both valid
assert_output_not_empty "Check not empty" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH database:check -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Check database schema" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  php:eval
# ═══════════════════════════════════════════════════════════════════

echo "========== php:eval =========="
echo ""

echo "--- Test: Eval simple ---"
OUT=$($PHP $MOOSH php:eval 'echo $CFG->wwwroot' -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Eval exit code 0" 0 $EC
MOODLE_BASENAME="$(basename "${MOODLE_DIR:-/var/www/html/moodle52}")"
assert_output_contains "Shows wwwroot" "$MOODLE_BASENAME" "$OUT"
echo ""

echo "--- Test: Eval DB query ---"
OUT=$($PHP $MOOSH php:eval 'echo $DB->count_records("user")' -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "DB eval exit code 0" 0 $EC
assert_output_not_empty "DB result not empty" "$OUT"
echo ""

echo "--- Test: Eval with error ---"
OUT=$($PHP $MOOSH php:eval 'nonexistent_function()' -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for error" 1 $EC
assert_output_contains "Shows error" "Error" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH php:eval -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Evaluate PHP code" "$OUT"
echo ""


print_summary
