#!/usr/bin/env bash
#
# Common test helper functions for moosh2 integration tests.
#
# Usage: source "$(dirname "$0")/common.sh"
#

set -uo pipefail

MOOSH="$(cd "$(dirname "$0")/.." && pwd)/moosh.php"

if [ -z "${MOODLE_DIR:-}" ]; then
    echo "ERROR: MOODLE_DIR environment variable is not set."
    exit 1
fi

if [ ! -d "$MOODLE_DIR" ]; then
    echo "ERROR: Directory $MOODLE_DIR does not exist."
    exit 1
fi


if [ ! -d "$MOODLE_DIR/public" ]; then
    echo "ERROR: Directory $MOODLE_DIR/public does not exist."
    exit 1
fi

if [ ! -f "$MOODLE_DIR/public/config.php" ]; then
    echo "ERROR: File $MOODLE_DIR/public/config.php not found."
    exit 1
fi

MOODLE_PATH="$MOODLE_DIR/public"
PHP="${PHP:-/usr/bin/php}"
PASS=0
FAIL=0

assert_output_contains() {
    local description="$1"
    local expected="$2"
    local actual="$3"
    if grep -qF -- "$expected" <<< "$actual"; then
        echo "  PASS: $description"
        ((PASS++))
    else
        echo "  FAIL: $description"
        echo "    Expected to contain: $expected"
        echo "    Got: $actual"
        ((FAIL++))
    fi
}

assert_output_not_contains() {
    local description="$1"
    local expected="$2"
    local actual="$3"
    if grep -qF -- "$expected" <<< "$actual"; then
        echo "  FAIL: $description"
        echo "    Expected NOT to contain: $expected"
        echo "    Got: $actual"
        ((FAIL++))
    else
        echo "  PASS: $description"
        ((PASS++))
    fi
}

assert_output_not_empty() {
    local description="$1"
    local actual="$2"
    if [ -n "$actual" ]; then
        echo "  PASS: $description"
        ((PASS++))
    else
        echo "  FAIL: $description (output was empty)"
        ((FAIL++))
    fi
}

assert_exit_code() {
    local description="$1"
    local expected="$2"
    local actual="$3"
    if [ "$actual" -eq "$expected" ]; then
        echo "  PASS: $description"
        ((PASS++))
    else
        echo "  FAIL: $description"
        echo "    Expected exit code: $expected"
        echo "    Got: $actual"
        ((FAIL++))
    fi
}

print_summary() {
    echo ""
    echo "================================"
    echo "Results: $PASS passed, $FAIL failed"
    echo "================================"

    if [ "$FAIL" -gt 0 ]; then
        exit 1
    fi
}
