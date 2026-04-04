#!/usr/bin/env bash
#
# Integration tests for moosh2 sql:cli, sql:dump, sql:run commands
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_sql.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 sql:cli / sql:dump / sql:run integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Test data summary:
#   Uses existing test data — standard Moodle tables (user, course, config).
#   62 users (admin, guest, 50 students, 10 teachers).
#   17 courses total including site course.
#   sql:cli is interactive — tested non-interactively via echo pipe.
#   sql:dump uses mysqldump/pg_dump against the live database.

TMPDIR=$(mktemp -d)
trap "rm -rf $TMPDIR" EXIT

# ═══════════════════════════════════════════════════════════════════
#  sql:run (SELECT queries)
# ═══════════════════════════════════════════════════════════════════

echo "--- Test: sql:run basic SELECT (table) ---"
OUT=$($PHP $MOOSH sql:run -p "$MOODLE_PATH" "SELECT id, username FROM {user} WHERE username='admin'" 2>&1)
echo "$OUT"
assert_output_contains "Shows admin user" "admin" "$OUT"
assert_output_contains "Shows id column" "id" "$OUT"
assert_output_contains "Shows username column" "username" "$OUT"
echo ""

echo "--- Test: sql:run SELECT CSV output ---"
OUT=$($PHP $MOOSH sql:run -p "$MOODLE_PATH" "SELECT id, username FROM {user} WHERE username='admin'" -o csv 2>&1)
echo "$OUT"
assert_output_contains "CSV header" "id,username" "$OUT"
assert_output_contains "CSV has admin" "admin" "$OUT"
echo ""

echo "--- Test: sql:run SELECT JSON output ---"
OUT=$($PHP $MOOSH sql:run -p "$MOODLE_PATH" "SELECT id, username FROM {user} WHERE username='admin'" -o json 2>&1)
assert_output_contains "JSON has username" '"username": "admin"' "$OUT"
echo ""

echo "--- Test: sql:run SELECT with --limit ---"
OUT=$($PHP $MOOSH sql:run -p "$MOODLE_PATH" "SELECT id FROM {user}" --limit=3 -o csv 2>&1)
echo "$OUT"
# Header + 3 data rows = 4 lines
line_count=$(echo "$OUT" | wc -l)
if [ "$line_count" -eq 4 ]; then
    echo "  PASS: Limit returns exactly 3 rows (4 lines with header)"
    ((PASS++))
else
    echo "  FAIL: Expected 4 lines, got $line_count"
    ((FAIL++))
fi
echo ""

echo "--- Test: sql:run SELECT empty result ---"
OUT=$($PHP $MOOSH sql:run -p "$MOODLE_PATH" "SELECT id FROM {user} WHERE username='nonexistent_user_xyz'" -o csv 2>&1)
echo "$OUT"
assert_output_not_contains "No data rows for empty result" "nonexistent_user_xyz" "$OUT"
echo ""

echo "--- Test: sql:run SELECT invalid SQL ---"
OUT=$($PHP $MOOSH sql:run -p "$MOODLE_PATH" "SELECT * FROM {nonexistent_table_xyz}" 2>&1)
EXIT_CODE=$?
assert_exit_code "Invalid SQL returns failure" 1 "$EXIT_CODE"
assert_output_contains "Error message shown" "Query failed" "$OUT"
echo ""

echo "--- Test: sql:run SELECT multiple rows ---"
OUT=$($PHP $MOOSH sql:run -p "$MOODLE_PATH" "SELECT id, shortname FROM {course} WHERE id > 1" --limit=5 -o csv 2>&1)
echo "$OUT"
assert_output_contains "Multiple rows header" "id,shortname" "$OUT"
assert_output_not_empty "Multiple rows not empty" "$OUT"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  sql:run (WRITE queries — dry-run and --run)
# ═══════════════════════════════════════════════════════════════════

echo "--- Test: sql:run UPDATE dry run ---"
OUT=$($PHP $MOOSH sql:run -p "$MOODLE_PATH" "UPDATE {user} SET city='TestCity' WHERE username='admin'" 2>&1)
echo "$OUT"
assert_output_contains "Dry run message" "Dry run" "$OUT"
assert_output_contains "Shows the query" "UPDATE" "$OUT"
echo ""

# Verify no change was made
echo "--- Test: Dry run did not modify data ---"
CITY=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$u = \$DB->get_record('user', ['username' => 'admin']);
echo \$u->city;
" 2>/dev/null)
if [ "$CITY" != "TestCity" ]; then
    echo "  PASS: City was not changed by dry run (got: '$CITY')"
    ((PASS++))
else
    echo "  FAIL: City was changed to TestCity during dry run"
    ((FAIL++))
fi
echo ""

echo "--- Test: sql:run UPDATE with --run ---"
OUT=$($PHP $MOOSH sql:run -p "$MOODLE_PATH" "UPDATE {user} SET city='TestCity' WHERE username='admin'" --run 2>&1)
echo "$OUT"
assert_output_contains "Execute success message" "executed successfully" "$OUT"
echo ""

# Verify the change was made
echo "--- Test: --run did modify data ---"
CITY=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$u = \$DB->get_record('user', ['username' => 'admin']);
echo \$u->city;
" 2>/dev/null)
if [ "$CITY" = "TestCity" ]; then
    echo "  PASS: City was changed to TestCity"
    ((PASS++))
else
    echo "  FAIL: City was not changed (got: '$CITY')"
    ((FAIL++))
fi
echo ""

echo "--- Test: sql:run write query invalid SQL ---"
OUT=$($PHP $MOOSH sql:run -p "$MOODLE_PATH" "DELETE FROM {nonexistent_table_xyz}" --run 2>&1)
EXIT_CODE=$?
assert_exit_code "Invalid write SQL returns failure" 1 "$EXIT_CODE"
assert_output_contains "Write error message" "Query failed" "$OUT"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  sql:run help & alias
# ═══════════════════════════════════════════════════════════════════

echo "--- Test: sql:run help ---"
OUT=$($PHP $MOOSH sql:run -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help shows description" "Execute arbitrary SQL" "$OUT"
assert_output_contains "Help shows --limit" "--limit" "$OUT"
assert_output_contains "Help shows --run" "--run" "$OUT"
assert_output_contains "Help shows sql argument" "sql" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  sql:dump
# ═══════════════════════════════════════════════════════════════════

echo "--- Test: sql:dump to stdout ---"
OUT=$($PHP $MOOSH sql:dump -p "$MOODLE_PATH" --tables=config 2>&1 | head -20)
echo "$OUT"
assert_output_not_empty "Dump output not empty" "$OUT"
# Should contain SQL dump markers
assert_output_contains "Dump contains SQL" "dump" "$OUT"
echo ""

echo "--- Test: sql:dump to file ---"
OUT=$($PHP $MOOSH sql:dump -p "$MOODLE_PATH" --file="$TMPDIR/dump.sql" --tables=config 2>&1)
echo "$OUT"
assert_output_contains "Dump file message" "dumped to" "$OUT"
if [ -f "$TMPDIR/dump.sql" ] && [ -s "$TMPDIR/dump.sql" ]; then
    echo "  PASS: Dump file exists and is non-empty"
    ((PASS++))
else
    echo "  FAIL: Dump file missing or empty"
    ((FAIL++))
fi
echo ""

echo "--- Test: sql:dump with --gzip ---"
OUT=$($PHP $MOOSH sql:dump -p "$MOODLE_PATH" --gzip --file="$TMPDIR/dump.sql.gz" --tables=config 2>&1)
echo "$OUT"
assert_output_contains "Gzip dump message" "dumped to" "$OUT"
if [ -f "$TMPDIR/dump.sql.gz" ] && [ -s "$TMPDIR/dump.sql.gz" ]; then
    echo "  PASS: Gzip file exists and is non-empty"
    ((PASS++))
else
    echo "  FAIL: Gzip file missing or empty"
    ((FAIL++))
fi
# Verify it's actually gzipped
FILE_TYPE=$(file "$TMPDIR/dump.sql.gz" 2>/dev/null)
if echo "$FILE_TYPE" | grep -q "gzip"; then
    echo "  PASS: File is gzip compressed"
    ((PASS++))
else
    echo "  FAIL: File is not gzip compressed (type: $FILE_TYPE)"
    ((FAIL++))
fi
echo ""

echo "--- Test: sql:dump --tables specific table ---"
OUT=$($PHP $MOOSH sql:dump -p "$MOODLE_PATH" --tables=user --file="$TMPDIR/dump_user.sql" 2>&1)
# Check the dump contains user table but not other large tables
DUMP_CONTENT=$(head -50 "$TMPDIR/dump_user.sql" 2>/dev/null)
assert_output_contains "Dump references user table" "user" "$DUMP_CONTENT"
echo ""

echo "--- Test: sql:dump help ---"
OUT=$($PHP $MOOSH sql:dump -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help shows description" "Dump" "$OUT"
assert_output_contains "Help shows --file" "--file" "$OUT"
assert_output_contains "Help shows --gzip" "--gzip" "$OUT"
assert_output_contains "Help shows --tables" "--tables" "$OUT"
echo ""


# ═══════════════════════════════════════════════════════════════════
#  sql:cli
# ═══════════════════════════════════════════════════════════════════

echo "--- Test: sql:cli non-interactive query ---"
OUT=$(echo "SELECT username FROM mdl_user WHERE username='admin';" | $PHP $MOOSH sql:cli -p "$MOODLE_PATH" 2>&1)
echo "$OUT"
assert_output_contains "CLI returns admin" "admin" "$OUT"
echo ""

echo "--- Test: sql:cli help ---"
OUT=$($PHP $MOOSH sql:cli -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help shows description" "interactive database CLI" "$OUT"
echo ""


print_summary
