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
class QuestionExport extends MooshCommand {
    public function __construct() {
        parent::__construct('export', 'question');

        $this->addOption('c|course:', 'Specifies questions course id.');
        $this->addOption('f|filename:', 'Specifies exported file name.', 'moosh-questions-export');
        $this->addOption('C|category:', 'Specifies questions category id.');
        $this->addOption('r|recursive', 'Exports also questions belonging to child categories of --category.');
    }

    public function execute() {
        $courseId = $this->expandedOptions['course'];
        $categoryId = $this->expandedOptions['category'];
        $fileName = $this->expandedOptions['filename'];
        $isRecursive = $this->expandedOptions['recursive'];

        // check if we want to export child categories
        if($isRecursive) {
            if($categoryId == null) {
                cli_error("Parameter --recursive must be used with parameter --category.");
            }

            $categoryIds = $this->getCategoryChildrenIds($categoryId);

            if($this->verbose) {
                $children = implode(", ", $categoryIds);
                mtrace("Loaded recursive categories: [$children].");
            }

        } elseif($categoryId) {
            $categoryIds = [$categoryId];
        } else {
            $categoryIds = [];
        }

        $questions = $this->loadQuestions($courseId, $categoryIds);

        if(!string_ends_with($fileName, ".json")) {
            $fileName = $fileName . ".json";
        }

        if($this->verbose) {
            mtrace("Exporting questions to $fileName.");
        }

        $fullQuestions = $this->loadAnswers($questions);

        $json = json_encode($fullQuestions);

        file_put_contents($fileName, $json);

        $count = count($fullQuestions);
        print "Exported $count questions to file $fileName.\n";
    }

    /**
     * Loads answer for question array and returns it.
     * @param stdClass[] $questions
     * @return stdClass[]
     */
    public function loadAnswers($questions) {
        global $DB;

        if($this->verbose) {
            $count = count($questions);
            mtrace("Loading answers for $count questions.");
        }

        $questionsWithAnswers = array();

        $sql = "select * from {question_answers} where question = :question_id and fraction > 0";

        $answersCount = 0;

        foreach ($questions as $question) {
            $correctAnswers = $DB->get_records_sql($sql, array('question_id' => $question->id));
            $question->answers = array_column($correctAnswers, 'answer');

            $questionsWithAnswers[] = $this->formatQuestion($question);

            $answersCount += count($correctAnswers);
        }

        if($this->verbose) {
            $questionsCount = count($questions);
            $answersCount -
            mtrace("Loaded $answersCount answers for $questionsCount questions.");
        }

        return $questionsWithAnswers;
    }

    /**
     * Loads all questions based on course id or category ids
     * @param int $courseId
     * @param int[] $categoryIds
     * @return array questions
     */
    public function loadQuestions($courseId, $categoryIds) {
        global $DB;

        $skipCategory = empty($categoryIds);

        if($skipCategory) {
            // IN clause must refer to some value, -1 will never exist so we add it.
            // I decided to add -1, because it's imo safer and cleaner than concatenating sql queries
            $categoryIds = [-1];
        }

        $categoriesStr = implode(", ", $categoryIds);
        $params = array('skip_category' => $skipCategory, 'course_id' => $courseId,'skip_course' => empty($courseId));
        $sql = "
            SELECT q.id AS id, q.name AS name, qc.id AS category_id, q.questiontext as text, qv.version as version
            FROM {question} q
            LEFT JOIN {question_versions} qv ON q.id = qv.questionid
            LEFT JOIN {question_bank_entries} qbe ON qv.questionbankentryid = qbe.id
            LEFT JOIN {question_categories} qc ON qbe.questioncategoryid = qc.id
            LEFT JOIN {context} ctx ON qc.contextid = ctx.id
            LEFT JOIN {course} c ON ctx.instanceid = c.id
            WHERE (qc.id in ($categoriesStr) or :skip_category = true)
            and (c.id = :course_id or :skip_course = true);
        ";

        $result = $DB->get_records_sql($sql, $params);

        if($this->verbose) {
            $count = count($result);
            mtrace("Loaded $count question records from DB.");
        }

        return $result;
    }

    /**
     * Removes html tags from question text and answer
     * @param \stdClass $question
     * @return \stdClass
     */
    public function formatQuestion($question) {
        $question->text = $this->stripAndTrim($question->text);
        $question->answers = array_map([$this, 'stripAndTrim'], $question->answers);

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

    /**
     * Returns an array of category children ids.
     * @param int $id parent id
     * @return int[]
     */
    public function getCategoryChildrenIds($id) {
        // This function is a little bit messy, please refactor if u have time.
        global $DB;

        // for safety
        if(!is_number($id)) {
            return [];
        }

        $categoriesIds = [$id];

        $childrenCategoriesResult = $DB->get_records("question_categories", ['parent' => $id]);

        foreach ($childrenCategoriesResult as $child) {
            if(!is_number($child->id) || $child->id == '0') {
                continue;
            }

            $subCategoriesIds = $this->getCategoryChildrenIds($child->id);
            if(!empty($subCategoriesIds)) {
                array_push($categoriesIds, ...$subCategoriesIds);
            }
        }

        return $categoriesIds;
    }
}
