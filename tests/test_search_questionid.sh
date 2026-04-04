#!/usr/bin/env bash
#
# Integration test for moosh2 search:questionid command
# Requires a working Moodle 5.2 installation at /var/www/html/moodle52
#
# Usage: bash tests/test_search_questionid.sh
#

source "$(dirname "$0")/common.sh"

echo "=== moosh2 search:questionid integration tests ==="
echo "Moodle path: $MOODLE_PATH"
echo "moosh path:  $MOOSH"
echo ""

echo "--- Resetting Moodle to known state ---"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
bash "$SCRIPT_DIR/clear.sh"
echo ""

# Create a test question
echo "--- Creating test question ---"
QID=$($PHP -r "
define('CLI_SCRIPT', true);
require('$MOODLE_PATH/config.php');
global \$DB;
\$ctx = context_course::instance(2);
\$cat = new stdClass();
\$cat->name = 'Search Test Cat';
\$cat->contextid = \$ctx->id;
\$cat->info = '';
\$cat->infoformat = 0;
\$cat->stamp = 'test_' . time();
\$cat->parent = 0;
\$cat->sortorder = 1;
\$cat->id = \$DB->insert_record('question_categories', \$cat);
\$q = new stdClass();
\$q->parent = 0;
\$q->name = 'SearchTest Question';
\$q->questiontext = 'Is this a test?';
\$q->questiontextformat = 1;
\$q->generalfeedback = '';
\$q->generalfeedbackformat = 1;
\$q->defaultmark = 1;
\$q->penalty = 1;
\$q->qtype = 'truefalse';
\$q->length = 1;
\$q->stamp = 'q_' . time();
\$q->timecreated = time();
\$q->timemodified = time();
\$q->createdby = 2;
\$q->modifiedby = 2;
\$q->id = \$DB->insert_record('question', \$q);
\$be = new stdClass();
\$be->questioncategoryid = \$cat->id;
\$be->idnumber = 'TESTQ1';
\$be->ownerid = 2;
\$be->id = \$DB->insert_record('question_bank_entries', \$be);
\$qv = new stdClass();
\$qv->questionbankentryid = \$be->id;
\$qv->version = 1;
\$qv->questionid = \$q->id;
\$qv->status = 'ready';
\$DB->insert_record('question_versions', \$qv);
echo \$q->id;
" 2>/dev/null)
echo "Created question ID: $QID"
echo ""

# ── CSV output ────────────────────────────────────────────────────

echo "--- Test: CSV output ---"
OUT=$($PHP $MOOSH search:questionid -p "$MOODLE_PATH" $QID -o csv)
echo "$OUT"
assert_output_contains "Header" "source,table,id,column,detail" "$OUT"
assert_output_contains "Found question record" "question,question," "$OUT"
assert_output_contains "Shows question name" "SearchTest Question" "$OUT"
assert_output_contains "Shows qtype" "truefalse" "$OUT"
assert_output_contains "Found version" "version,question_versions," "$OUT"
assert_output_contains "Found bank entry" "bankentry,question_bank_entries," "$OUT"
assert_output_contains "Shows category" "Search Test Cat" "$OUT"
echo ""

# ── JSON output ───────────────────────────────────────────────────

echo "--- Test: JSON output ---"
OUT=$($PHP $MOOSH search:questionid -p "$MOODLE_PATH" $QID -o json)
echo "$OUT" | head -10
assert_output_contains "JSON has source" '"source"' "$OUT"
assert_output_contains "JSON has table" '"table"' "$OUT"
assert_output_contains "JSON has question" '"question"' "$OUT"
echo ""

# ── Table output ──────────────────────────────────────────────────

echo "--- Test: Table output ---"
OUT=$($PHP $MOOSH search:questionid -p "$MOODLE_PATH" $QID)
echo "$OUT"
assert_output_contains "Table has source" "source" "$OUT"
assert_output_contains "Table has SearchTest" "SearchTest" "$OUT"
echo ""

# ── Nonexistent question ──────────────────────────────────────────

echo "--- Test: Nonexistent question ---"
OUT=$($PHP $MOOSH search:questionid -p "$MOODLE_PATH" 99999 -o csv)
LINE_COUNT=$(echo "$OUT" | wc -l)
assert_output_contains "Only header for nonexistent" "1" "$LINE_COUNT"
echo ""

# ── Bank entry idnumber ───────────────────────────────────────────

echo "--- Test: Shows bank entry idnumber ---"
OUT=$($PHP $MOOSH search:questionid -p "$MOODLE_PATH" $QID -o csv)
assert_output_contains "Shows idnumber" "TESTQ1" "$OUT"
echo ""

# ── Help output ───────────────────────────────────────────────────

echo "--- Test: Help output ---"
OUT=$($PHP $MOOSH search:questionid -p "$MOODLE_PATH" --help)
assert_output_contains "Help description" "Search for a question ID" "$OUT"
assert_output_contains "Help shows questionid arg" "questionid" "$OUT"
echo ""

# ── Alias ─────────────────────────────────────────────────────────


print_summary
