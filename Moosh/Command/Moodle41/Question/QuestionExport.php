<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle41\Question;

use Moosh\MooshCommand;

/**
 * Command exports all question. It may filter them by categories or courses.
 */
class QuestionExport extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('export', 'question');

        $this->addOption('c|course:', 'Specifies questions course id.');
        $this->addOption('f|filename:', 'Specifies exported file name.', 'moosh-questions-export');
        $this->addOption('C|category:', 'Specifies questions category id.');
    }

    public function execute() {
        global $DB, $CFG;

        $courseId = $this->expandedOptions['course'];
        $categoryId = $this->expandedOptions['category'];
        $fileName = $this->expandedOptions['filename'];

        // moodle queries questions using similar requests, it is needed due to complicated structure
        $sql = "
            SELECT q.id AS id, q.name AS name, qc.id AS categoryId, q.questiontext as text
            FROM {question} q
            JOIN {question_versions} qv ON q.id = qv.questionid
            JOIN {question_bank_entries} qbe ON qv.questionbankentryid = qbe.id
            JOIN {question_categories} qc ON qbe.questioncategoryid = qc.id
            JOIN {context} ctx ON qc.contextid = ctx.id
            JOIN {course} c ON ctx.instanceid = c.id 
            WHERE (qc.id = '$categoryId' or '$categoryId' = '') 
            and (c.id = '$courseId' or '$courseId' = '')  
        ";

        $questions = $DB->get_records_sql($sql);

        if(!string_ends_with($fileName, ".json")) {
            $fileName = $fileName . ".json";
        }

        $fullQuestions = $this->loadAnswers($questions);

        $json = json_encode($fullQuestions);

        file_put_contents($fileName, $json);
    }

    /**
     * Loads answer for question array and returns it.
     * @param stdClass[] $questions
     * @return stdClass[]
     */
    public function loadAnswers($questions) {
        global $DB;

        $questionsWithAnswers = array();

        foreach ($questions as $question) {
            $correctAnswer = $DB->get_record('question_answers', array('question' => $question->id, 'fraction' => 1.0));
            $question->answer = $correctAnswer->answer;

            $questionsWithAnswers[] = $this->formatQuestion($question);
        }

        return $questionsWithAnswers;
    }

    /**
     * Removes html tags from question text and answer
     * @param \stdClass $question
     * @return \stdClass
     */
    public function formatQuestion($question) {
        $question->text = $this->stripAndTrim($question->text);
        $question->answer = $this->stripAndTrim($question->answer);

        return $question;
    }

    /**
     * Strips html from string and trims it. If null given returns null.
     * @param string $string
     * @return null|string
     */
    public function stripAndTrim($string) {
        if(isset($string)) {
            $string = trim(strip_tags($string));
        }

        return $string;
    }

}
