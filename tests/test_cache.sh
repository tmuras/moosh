#!/usr/bin/env bash
#
# Integration tests for moosh2 cache commands:
#   cache:purge, cache:rebuild, cache:info, cache:create, cache:mod
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_cache.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 cache commands integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  cache:purge
# ═══════════════════════════════════════════════════════════════════

echo "========== cache:purge =========="
echo ""

echo "--- Test: Purge all caches ---"
OUT=$($PHP $MOOSH cache:purge -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Purge all exit code 0" 0 $EC
assert_output_contains "Shows purged" "All caches purged" "$OUT"
echo ""

echo "--- Test: Purge specific store ---"
OUT=$($PHP $MOOSH cache:purge --store default_application -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Purge store exit code 0" 0 $EC
assert_output_contains "Shows store purged" "Purged cache store" "$OUT"
echo ""

echo "--- Test: Purge invalid store ---"
OUT=$($PHP $MOOSH cache:purge --store nonexistent -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid store" 1 $EC
assert_output_contains "Error for invalid store" "not found" "$OUT"
echo ""

echo "--- Test: Purge by definition ---"
OUT=$($PHP $MOOSH cache:purge --definition core/coursemodinfo -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Purge definition exit code 0" 0 $EC
assert_output_contains "Shows definition purged" "Purged cache definition" "$OUT"
echo ""

echo "--- Test: Store and definition together ---"
OUT=$($PHP $MOOSH cache:purge --store default_application --definition core/coursemodinfo -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for both options" 1 $EC
assert_output_contains "Error for both options" "not both" "$OUT"
echo ""

echo "--- Test: cache:purge help ---"
OUT=$($PHP $MOOSH cache:purge -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Purge caches" "$OUT"
assert_output_contains "Help shows --store" "--store" "$OUT"
assert_output_contains "Help shows --definition" "--definition" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  cache:rebuild
# ═══════════════════════════════════════════════════════════════════

echo "========== cache:rebuild =========="
echo ""

echo "--- Test: Rebuild single course ---"
OUT=$($PHP $MOOSH cache:rebuild 2 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Rebuild single exit code 0" 0 $EC
assert_output_contains "Shows rebuilt" "Rebuilt course cache" "$OUT"
assert_output_contains "Shows course name" "algebrafundamentals" "$OUT"
echo ""

echo "--- Test: Rebuild all courses ---"
OUT=$($PHP $MOOSH cache:rebuild --all -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Rebuild all exit code 0" 0 $EC
assert_output_contains "Shows all rebuilt" "all courses" "$OUT"
echo ""

echo "--- Test: No arguments ---"
OUT=$($PHP $MOOSH cache:rebuild -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for no args" 1 $EC
assert_output_contains "Error for no args" "Specify" "$OUT"
echo ""

echo "--- Test: Invalid course ---"
OUT=$($PHP $MOOSH cache:rebuild 999 -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid course" 1 $EC
assert_output_contains "Error for invalid course" "not found" "$OUT"
echo ""

echo "--- Test: cache:rebuild help ---"
OUT=$($PHP $MOOSH cache:rebuild -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Rebuild course cache" "$OUT"
assert_output_contains "Help shows --all" "--all" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  cache:info
# ═══════════════════════════════════════════════════════════════════

echo "========== cache:info =========="
echo ""

echo "--- Test: Show all ---"
OUT=$($PHP $MOOSH cache:info -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Info all exit code 0" 0 $EC
assert_output_contains "Shows stores section" "Cache Stores" "$OUT"
assert_output_contains "Shows mappings section" "Mode Mappings" "$OUT"
assert_output_contains "Shows definitions section" "Cache Definitions" "$OUT"
assert_output_contains "Shows locks section" "Cache Locks" "$OUT"
echo ""

echo "--- Test: Stores only ---"
OUT=$($PHP $MOOSH cache:info --stores -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Shows stores" "default_application" "$OUT"
assert_output_contains "Shows plugin" "file" "$OUT"
assert_output_not_contains "No definitions section" "Cache Definitions" "$OUT"
echo ""

echo "--- Test: Definitions only ---"
OUT=$($PHP $MOOSH cache:info --definitions -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Shows definitions" "Cache Definitions" "$OUT"
assert_output_contains "Shows a definition" "core" "$OUT"
echo ""

echo "--- Test: Mappings only ---"
OUT=$($PHP $MOOSH cache:info --mappings -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Shows mode mappings" "Mode Mappings" "$OUT"
assert_output_contains "Shows application mapping" "application" "$OUT"
echo ""

echo "--- Test: Locks only ---"
OUT=$($PHP $MOOSH cache:info --locks -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Shows locks" "Cache Locks" "$OUT"
echo ""

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH cache:info --stores -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "CSV has header" "name,plugin,modes,default" "$OUT"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH cache:info --stores -p "$MOODLE_PATH" -o json 2>&1)
assert_output_contains "JSON has plugin" '"plugin": "file"' "$OUT"
echo ""

echo "--- Test: cache:info help ---"
OUT=$($PHP $MOOSH cache:info -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Show cache configuration" "$OUT"
assert_output_contains "Help shows --stores" "--stores" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  cache:create
# ═══════════════════════════════════════════════════════════════════

echo "========== cache:create =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH cache:create file teststore -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Create file store ---"
OUT=$($PHP $MOOSH cache:create file testfilestore -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Create file exit code 0" 0 $EC
assert_output_contains "Shows name" "testfilestore" "$OUT"
assert_output_contains "Shows plugin" "file" "$OUT"
echo ""

echo "--- Test: Duplicate name ---"
OUT=$($PHP $MOOSH cache:create file testfilestore -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for duplicate" 1 $EC
assert_output_contains "Error for duplicate" "already exists" "$OUT"
echo ""

echo "--- Test: Invalid plugin ---"
OUT=$($PHP $MOOSH cache:create nonexistent mystore -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid plugin" 1 $EC
assert_output_contains "Error for invalid plugin" "not found" "$OUT"
echo ""

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH cache:create file csvstore -p "$MOODLE_PATH" --run -o csv 2>&1)
assert_output_contains "CSV header" "name,plugin,server,prefix" "$OUT"
assert_output_contains "CSV has name" "csvstore" "$OUT"
echo ""

echo "--- Test: cache:create help ---"
OUT=$($PHP $MOOSH cache:create -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Create a cache store instance" "$OUT"
assert_output_contains "Help shows plugin" "plugin" "$OUT"
assert_output_contains "Help shows --server" "--server" "$OUT"
echo ""


# Verify store was created
OUT=$($PHP $MOOSH cache:info --stores -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "Store visible in info" "testfilestore" "$OUT"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  cache:mod
# ═══════════════════════════════════════════════════════════════════

echo "========== cache:mod =========="
echo ""

echo "--- Test: Mode mapping dry run ---"
OUT=$($PHP $MOOSH cache:mod --application testfilestore -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Dry run exit code 0" 0 $EC
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows store" "testfilestore" "$OUT"
echo ""

echo "--- Test: Set mode mapping ---"
OUT=$($PHP $MOOSH cache:mod --application testfilestore -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Mode mapping exit code 0" 0 $EC
assert_output_contains "Shows application" "application" "$OUT"
assert_output_contains "Shows store name" "testfilestore" "$OUT"
echo ""

# Restore to default
$PHP $MOOSH cache:mod --application default_application -p "$MOODLE_PATH" --run > /dev/null 2>&1

echo "--- Test: Definition mapping dry run ---"
# Get a valid definition
DEF_ID=$($PHP $MOOSH cache:info --definitions -p "$MOODLE_PATH" -o csv 2>&1 | grep application | head -1 | cut -d, -f1)
echo "  Using definition: $DEF_ID"
OUT=$($PHP $MOOSH cache:mod --definition "$DEF_ID" --store testfilestore -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Def mapping dry run exit code 0" 0 $EC
assert_output_contains "Shows def dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Set definition mapping ---"
OUT=$($PHP $MOOSH cache:mod --definition "$DEF_ID" --store testfilestore -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Def mapping exit code 0" 0 $EC
assert_output_contains "Shows definition" "$DEF_ID" "$OUT"
assert_output_contains "Shows store" "testfilestore" "$OUT"
echo ""

echo "--- Test: No options ---"
OUT=$($PHP $MOOSH cache:mod -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for no options" 1 $EC
assert_output_contains "Error for no options" "Specify" "$OUT"
echo ""

echo "--- Test: Definition without store ---"
OUT=$($PHP $MOOSH cache:mod --definition core/coursemodinfo -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for def without store" 1 $EC
assert_output_contains "Error for missing store" "--store" "$OUT"
echo ""

echo "--- Test: Invalid store ---"
OUT=$($PHP $MOOSH cache:mod --application nonexistent -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid store" 1 $EC
assert_output_contains "Error for invalid store" "not found" "$OUT"
echo ""

echo "--- Test: Invalid definition ---"
OUT=$($PHP $MOOSH cache:mod --definition fake/definition --store testfilestore -p "$MOODLE_PATH" --run 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid def" 1 $EC
assert_output_contains "Error for invalid def" "not found" "$OUT"
echo ""

echo "--- Test: cache:mod help ---"
OUT=$($PHP $MOOSH cache:mod -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Modify cache mode or definition mappings" "$OUT"
assert_output_contains "Help shows --application" "--application" "$OUT"
assert_output_contains "Help shows --definition" "--definition" "$OUT"
echo ""


print_summary
