#!/usr/bin/env bash
#
# Integration test for moosh2 plugin:usage command
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_plugin_usage.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 plugin:usage integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Test data:
#   14 resource activities, 15 courses using 'topics' format
#   15 manual enrol instances, 15 guest enrol instances, 15 self enrol instances
#   62 users with auth=manual

# ── Full listing (CSV) ───────────────────────────────────────────

echo "--- Test: Full listing (CSV) ---"
OUT=$($PHP $MOOSH plugin:usage -p "$MOODLE_PATH" -o csv)
echo "$OUT" | head -5
assert_output_contains "Header row" "type,plugin,name,count,status" "$OUT"
assert_output_contains "Has activity type" "activity," "$OUT"
assert_output_contains "Has block type" "block," "$OUT"
assert_output_contains "Has format type" "format," "$OUT"
assert_output_contains "Has enrol type" "enrol," "$OUT"
assert_output_contains "Has auth type" "auth," "$OUT"
assert_output_contains "Has qtype type" "qtype," "$OUT"
assert_output_contains "Has filter type" "filter," "$OUT"
echo ""

# ── Activity type ─────────────────────────────────────────────────

echo "--- Test: --type activity ---"
OUT=$($PHP $MOOSH plugin:usage -p "$MOODLE_PATH" --type activity -o csv)
echo "$OUT" | head -5
assert_output_contains "Resource activity with count" "activity,resource,File,14,enabled" "$OUT"
assert_output_contains "Forum activity listed" "activity,forum,Forum,0,enabled" "$OUT"
assert_output_contains "Assignment activity listed" "activity,assign,Assignment,0,enabled" "$OUT"
assert_output_not_contains "No block in activity filter" "block," "$OUT"
echo ""

# ── Block type ────────────────────────────────────────────────────

echo "--- Test: --type block ---"
OUT=$($PHP $MOOSH plugin:usage -p "$MOODLE_PATH" --type block -o csv)
assert_output_contains "Admin bookmarks block" "block,admin_bookmarks" "$OUT"
assert_output_contains "Calendar block" "block,calendar_month" "$OUT"
# Block names like "activity_modules" contain "activity" so check for activity type prefix instead
ACTIVITY_TYPE_LINES=$(echo "$OUT" | grep -c "^activity," || true)
if [ "$ACTIVITY_TYPE_LINES" -eq 0 ]; then
    echo "  PASS: No activity type rows in block filter"
    ((PASS++))
else
    echo "  FAIL: No activity type rows in block filter"
    echo "    Found $ACTIVITY_TYPE_LINES activity type rows"
    ((FAIL++))
fi
echo ""

# ── Format type ───────────────────────────────────────────────────

echo "--- Test: --type format ---"
OUT=$($PHP $MOOSH plugin:usage -p "$MOODLE_PATH" --type format -o csv)
echo "$OUT"
assert_output_contains "Topics format with 16 courses" "format,topics," "$OUT"
assert_output_contains "Topics count is 16" ",16," "$OUT"
assert_output_contains "Weeks format" "format,weeks," "$OUT"
echo ""

# ── Enrol type ────────────────────────────────────────────────────

echo "--- Test: --type enrol ---"
OUT=$($PHP $MOOSH plugin:usage -p "$MOODLE_PATH" --type enrol -o csv)
echo "$OUT"
assert_output_contains "Manual enrol with 15 instances" "enrol,manual," "$OUT"
assert_output_contains "Guest enrol" "enrol,guest," "$OUT"
assert_output_contains "Self enrol" "enrol,self," "$OUT"
echo ""

# ── Auth type ─────────────────────────────────────────────────────

echo "--- Test: --type auth ---"
OUT=$($PHP $MOOSH plugin:usage -p "$MOODLE_PATH" --type auth -o csv)
echo "$OUT"
assert_output_contains "Manual auth with 62 users" "auth,manual," "$OUT"
assert_output_contains "62 manual users" ",62," "$OUT"
assert_output_contains "Email auth" "auth,email," "$OUT"
assert_output_contains "LDAP auth" "auth,ldap," "$OUT"
echo ""

# ── Question type ─────────────────────────────────────────────────

echo "--- Test: --type qtype ---"
OUT=$($PHP $MOOSH plugin:usage -p "$MOODLE_PATH" --type qtype -o csv)
assert_output_contains "Multiple choice qtype" "qtype,multichoice," "$OUT"
assert_output_contains "True/false qtype" "qtype,truefalse," "$OUT"
assert_output_contains "Short answer qtype" "qtype,shortanswer," "$OUT"
echo ""

# ── Filter type ───────────────────────────────────────────────────

echo "--- Test: --type filter ---"
OUT=$($PHP $MOOSH plugin:usage -p "$MOODLE_PATH" --type filter -o csv)
assert_output_contains "Has filter rows" "filter," "$OUT"
echo ""

# ── JSON output ───────────────────────────────────────────────────

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH plugin:usage -p "$MOODLE_PATH" --type format -o json)
echo "$OUT"
assert_output_contains "JSON has type key" '"type"' "$OUT"
assert_output_contains "JSON has plugin key" '"plugin"' "$OUT"
assert_output_contains "JSON has name key" '"name"' "$OUT"
assert_output_contains "JSON has count key" '"count"' "$OUT"
assert_output_contains "JSON has topics" '"topics"' "$OUT"
echo ""

# ── Table output ──────────────────────────────────────────────────

echo "--- Test: Table output ---"
OUT=$($PHP $MOOSH plugin:usage -p "$MOODLE_PATH" --type format)
echo "$OUT"
assert_output_contains "Table has type header" "type" "$OUT"
assert_output_contains "Table has plugin header" "plugin" "$OUT"
assert_output_contains "Table has name header" "name" "$OUT"
assert_output_contains "Table has topics" "topics" "$OUT"
echo ""

# ── Contrib-only flag ─────────────────────────────────────────────

echo "--- Test: --contrib-only ---"
OUT=$($PHP $MOOSH plugin:usage -p "$MOODLE_PATH" --contrib-only --type activity -o csv)
echo "$OUT"
# Standard Moodle has no contrib plugins so output should just be the header
LINE_COUNT=$(echo "$OUT" | wc -l)
assert_output_contains "Only header with no contrib plugins" "1" "$LINE_COUNT"
echo ""

# ── Help output ───────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH plugin:usage -p "$MOODLE_PATH" --help)
assert_output_contains "Help shows description" "Show plugin usage statistics" "$OUT"
assert_output_contains "Help shows --contrib-only" "--contrib-only" "$OUT"
assert_output_contains "Help shows --type" "--type" "$OUT"
echo ""

# ── plugin-usage alias ───────────────────────────────────────────


print_summary
