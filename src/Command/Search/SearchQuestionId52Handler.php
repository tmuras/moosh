<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Search;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * search:questionid implementation for Moodle 5.1.
 */
class SearchQuestionId52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument('questionid', InputArgument::REQUIRED, 'Question ID to search for');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $questionId = (int) $input->getArgument('questionid');

        $headers = ['source', 'table', 'id', 'column', 'detail'];
        $rows = [];

        // --- Check the question itself exists ---
        $verbose->step('Looking up question record');
        $question = $DB->get_record('question', ['id' => $questionId]);
        if ($question) {
            $rows[] = ['question', 'question', $question->id, 'id', "name=\"{$question->name}\", qtype={$question->qtype}"];
        } else {
            $verbose->info("Question ID $questionId not found in question table");
        }

        // --- Child questions (parent = questionid) ---
        $verbose->step('Searching for child questions');
        $children = $DB->get_records('question', ['parent' => $questionId]);
        foreach ($children as $child) {
            if ((int) $child->id === $questionId) {
                continue; // Skip self-reference.
            }
            $rows[] = ['child', 'question', $child->id, 'parent', "name=\"{$child->name}\", qtype={$child->qtype}"];
        }

        // --- Question versions ---
        $verbose->step('Searching question_versions');
        $versions = $DB->get_records('question_versions', ['questionid' => $questionId]);
        foreach ($versions as $v) {
            $rows[] = ['version', 'question_versions', $v->id, 'questionid', "bankentryid={$v->questionbankentryid}, version={$v->version}, status={$v->status}"];
        }

        // --- Question bank entries (via versions) ---
        if (!empty($versions)) {
            $verbose->step('Searching question_bank_entries');
            $bankEntryIds = array_unique(array_map(fn($v) => (int) $v->questionbankentryid, $versions));
            foreach ($bankEntryIds as $beId) {
                $be = $DB->get_record('question_bank_entries', ['id' => $beId]);
                if ($be) {
                    $cat = $DB->get_record('question_categories', ['id' => $be->questioncategoryid], 'name');
                    $catName = $cat ? $cat->name : "catid={$be->questioncategoryid}";
                    $rows[] = ['bankentry', 'question_bank_entries', $be->id, 'id', "category=\"$catName\", idnumber=\"{$be->idnumber}\""];

                    // Question references pointing to this bank entry.
                    $refs = $DB->get_records('question_references', ['questionbankentryid' => $be->id]);
                    foreach ($refs as $ref) {
                        $rows[] = ['reference', 'question_references', $ref->id, 'questionbankentryid',
                            "component={$ref->component}, area={$ref->questionarea}, itemid={$ref->itemid}"];
                    }
                }
            }
        }

        // --- Files associated with this question ---
        $verbose->step('Searching files');
        $files = $DB->get_records_select('files',
            "component = 'question' AND itemid = ? AND filename <> '.'",
            [$questionId],
        );
        foreach ($files as $file) {
            $rows[] = ['file', 'files', $file->id, 'itemid', "area={$file->filearea}, name=\"{$file->filename}\", size={$file->filesize}"];
        }

        // --- Schema scan: all tables with 'questionid' column ---
        $verbose->step('Scanning schema for questionid columns');
        $manager = $DB->get_manager();
        $schema = $manager->get_install_xml_schema();
        $tables = $schema->getTables();

        foreach ($tables as $table) {
            $tableName = $table->getName();
            // Skip tables already checked explicitly.
            if (in_array($tableName, ['question', 'question_versions', 'question_bank_entries', 'question_references', 'files'], true)) {
                continue;
            }

            $hasIdCol = $table->getField('id') !== null;
            foreach ($table->getFields() as $column) {
                if ($column->getType() !== XMLDB_TYPE_INTEGER) {
                    continue;
                }
                if ($column->getName() !== 'questionid') {
                    continue;
                }

                try {
                    if ($hasIdCol) {
                        $records = $DB->get_records($tableName, ['questionid' => $questionId], '', 'id', 0, 100);
                    } else {
                        $records = $DB->get_records($tableName, ['questionid' => $questionId], '', '*', 0, 100);
                    }
                } catch (\Throwable $e) {
                    continue;
                }

                foreach ($records as $record) {
                    $recordId = $hasIdCol ? $record->id : '-';
                    $rows[] = ['schema', $tableName, $recordId, 'questionid', ''];
                }
            }
        }

        $verbose->done('Found ' . count($rows) . ' reference(s)');

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
