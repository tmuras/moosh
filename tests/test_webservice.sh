#!/usr/bin/env bash
#
# Integration tests for moosh2 webservice commands:
#   webservice:call
# Requires a working Moodle 5.1 installation at /var/www/html/moodle51
#
# Usage: bash tests/test_webservice.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 webservice commands integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

# Step 1: Reset Moodle to known state
echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Step 2: Enable webservices and create a token via SQL
echo "--- Setting up webservice token ---"

# Enable webservices
$PHP $MOOSH config:set enablewebservices 1 -p "$MOODLE_PATH" --run > /dev/null 2>&1

# Enable REST protocol
$PHP $MOOSH config:set webserviceprotocols rest -p "$MOODLE_PATH" --run > /dev/null 2>&1

# Create an external service
$PHP $MOOSH sql:run -p "$MOODLE_PATH" --run "INSERT INTO mdl_external_services (name, enabled, requiredcapability, restrictedusers, component, shortname, downloadfiles, uploadfiles, timecreated, timemodified) VALUES ('Moosh Test Service', 1, NULL, 0, '', 'moosh_test', 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())" > /dev/null 2>&1

# Get the service ID
SVC_ID=$($PHP $MOOSH sql:select -p "$MOODLE_PATH" "SELECT id FROM mdl_external_services WHERE shortname='moosh_test'" -o csv 2>&1 | tail -1)
echo "  Service ID: $SVC_ID"

# Add core_webservice_get_site_info function to the service
$PHP $MOOSH sql:run -p "$MOODLE_PATH" --run "INSERT INTO mdl_external_services_functions (externalserviceid, functionname) VALUES ($SVC_ID, 'core_webservice_get_site_info')" > /dev/null 2>&1

# Add core_course_get_courses function
$PHP $MOOSH sql:run -p "$MOODLE_PATH" --run "INSERT INTO mdl_external_services_functions (externalserviceid, functionname) VALUES ($SVC_ID, 'core_course_get_courses')" > /dev/null 2>&1

# Get admin user ID
ADMIN_ID=$($PHP $MOOSH sql:select -p "$MOODLE_PATH" "SELECT id FROM mdl_user WHERE username='admin'" -o csv 2>&1 | tail -1)
echo "  Admin ID: $ADMIN_ID"

# Create a token
TOKEN="moosh2testtoken00000000000000aa"
$PHP $MOOSH sql:run -p "$MOODLE_PATH" --run "INSERT INTO mdl_external_tokens (token, tokentype, userid, externalserviceid, contextid, creatorid, iprestriction, validuntil, timecreated, lastaccess, privatetoken) VALUES ('$TOKEN', 0, $ADMIN_ID, $SVC_ID, 1, $ADMIN_ID, '', 0, UNIX_TIMESTAMP(), 0, '')" > /dev/null 2>&1

echo "  Token: $TOKEN"
echo ""

# ═══════════════════════════════════════════════════════════════════
#  webservice:call
# ═══════════════════════════════════════════════════════════════════

echo "========== webservice:call =========="
echo ""

echo "--- Test: Get site info (JSON) ---"
OUT=$($PHP $MOOSH webservice:call core_webservice_get_site_info --token "$TOKEN" -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Site info exit code 0" 0 $EC
assert_output_contains "Shows sitename" "sitename" "$OUT"
assert_output_contains "Shows username" "admin" "$OUT"
echo ""

echo "--- Test: Pretty-print JSON ---"
OUT=$($PHP $MOOSH webservice:call core_webservice_get_site_info --token "$TOKEN" --pretty -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Pretty exit code 0" 0 $EC
assert_output_contains "Pretty has sitename" "sitename" "$OUT"
# Pretty-printed JSON has indentation
assert_output_contains "Has indentation" "    " "$OUT"
echo ""

echo "--- Test: XML format ---"
OUT=$($PHP $MOOSH webservice:call core_webservice_get_site_info --token "$TOKEN" --format xml -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "XML exit code 0" 0 $EC
assert_output_contains "XML has RESPONSE tag" "RESPONSE" "$OUT"
assert_output_contains "XML has sitename" "sitename" "$OUT"
echo ""

echo "--- Test: POST method ---"
OUT=$($PHP $MOOSH webservice:call core_webservice_get_site_info --token "$TOKEN" --post -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "POST exit code 0" 0 $EC
assert_output_contains "POST has sitename" "sitename" "$OUT"
echo ""

echo "--- Test: With parameters ---"
OUT=$($PHP $MOOSH webservice:call core_course_get_courses --token "$TOKEN" --pretty "options[ids][0]=2" -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Params exit code 0" 0 $EC
assert_output_contains "Shows course data" "fullname" "$OUT"
echo ""

echo "--- Test: Invalid token ---"
OUT=$($PHP $MOOSH webservice:call core_webservice_get_site_info --token invalidtoken -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid token" 1 $EC
assert_output_contains "Error for invalid token" "exception" "$OUT"
echo ""

echo "--- Test: Missing token ---"
OUT=$($PHP $MOOSH webservice:call core_webservice_get_site_info -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for missing token" 1 $EC
assert_output_contains "Error for missing token" "token is required" "$OUT"
echo ""

echo "--- Test: Invalid format ---"
OUT=$($PHP $MOOSH webservice:call core_webservice_get_site_info --token "$TOKEN" --format yaml -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Exit code 1 for invalid format" 1 $EC
assert_output_contains "Error for invalid format" "Invalid format" "$OUT"
echo ""

echo "--- Test: Raw params ---"
OUT=$($PHP $MOOSH webservice:call core_course_get_courses --token "$TOKEN" --raw-params "options[ids][0]=2" --pretty -p "$MOODLE_PATH" 2>&1)
EC=$?
assert_exit_code "Raw params exit code 0" 0 $EC
assert_output_contains "Raw params shows course" "fullname" "$OUT"
echo ""

echo "--- Test: webservice:call help ---"
OUT=$($PHP $MOOSH webservice:call -p "$MOODLE_PATH" --help 2>&1)
assert_output_contains "Help description" "Call a Moodle webservice function" "$OUT"
assert_output_contains "Help shows --token" "--token" "$OUT"
assert_output_contains "Help shows --post" "--post" "$OUT"
assert_output_contains "Help shows --pretty" "--pretty" "$OUT"
assert_output_contains "Help shows --format" "--format" "$OUT"
echo ""


print_summary
