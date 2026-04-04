#!/usr/bin/env bash
#
# Integration test for moosh2 theme:info, theme:settings-export, theme:settings-import
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_theme.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 theme commands integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

EXPORT_DIR=$(mktemp -d)

# ═══════════════════════════════════════════════════════════════════
# theme:info
# ═══════════════════════════════════════════════════════════════════

echo "========== theme:info =========="
echo ""

echo "--- Test: Overview (table) ---"
OUT=$($PHP $MOOSH theme:info -p "$MOODLE_PATH" 2>&1)
echo "$OUT"
assert_output_contains "Shows site theme" "boost" "$OUT"
assert_output_contains "Shows course overrides" "Course theme overrides" "$OUT"
assert_output_contains "Shows category overrides" "Category theme overrides" "$OUT"
assert_output_contains "Shows user overrides" "User theme overrides" "$OUT"
echo ""

echo "--- Test: Overview (CSV) ---"
OUT=$($PHP $MOOSH theme:info -p "$MOODLE_PATH" -o csv 2>&1)
assert_output_contains "CSV has site theme" "boost" "$OUT"
assert_output_contains "CSV has header" "Site theme" "$OUT"
echo ""

echo "--- Test: Overview (JSON) ---"
OUT=$($PHP $MOOSH theme:info -p "$MOODLE_PATH" -o json 2>&1)
assert_output_contains "JSON has site theme" '"Site theme"' "$OUT"
assert_output_contains "JSON has boost" "boost" "$OUT"
echo ""

echo "--- Test: Detailed info for boost ---"
OUT=$($PHP $MOOSH theme:info -p "$MOODLE_PATH" boost 2>&1)
echo "$OUT"
assert_output_contains "Shows name" "boost" "$OUT"
assert_output_contains "Shows component" "theme_boost" "$OUT"
assert_output_contains "Shows version disk" "Version (disk)" "$OUT"
assert_output_contains "Shows status" "uptodate" "$OUT"
assert_output_contains "Shows active site theme" "Active site theme" "$OUT"
assert_output_contains "Shows settings count" "Configuration settings" "$OUT"
echo ""

echo "--- Test: Detailed info (CSV) ---"
OUT=$($PHP $MOOSH theme:info -p "$MOODLE_PATH" boost -o csv 2>&1)
assert_output_contains "CSV detail has component" "theme_boost" "$OUT"
echo ""

echo "--- Test: Nonexistent theme ---"
OUT=$($PHP $MOOSH theme:info -p "$MOODLE_PATH" nonexistent 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for nonexistent theme" 1 "$EXIT_CODE"
assert_output_contains "Shows not found" "not found" "$OUT"
assert_output_contains "Shows available themes" "Available themes" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH theme:info -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Show theme usage information" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
# theme:settings-export
# ═══════════════════════════════════════════════════════════════════

echo "========== theme:settings-export =========="
echo ""

echo "--- Test: Export boost settings ---"
OUT=$($PHP $MOOSH theme:settings-export -p "$MOODLE_PATH" boost --outputdir "$EXPORT_DIR" 2>&1)
echo "$OUT"
assert_output_contains "Shows exported" "exported" "$OUT"
EXPORT_FILE=$(ls -1 "$EXPORT_DIR"/boost_settings_*.tar.gz 2>/dev/null | head -1)
if [ -n "$EXPORT_FILE" ] && [ -f "$EXPORT_FILE" ]; then
    echo "  PASS: Archive file created"
    ((PASS++))
else
    echo "  FAIL: Archive file not created"
    ((FAIL++))
fi
echo ""

echo "--- Test: Nonexistent theme ---"
OUT=$($PHP $MOOSH theme:settings-export -p "$MOODLE_PATH" nonexistent --outputdir "$EXPORT_DIR" 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for nonexistent theme" 1 "$EXIT_CODE"
assert_output_contains "Shows not found" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH theme:settings-export -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Export theme settings" "$OUT"
assert_output_contains "Help shows --outputdir" "--outputdir" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
# theme:settings-import
# ═══════════════════════════════════════════════════════════════════

echo "========== theme:settings-import =========="
echo ""

# Use the first export file
EXPORT_FILE=$(ls -1 "$EXPORT_DIR"/boost_settings_*.tar.gz 2>/dev/null | head -1)

echo "--- Test: Dry run ---"
OUT=$($PHP $MOOSH theme:settings-import -p "$MOODLE_PATH" "$EXPORT_FILE" 2>&1)
echo "$OUT"
assert_output_contains "Shows dry run" "Dry run" "$OUT"
assert_output_contains "Shows theme name" "boost" "$OUT"
assert_output_contains "Shows component" "theme_boost" "$OUT"
assert_output_contains "Shows settings count" "Settings:" "$OUT"
echo ""

echo "--- Test: Import with --run ---"
OUT=$($PHP $MOOSH theme:settings-import -p "$MOODLE_PATH" --run "$EXPORT_FILE" 2>&1)
echo "$OUT"
assert_output_contains "Shows imported" "imported" "$OUT"
assert_output_contains "Shows component" "theme_boost" "$OUT"
echo ""

echo "--- Test: Import to different theme ---"
OUT=$($PHP $MOOSH theme:settings-import -p "$MOODLE_PATH" --target-theme classic "$EXPORT_FILE" 2>&1)
assert_output_contains "Target theme" "classic" "$OUT"
assert_output_contains "Target component" "theme_classic" "$OUT"
echo ""

echo "--- Test: Nonexistent target theme ---"
OUT=$($PHP $MOOSH theme:settings-import -p "$MOODLE_PATH" --run --target-theme nonexistent "$EXPORT_FILE" 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for nonexistent target" 1 "$EXIT_CODE"
assert_output_contains "Shows not installed" "not installed" "$OUT"
echo ""

echo "--- Test: Nonexistent file ---"
OUT=$($PHP $MOOSH theme:settings-import -p "$MOODLE_PATH" /tmp/nonexistent.tar.gz 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for nonexistent file" 1 "$EXIT_CODE"
assert_output_contains "Shows not found" "not found" "$OUT"
echo ""

echo "--- Test: Help ---"
OUT=$($PHP $MOOSH theme:settings-import -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Import theme settings" "$OUT"
assert_output_contains "Help shows --target-theme" "--target-theme" "$OUT"
echo ""


# ── Cleanup ──────────────────────────────────────────────────────

rm -rf "$EXPORT_DIR"

print_summary
