<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright 2025 Devlion {@link http://devlion.co.il}
 * * @author 2025 Kiril Goldenshteyn <kiril@devlion.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle41\Question;

use Moosh\MooshCommand;

class QuestionClean extends MooshCommand {
    public function __construct() {
        parent::__construct('clean', 'question');
        $this->addOption('c|course:', 'Specifies the course ID to remove all questions and versions grouped by name and category ');
        $this->addOption('check-only',
                'Checks only whether questions with duplicate names exist in the course, grouped by both name and category');
    }
    public function execute() {
        global $DB, $CFG;
        $courseid = $this->expandedOptions['course'];
        $checkonly = $this->expandedOptions['check-only'];
        require_once($CFG->libdir . '/questionlib.php');
        $course = get_course($courseid);
        $sql = "SELECT q.id,
                    qc.id AS categoryid,
                    q.name AS questionname,
                    q.qtype AS questiontype,
                    GROUP_CONCAT(q.id ORDER BY q.id) AS questionids
                FROM
                    {course} AS c
                    LEFT JOIN {context} AS ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50
                    LEFT JOIN {question_categories} AS qc ON qc.contextid = ctx.id
                    LEFT JOIN {question_bank_entries} AS qbe ON qbe.questioncategoryid = qc.id
                    LEFT JOIN {question_versions} AS qv ON qv.questionbankentryid = qbe.id 
                    LEFT JOIN {question} AS q ON q.id = qv.questionid
                    WHERE
                        c.id = :courseid
                    GROUP BY
                        qc.id, q.name, q.qtype
                    HAVING
                        COUNT(q.id) > 1";
        $params = ['courseid' => $course->id];
        try {
            $duplicates = $DB->get_records_sql($sql, $params);
        } catch (\Exception $e) {
            mtrace("[COURSEID:$courseid] Query error: $e->getMessage() ");
            return;
        }
        if ($duplicates) {
            $totaldeleted = 0;
            foreach ($duplicates as $duplicate) {
                $questionids = explode(',', $duplicate->questionids);
                //Preserve the first question in each category.
                array_shift($questionids);
                foreach ($questionids as $questionid) {
                    try {
                        if (!questions_in_use([$questionid])) {
                            if (!$checkonly) {
                                question_delete_question($questionid);
                            }
                            mtrace("[COURSEID: $courseid] Found question: $questionid, Question name: $duplicate->questionname, Category: $duplicate->categoryid, Type: $duplicate->questiontype ");
                            $totaldeleted++;
                        }
                    } catch (\Exception $e) {
                        mtrace("[COURSEID: $courseid] : $e->getMessage() ");
                    }
                }
            }
            if (!$totaldeleted) {
                mtrace("[COURSEID:$courseid] There are no duplicate questions sharing the same name within the same category");
                return;
            }
            if ($checkonly) {
                mtrace("[COURSEID: $courseid] : must be deleted $totaldeleted questions");
            } else {
                mtrace("[COURSEID: $courseid] : deleted $totaldeleted questions");
            }
        } else {
            mtrace("[COURSEID:$courseid] There are no duplicate questions sharing the same name within the same category");
        }
    }
}
