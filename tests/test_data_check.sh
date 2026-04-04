#!/usr/bin/env bash
#
# Integration test for moosh2 data:check command
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_data_check.sh
#

source "$(dirname "$0")/common.sh"

MOODLE_BASENAME="$(basename "${MOODLE_DIR:-/var/www/html/moodle52}")"
DATAROOT="/opt/data/$MOODLE_BASENAME"

echo "=== moosh2 data:check integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# ── Checksum check (clean state) ─────────────────────────────────

echo "--- Test: Checksum check (clean) ---"
OUT=$($PHP $MOOSH data:check -p "$MOODLE_PATH" checksum -o csv)
echo "$OUT"
assert_output_contains "Header row" "check,status,path,detail" "$OUT"
assert_output_contains "Checksum OK" "checksum,OK" "$OUT"
echo ""

# ── Writable check (clean state) ─────────────────────────────────

echo "--- Test: Writable check (clean) ---"
OUT=$($PHP $MOOSH data:check -p "$MOODLE_PATH" writable -o csv)
echo "$OUT"
assert_output_contains "Writable OK" "writable,OK" "$OUT"
echo ""

# ── DB-to-disk check (clean state) ───────────────────────────────

echo "--- Test: DB-to-disk check (clean) ---"
OUT=$($PHP $MOOSH data:check -p "$MOODLE_PATH" db-to-disk -o csv)
echo "$OUT"
assert_output_contains "DB-to-disk OK" "db-to-disk,OK" "$OUT"
echo ""

# ── Disk-to-DB check (clean state) ───────────────────────────────

echo "--- Test: Disk-to-DB check (clean) ---"
OUT=$($PHP $MOOSH data:check -p "$MOODLE_PATH" disk-to-db -o csv)
echo "$OUT"
assert_output_contains "Disk-to-DB OK" "disk-to-db,OK" "$OUT"
echo ""

# ── All checks (clean state) ─────────────────────────────────────

echo "--- Test: All checks (clean) ---"
OUT=$($PHP $MOOSH data:check -p "$MOODLE_PATH" all -o csv)
echo "$OUT"
assert_output_contains "Has checksum OK" "checksum,OK" "$OUT"
assert_output_contains "Has writable OK" "writable,OK" "$OUT"
assert_output_contains "Has db-to-disk OK" "db-to-disk,OK" "$OUT"
assert_output_contains "Has disk-to-db OK" "disk-to-db,OK" "$OUT"
EXIT_CODE=$?
assert_exit_code "Exit code 0 when all OK" 0 "$EXIT_CODE"
echo ""

# ── Default argument is 'all' ────────────────────────────────────

echo "--- Test: Default is 'all' ---"
OUT=$($PHP $MOOSH data:check -p "$MOODLE_PATH" -o csv)
assert_output_contains "Default has checksum" "checksum" "$OUT"
assert_output_contains "Default has writable" "writable" "$OUT"
assert_output_contains "Default has db-to-disk" "db-to-disk" "$OUT"
assert_output_contains "Default has disk-to-db" "disk-to-db" "$OUT"
echo ""

# ── JSON output ───────────────────────────────────────────────────

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH data:check -p "$MOODLE_PATH" checksum -o json)
echo "$OUT"
assert_output_contains "JSON has check key" '"check"' "$OUT"
assert_output_contains "JSON has status key" '"status"' "$OUT"
assert_output_contains "JSON has OK" '"OK"' "$OUT"
echo ""

# ── Create corrupted file to test checksum detection ──────────────

echo "--- Test: Detect corrupted file ---"
# Find a real file in filedir
REAL_FILE=$(find "$DATAROOT/filedir" -type f ! -name warning.txt | head -1)
if [ -n "$REAL_FILE" ]; then
    # Corrupt it by appending data
    ORIG_CONTENT=$(cat "$REAL_FILE")
    echo "CORRUPTED" >> "$REAL_FILE"
    OUT=$($PHP $MOOSH data:check -p "$MOODLE_PATH" checksum -o csv)
    echo "$OUT"
    assert_output_contains "Detects corrupted file" "checksum,FAIL" "$OUT"
    assert_output_contains "Shows SHA1 mismatch" "SHA1 does not match" "$OUT"
    # Restore original
    printf '%s' "$ORIG_CONTENT" > "$REAL_FILE"
else
    echo "  SKIP: No files in filedir to corrupt"
fi
echo ""

# ── Create orphan file to test disk-to-db detection ───────────────

echo "--- Test: Detect orphan file on disk ---"
ORPHAN_DIR="$DATAROOT/filedir/zz/zz"
mkdir -p "$ORPHAN_DIR"
echo "orphan" > "$ORPHAN_DIR/zzorphanfile1234567890abcdef12345678"
OUT=$($PHP $MOOSH data:check -p "$MOODLE_PATH" disk-to-db -o csv)
echo "$OUT"
assert_output_contains "Detects orphan file" "disk-to-db,FAIL" "$OUT"
assert_output_contains "Shows not in DB detail" "not in DB" "$OUT"
# Cleanup
rm -rf "$DATAROOT/filedir/zz"
echo ""

# ── Limit option ──────────────────────────────────────────────────

echo "--- Test: --limit option ---"
# Create multiple orphans
mkdir -p "$DATAROOT/filedir/yy/yy"
for i in 1 2 3 4 5; do
    echo "orphan$i" > "$DATAROOT/filedir/yy/yy/yyorphan${i}abcdefabcdefabcdef1234"
done
OUT=$($PHP $MOOSH data:check -p "$MOODLE_PATH" disk-to-db --limit 2 -o csv)
FAIL_COUNT=$(echo "$OUT" | grep -c "FAIL" || true)
if [ "$FAIL_COUNT" -le 2 ]; then
    echo "  PASS: Limit respected ($FAIL_COUNT issues reported)"
    ((PASS++))
else
    echo "  FAIL: Limit not respected ($FAIL_COUNT issues reported, expected <= 2)"
    ((FAIL++))
fi
# Cleanup
rm -rf "$DATAROOT/filedir/yy"
echo ""

# ── Exit code on failure ──────────────────────────────────────────

echo "--- Test: Exit code on failure ---"
mkdir -p "$DATAROOT/filedir/xx/xx"
echo "orphan" > "$DATAROOT/filedir/xx/xx/xxorphanfiletest1234567890abcde"
$PHP $MOOSH data:check -p "$MOODLE_PATH" disk-to-db -o csv > /dev/null 2>&1
EXIT_CODE=$?
assert_exit_code "Exit code 1 when issues found" 1 "$EXIT_CODE"
rm -rf "$DATAROOT/filedir/xx"
echo ""

# ── Invalid check name ────────────────────────────────────────────

echo "--- Test: Invalid check name ---"
OUT=$($PHP $MOOSH data:check -p "$MOODLE_PATH" nonexistent 2>&1)
EXIT_CODE=$?
assert_exit_code "Exit code 1 for invalid check" 1 "$EXIT_CODE"
assert_output_contains "Unknown check error" "Unknown check" "$OUT"
echo ""

# ── Help output ───────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH data:check -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Run data integrity checks" "$OUT"
assert_output_contains "Help shows checksum" "checksum" "$OUT"
assert_output_contains "Help shows writable" "writable" "$OUT"
assert_output_contains "Help shows db-to-disk" "db-to-disk" "$OUT"
assert_output_contains "Help shows disk-to-db" "disk-to-db" "$OUT"
assert_output_contains "Help shows --limit" "--limit" "$OUT"
echo ""

# ── data-check alias ──────────────────────────────────────────────


print_summary
