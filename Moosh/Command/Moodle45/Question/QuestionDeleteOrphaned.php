<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle45\Question;

use Moosh\MooshCommand;

class QuestionDeleteOrphaned extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('deleteOrphaned', 'question');
        $this->addOption('check-only', 'Only check if orphaned questions exist, do not delete');
    }

    /**
     * Execute the command to find and delete orphaned questions.
     *
     * @throws \Throwable
     */
    public function execute(): void
    {
        global $CFG;
        $checkonly = !empty($this->expandedOptions['check-only']);

        require_once($CFG->dirroot . '/question/engine/lib.php');
        require_once($CFG->dirroot . '/question/type/questiontypebase.php');

        $questions = $this->find_orphaned_questions();

        if (!empty($questions)) {
            mtrace('Found ' . count($questions) . ' orphaned questions.');
            $this->show_question_data($questions);

            if ($checkonly) {
                mtrace('The check-only option is enabled. No questions will be deleted. Exiting.');
                return;
            } else {
                $choice = cli_input(
                    "⚠️ ⚠️ ⚠️  DANGER ⚠️ ⚠️ ⚠️\n"
                    . "Running this command will modify your database. All questions with status 'SAFE_TO_DELETE' will be deleted.\n"
                    . "Make sure you backup your database first!\n\n"
                    . "Are you sure you want to proceed? (y/n)",
                    'n',
                    ['y', 'n']
                );
                if ($choice === 'y') {
                    mtrace('Deleting questions...');
                    $this->delete_questions($questions);

                    return;
                }
                mtrace('No questions will be deleted. Exiting.');
                return;
            }
        } else {
            mtrace('No orphaned questions found. Exiting.');
            return;
        }

    }

    /**
     * Find orphaned questions.
     *
     * @return array
     * @throws \Throwable
     */
    private function find_orphaned_questions(): array {
        global $DB;

        $sql = "SELECT
    q.id as questionid,
    q.name as questionname,
    q.qtype,
    FROM_UNIXTIME(q.timecreated) as created,
    FROM_UNIXTIME(q.timemodified) as modified,
    CASE
        WHEN q.qtype = 'ddimageortext' THEN
            CASE WHEN EXISTS(SELECT 1 FROM {qtype_ddimageortext} WHERE questionid = q.id)
                 THEN 'EXISTS' ELSE 'MISSING' END
        WHEN q.qtype = 'gapselect' THEN
            CASE WHEN EXISTS(SELECT 1 FROM {question_gapselect} WHERE questionid = q.id)
                 THEN 'EXISTS' ELSE 'MISSING' END
        WHEN q.qtype = 'ddmarker' THEN
            CASE WHEN EXISTS(SELECT 1 FROM {qtype_ddmarker} WHERE questionid = q.id)
                 THEN 'EXISTS' ELSE 'MISSING' END
        WHEN q.qtype = 'ddwtos' THEN
            CASE WHEN EXISTS(SELECT 1 FROM {question_ddwtos} WHERE questionid = q.id)
                 THEN 'EXISTS' ELSE 'MISSING' END
        WHEN q.qtype = 'essay' THEN
            CASE WHEN EXISTS(SELECT 1 FROM {qtype_essay_options} WHERE questionid = q.id)
                 THEN 'EXISTS' ELSE 'MISSING' END
        ELSE 'UNKNOWN_TYPE'
    END as qtype_record_status,

    (SELECT COUNT(DISTINCT qz.id)
     FROM {quiz_slots} slot
     JOIN {quiz} qz ON qz.id = slot.quizid
     JOIN {question_references} qr ON qr.itemid = slot.id
     JOIN {question_bank_entries} qbe2 ON qbe2.id = qr.questionbankentryid
     JOIN {question_versions} qv2 ON qv2.questionbankentryid = qbe2.id
     WHERE qv2.questionbankentryid = qbe.id
       AND qr.component = 'mod_quiz'
       AND qr.questionarea = 'slot') as usage_quiz_slots,

    (SELECT COUNT(DISTINCT qz.id)
     FROM {quiz} qz
     JOIN {quiz_attempts} qa ON qa.quiz = qz.id
     JOIN {question_usages} qu ON qu.id = qa.uniqueid
     JOIN {question_attempts} qatt ON qatt.questionusageid = qu.id
     JOIN {question_versions} qv2 ON qv2.questionid = qatt.questionid
     JOIN {question_versions} qv3 ON qv2.questionbankentryid = qv3.questionbankentryid
     WHERE qa.preview = 0
       AND qv3.questionbankentryid = qbe.id) as usage_quiz_attempts,

    (SELECT COUNT(DISTINCT quizid) FROM (
        SELECT qz.id as quizid
        FROM {quiz_slots} slot
        JOIN {quiz} qz ON qz.id = slot.quizid
        JOIN {question_references} qr ON qr.itemid = slot.id
        JOIN {question_bank_entries} qbe2 ON qbe2.id = qr.questionbankentryid
        JOIN {question_versions} qv2 ON qv2.questionbankentryid = qbe2.id
        WHERE qv2.questionbankentryid = qbe.id
          AND qr.component = 'mod_quiz'
          AND qr.questionarea = 'slot'

        UNION

        SELECT qz.id as quizid
        FROM {quiz} qz
        JOIN {quiz_attempts} qa ON qa.quiz = qz.id
        JOIN {question_usages} qu ON qu.id = qa.uniqueid
        JOIN {question_attempts} qatt ON qatt.questionusageid = qu.id
        JOIN {question_versions} qv2 ON qv2.questionid = qatt.questionid
        JOIN {question_versions} qv3 ON qv2.questionbankentryid = qv3.questionbankentryid
        WHERE qa.preview = 0
          AND qv3.questionbankentryid = qbe.id
    ) as usage_combined) as usage_count,

    qc.id as categoryid,
    qc.name as categoryname,
    CASE ctx.contextlevel
        WHEN 50 THEN CONCAT('Course: ', (SELECT shortname FROM {course} WHERE id = ctx.instanceid))
        WHEN 40 THEN CONCAT('Category: ', (SELECT name FROM {course_categories} WHERE id = ctx.instanceid))
        WHEN 10 THEN 'System'
        ELSE 'Other'
    END as location,
    CASE ctx.contextlevel
        WHEN 50 THEN ctx.instanceid
        ELSE NULL
    END as courseid,

    CASE
        WHEN (SELECT COUNT(DISTINCT quizid) FROM (
            SELECT qz.id as quizid
            FROM {quiz_slots} slot
            JOIN {quiz} qz ON qz.id = slot.quizid
            JOIN {question_references} qr ON qr.itemid = slot.id
            JOIN {question_bank_entries} qbe2 ON qbe2.id = qr.questionbankentryid
            JOIN {question_versions} qv2 ON qv2.questionbankentryid = qbe2.id
            WHERE qv2.questionbankentryid = qbe.id
              AND qr.component = 'mod_quiz'
              AND qr.questionarea = 'slot'
            UNION
            SELECT qz.id as quizid
            FROM {quiz} qz
            JOIN {quiz_attempts} qa ON qa.quiz = qz.id
            JOIN {question_usages} qu ON qu.id = qa.uniqueid
            JOIN {question_attempts} qatt ON qatt.questionusageid = qu.id
            JOIN {question_versions} qv2 ON qv2.questionid = qatt.questionid
            JOIN {question_versions} qv3 ON qv2.questionbankentryid = qv3.questionbankentryid
            WHERE qa.preview = 0
              AND qv3.questionbankentryid = qbe.id
        ) as usage_combined) = 0 THEN 'SAFE_TO_DELETE'
        ELSE 'IN_USE'
    END as deletion_status

FROM {question} q
JOIN {question_versions} qv ON qv.questionid = q.id
JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
JOIN {context} ctx ON ctx.id = qc.contextid
WHERE
    q.qtype IN ('ddimageortext', 'gapselect', 'ddmarker', 'ddwtos', 'essay')
    AND (
        (q.qtype = 'ddimageortext' AND NOT EXISTS(SELECT 1 FROM {qtype_ddimageortext} WHERE questionid = q.id))
        OR (q.qtype = 'gapselect' AND NOT EXISTS(SELECT 1 FROM {question_gapselect} WHERE questionid = q.id))
        OR (q.qtype = 'ddmarker' AND NOT EXISTS(SELECT 1 FROM {qtype_ddmarker} WHERE questionid = q.id))
        OR (q.qtype = 'ddwtos' AND NOT EXISTS(SELECT 1 FROM {question_ddwtos} WHERE questionid = q.id))
        OR (q.qtype = 'essay' AND NOT EXISTS(SELECT 1 FROM {qtype_essay_options} WHERE questionid = q.id))
    )
    AND (SELECT COUNT(DISTINCT quizid) FROM (
        SELECT qz.id as quizid
        FROM {quiz_slots} slot
        JOIN {quiz} qz ON qz.id = slot.quizid
        JOIN {question_references} qr ON qr.itemid = slot.id
        JOIN {question_bank_entries} qbe2 ON qbe2.id = qr.questionbankentryid
        JOIN {question_versions} qv2 ON qv2.questionbankentryid = qbe2.id
        WHERE qv2.questionbankentryid = qbe.id
          AND qr.component = 'mod_quiz'
          AND qr.questionarea = 'slot'
        UNION
        SELECT qz.id as quizid
        FROM {quiz} qz
        JOIN {quiz_attempts} qa ON qa.quiz = qz.id
        JOIN {question_usages} qu ON qu.id = qa.uniqueid
        JOIN {question_attempts} qatt ON qatt.questionusageid = qu.id
        JOIN {question_versions} qv2 ON qv2.questionid = qatt.questionid
        JOIN {question_versions} qv3 ON qv2.questionbankentryid = qv3.questionbankentryid
        WHERE qa.preview = 0
          AND qv3.questionbankentryid = qbe.id
    ) as usage_combined) = 0
ORDER BY q.qtype, q.id;";

        try {
            $questions = $DB->get_records_sql($sql);
        } catch (\Throwable $e) {
            cli_error('Error while executing SQL in QuestionDeleteOrphaned::find_orphaned_questions(): ' . $e->getMessage());
        }

        return $questions;
    }

    private function show_question_data(array $questions): void {
        $headerMap = [
            'Question ID' => 'questionid',
            'Question Name' => 'questionname',
            'Question Type' => 'qtype',
            'Created' => 'created',
            'Modified' => 'modified',
            'Question Type Status' => 'qtype_record_status',
            'Quiz Usage' => 'usage_quiz_slots',
            'Quiz Attempts' => 'usage_quiz_attempts',
            'Usage Count' => 'usage_count',
            'Category ID' => 'categoryid',
            'Category Name' => 'categoryname',
            'Location' => 'location',
            'Course ID' => 'courseid',
            'Deletion Status' => 'deletion_status'
        ];
        
        $headers = array_keys($headerMap);
        $separator = ' | ';
        $columnWidth = 20;
        
        // Print headers once
        $headerRow = [];
        foreach ($headers as $header) {
            $headerRow[] = str_pad($header, $columnWidth, ' ', STR_PAD_RIGHT);
        }
        mtrace(implode($separator, $headerRow) . "\n");
        
        // Print separator line
        $separatorRow = [];
        foreach ($headers as $header) {
            $separatorRow[] = str_repeat('-', $columnWidth);
        }
        mtrace(implode($separator, $separatorRow) . "\n");
        
        // Print data rows
        foreach ($questions as $question) {
            $dataRow = [];
            foreach ($headers as $header) {
                $fieldName = $headerMap[$header];
                $value = isset($question->$fieldName) ? (string)$question->$fieldName : '';
                $dataRow[] = str_pad($value, $columnWidth, ' ', STR_PAD_RIGHT);
            }
            mtrace(implode($separator, $dataRow) . "\n");
        }
        mtrace("\n");
    }

    /**
     * Delete orphaned questions.
     *
     * @param array $questions
     * @throws \Throwable
     */
    private function delete_questions(array $questions): void {
        global $CFG;
        require_once($CFG->dirroot . '/lib/questionlib.php');
        $notDeletedQuestionIds = array();
        $skippedQuestionIds = array();

        foreach ($questions as $question) {
            try {
                if ($question->deletion_status == 'SAFE_TO_DELETE') {
                    question_delete_question($question->questionid);
                    mtrace('Question with ID ' . $question->questionid . ' deleted successfully.');
                } else {
                    mtrace('Question with ID ' . $question->questionid . ' is not safe to delete. Skipping.');
                    $skippedQuestionIds[] = $question->questionid;
                }
            } catch (\Throwable $e) {
                $notDeletedQuestionIds[] = $question->questionid;
                mtrace('Error while deleting question ' . $question->questionid . ': ' . $e->getMessage());
            }
        }

        mtrace("\n\nDeletion process summary:");

        if (!empty($skippedQuestionIds)) {
            mtrace('Some questions were not safe to delete. Skipped question IDs: ' . implode(', ', $skippedQuestionIds));
        }

        if (empty($notDeletedQuestionIds)) {
            mtrace('Questions deleted successfully.' . PHP_EOL);
        } else {
            mtrace('Some questions were not deleted successfully. Not deleted question IDs: ' . implode(', ', $notDeletedQuestionIds) . PHP_EOL);
        }
    }

    /**
     * Get help text for the command arguments.
     *
     * @return string Help text for the command arguments.
     */
    protected function getArgumentsHelp(): string {
        $help = parent::getArgumentsHelp();
        $help .= "\n\n";
        $help .= "This command deletes orphaned questions if they are not in use. Supports qtype_ddimageortext, qtype_gapselect, qtype_ddmarker, qtype_ddwtos and qtype_essay currently.\n";
        $help .= "By orphaned, we mean questions that are not used in any quiz or activity and have no qtype record in the database.\n";
        $help .= "It was created to fix the error \"Can't find data record in database table qtype_table\".\n";
        $help .= "--check-only option - only check if orphaned questions exist, do not delete them.\n";
        return $help;
    }

}
