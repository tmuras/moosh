#!/usr/bin/env bash
#
# Integration test for moosh2 config:get, config:set
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_config.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 config:get/set integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ═══════════════════════════════════════════════════════════════════
# config:get
# ═══════════════════════════════════════════════════════════════════

echo "========== config:get =========="
echo ""

echo "--- Test: Get single core value ---"
OUT=$($PHP $MOOSH config:get -p "$MOODLE_PATH" theme 2>&1)
assert_output_contains "Theme is boost" "boost" "$OUT"
echo ""

echo "--- Test: Get all core settings (CSV) ---"
OUT=$($PHP $MOOSH config:get -p "$MOODLE_PATH" -o csv 2>&1 | head -3)
assert_output_contains "Header row" "name,value" "$OUT"
echo ""

echo "--- Test: Get plugin settings ---"
OUT=$($PHP $MOOSH config:get -p "$MOODLE_PATH" --plugin mod_forum -o csv 2>&1)
assert_output_contains "Forum version" "version" "$OUT"
echo ""

echo "--- Test: Get single plugin value ---"
OUT=$($PHP $MOOSH config:get -p "$MOODLE_PATH" --plugin mod_forum version 2>&1)
if echo "$OUT" | grep -qE '^[0-9]+$'; then
    echo "  PASS: Plugin version is numeric ($OUT)"
    ((PASS++))
else
    echo "  FAIL: Expected numeric version, got: $OUT"
    ((FAIL++))
fi
echo ""

echo "--- Test: Nonexistent setting ---"
OUT=$($PHP $MOOSH config:get -p "$MOODLE_PATH" xyznonexistent123 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for nonexistent" 1 "$EXIT_CODE"
assert_output_contains "Not found" "not found" "$OUT"
echo ""

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH config:get -p "$MOODLE_PATH" --plugin mod_forum -o json 2>&1)
assert_output_contains "JSON has name" '"name"' "$OUT"
assert_output_contains "JSON has value" '"value"' "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH config:get -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Get Moodle configuration" "$OUT"
assert_output_contains "Help shows --plugin" "--plugin" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
# config:set
# ═══════════════════════════════════════════════════════════════════

echo "========== config:set =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH config:set -p "$MOODLE_PATH" forcelogin 1 2>&1)
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows name" "forcelogin" "$OUT"
assert_output_contains "Shows new value" "New:     1" "$OUT"
echo ""

echo "--- Test: Set core value ---"
OUT=$($PHP $MOOSH config:set -p "$MOODLE_PATH" --run forcelogin 1 2>&1)
assert_output_contains "Shows set" "Set core/forcelogin" "$OUT"
# Verify
OUT=$($PHP $MOOSH config:get -p "$MOODLE_PATH" forcelogin 2>&1)
assert_output_contains "Value was set" "1" "$OUT"
echo ""

echo "--- Test: Set plugin value ---"
OUT=$($PHP $MOOSH config:set -p "$MOODLE_PATH" --run --plugin mod_forum trackingtype 2 2>&1)
assert_output_contains "Shows plugin set" "mod_forum/trackingtype" "$OUT"
# Verify
OUT=$($PHP $MOOSH config:get -p "$MOODLE_PATH" --plugin mod_forum trackingtype 2>&1)
assert_output_contains "Plugin value was set" "2" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH config:set -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Set a Moodle configuration" "$OUT"
assert_output_contains "Help shows --plugin" "--plugin" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
# config:export
# ═══════════════════════════════════════════════════════════════════

echo "========== config:export =========="
echo ""

echo "--- Test: Export core to stdout ---"
OUT=$($PHP $MOOSH config:export -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Has _type" '"_type": "core"' "$OUT"
assert_output_contains "Has settings" '"settings"' "$OUT"
assert_output_contains "Has theme" '"theme"' "$OUT"
echo ""

echo "--- Test: Export core to file ---"
TMPFILE=$(mktemp /tmp/moosh_config_XXXXXX.json)
OUT=$($PHP $MOOSH config:export -p "$MOODLE_PATH" "$TMPFILE" 2>&1)
assert_output_contains "Shows exported" "Exported to" "$OUT"
assert_output_contains "Shows filename" "$TMPFILE" "$OUT"
# Verify file content
CONTENT=$(cat "$TMPFILE")
echo "$CONTENT" | grep -q '"_type": "core"'
if [ $? -eq 0 ]; then
    echo "  PASS: File contains core export"
    ((PASS++))
else
    echo "  FAIL: File does not contain core export"
    ((FAIL++))
fi
echo ""

echo "--- Test: Export plugin ---"
OUT=$($PHP $MOOSH config:export --plugin mod_forum -p "$MOODLE_PATH" 2>&1)
assert_output_contains "Has plugin type" '"_type": "plugin"' "$OUT"
assert_output_contains "Has plugin name" '"plugin": "mod_forum"' "$OUT"
assert_output_contains "Has version" '"version"' "$OUT"
echo ""

echo "--- Test: Export plugin to file ---"
PLUGIN_FILE=$(mktemp /tmp/moosh_plugin_XXXXXX.json)
OUT=$($PHP $MOOSH config:export --plugin mod_forum -p "$MOODLE_PATH" "$PLUGIN_FILE" 2>&1)
assert_output_contains "Plugin exported" "Exported to" "$OUT"
echo ""

echo "--- Test: Export all ---"
OUT=$($PHP $MOOSH config:export --plugin all -p "$MOODLE_PATH" 2>&1 | head -5)
assert_output_contains "Has all type" '"_type": "all"' "$OUT"
assert_output_contains "Has core key" '"core"' "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH config:export -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Export Moodle configuration" "$OUT"
assert_output_contains "Help shows --plugin" "--plugin" "$OUT"
echo ""

# ═══════════════════════════════════════════════════════════════════
# config:import
# ═══════════════════════════════════════════════════════════════════

echo "========== config:import =========="
echo ""

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH config:import -p "$MOODLE_PATH" "$TMPFILE" 2>&1)
assert_output_contains "Shows dry run" "Dry run" "$OUT"
echo ""

echo "--- Test: Import core with --run ---"
OUT=$($PHP $MOOSH config:import -p "$MOODLE_PATH" --run "$TMPFILE" 2>&1)
assert_output_contains "Shows summary" "settings" "$OUT"
echo ""

echo "--- Test: Import plugin ---"
OUT=$($PHP $MOOSH config:import -p "$MOODLE_PATH" --run "$PLUGIN_FILE" 2>&1)
assert_output_contains "Plugin import summary" "settings" "$OUT"
echo ""

echo "--- Test: Import with --ignore-existing ---"
OUT=$($PHP $MOOSH config:import -p "$MOODLE_PATH" --run --ignore-existing "$TMPFILE" 2>&1)
assert_output_contains "Ignore existing summary" "skip" "$OUT"
echo ""

echo "--- Test: Nonexistent file ---"
OUT=$($PHP $MOOSH config:import -p "$MOODLE_PATH" /tmp/nonexistent_file.json 2>&1)
EC=$?
assert_exit_code "Exit code 1 for nonexistent file" 1 $EC
assert_output_contains "File not found" "not found" "$OUT"
echo ""

echo "--- Test: Invalid JSON ---"
BADFILE=$(mktemp /tmp/moosh_bad_XXXXXX.json)
echo "not json" > "$BADFILE"
OUT=$($PHP $MOOSH config:import -p "$MOODLE_PATH" "$BADFILE" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid JSON" 1 $EC
assert_output_contains "Invalid JSON error" "Invalid JSON" "$OUT"
rm -f "$BADFILE"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH config:import -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Import Moodle configuration" "$OUT"
assert_output_contains "Help shows --ignore-existing" "--ignore-existing" "$OUT"
echo ""

# Cleanup
rm -f "$TMPFILE" "$PLUGIN_FILE"

print_summary
