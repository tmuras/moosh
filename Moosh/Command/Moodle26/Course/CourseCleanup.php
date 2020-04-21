<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle26\Course;

use Moosh\MooshCommand;

class CourseCleanup extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('cleanup', 'course');

        $this->addArgument('id');
    }

    public function execute()
    {
        global $CFG, $DB;


        require_once $CFG->dirroot . '/course/lib.php';

        // get all important resources related to the course
        // intro column for: resource, folder, url, label, assign, assignment, page, quiz, scorm, forum, choice, questionnaire, chat, wiki, data, book
        // Hmm, why not all intro columns
        $courseid = intval($this->arguments[0]);

        $this->get_used_intros($courseid);
        $this->get_summaries($courseid);
        $this->process_quizzes($courseid);
        $this->process_questionnaires($courseid);
        $this->process_pages($courseid);
        $this->process_lesson($courseid);


    }

    protected  function get_used_intros($courseid)
    {
        global $DB, $CFG;

        $records = $DB->get_records_sql("SELECT c.module AS id,m.name FROM {course_modules} c JOIN {modules} m ON c.module=m.id WHERE course=? group by module", array($courseid));

        foreach($records as $record) {
            $sql = "SELECT id,intro FROM {{$record->name}} WHERE intro IS NOT NULL AND intro <> '' AND intro <> '<p></p>' AND course = ?";
            try {
                $intros = $DB->get_records_sql($sql, array($courseid));
            } catch (\Exception $e) {
                // Most likely intro column does not exist.
                continue;
            }

            foreach($intros as $intro) {
                $cleaned = $this->clean_up($intro->intro);
                if($cleaned != $intro->intro) {
                    echo "Cleaned up {$record->name}.intro with ID {$intro->id}\n";
                }
            }
//            var_dump($intros);
        }

        return $record;
    }

    protected  function get_summaries($courseid)
    {
        global $DB;
        //course.summary
        //course_sections.summary
        $sql = "SELECT id,summary FROM {course} WHERE summary IS NOT NULL AND summary <> '' AND id = ?";
        $summaries = $DB->get_records_sql($sql, array($courseid));
        foreach($summaries as $summary) {
            $cleaned = $this->clean_up($summary->summary);
            if($cleaned != $summary->summary) {
                echo "Cleaned up course.summary with ID {$summary->id}\n";
            }
        }
        //var_dump($summaries);

        $sql = "SELECT id,summary FROM {course_sections} WHERE summary IS NOT NULL AND summary <> '' AND course = ?";
        $summaries = $DB->get_records_sql($sql, array($courseid));
        foreach($summaries as $summary) {
            $cleaned = $this->clean_up($summary->summary);
            if($cleaned != $summary->summary) {
                echo "Cleaned up course_sections.summary with ID {$summary->id}\n";
            }
        }

    }

    protected function process_questionnaires($courseid)
    {
        global $DB;

        $sql = "SELECT q.id,q.content FROM {questionnaire} qu JOIN {questionnaire_survey} s ON qu.sid = s.id JOIN {questionnaire_question} q ON s.id = q.survey_id WHERE qu.course = ? ";
        $results = $DB->get_records_sql($sql,array($courseid));
        foreach($results as $result) {
            $cleaned = $this->clean_up($result->content);
            if($cleaned != $result->content) {
                echo "Cleaned up questionnaire_question.content with ID {$result->id}\n";
            }
        }

        $sql = "SELECT s.id, s.info, s.thank_body FROM {questionnaire} qu JOIN {questionnaire_survey} s ON qu.sid = s.id WHERE qu.course = ? ";
        $results = $DB->get_records_sql($sql,array($courseid));
        foreach($results as $result) {
            $cleaned = $this->clean_up($result->info);
            if($cleaned != $result->info) {
                echo "Cleaned up questionnaire_survey.info with ID {$result->id}\n";
            }

            $cleaned = $this->clean_up($result->thank_body);
            if($cleaned != $result->thank_body) {
                echo "Cleaned up questionnaire_survey.thank_body with ID {$result->id}\n";
            }
        }

    }

    protected function process_quizzes($courseid)
    {
        global $DB;

        //get all quizzes and all questions for each
        $sql = "SELECT qu.* FROM {quiz} q JOIN {quiz_question_instances} i ON q.id = i.quiz JOIN {question} qu ON i.question = qu.id WHERE q.course = ? GROUP BY qu.id";
        $questions = $DB->get_records_sql($sql, array($courseid));
        foreach($questions as $question) {
            $cleaned = $this->clean_up($question->questiontext);
            if($cleaned != $question->questiontext) {
                echo "Cleaned up question.questiontext with ID {$question->id}\n";
//                var_dump($cleaned);  var_dump($question->questiontext);
            }
            $cleaned = $this->clean_up($question->generalfeedback);
            if($cleaned != $question->generalfeedback) {
                echo "Cleaned up question.generalfeedback with ID {$question->id}\n";
//                var_dump($cleaned);                var_dump($question->generalfeedback);
            }

        }

        // We only care about multichoice
        $sql = "SELECT qu.* FROM {quiz} q JOIN {quiz_question_instances} i ON q.id = i.quiz JOIN {qtype_multichoice_options} qu ON i.question = qu.questionid WHERE q.course = ? GROUP BY qu.id";
        $questions = $DB->get_records_sql($sql, array($courseid));
        foreach($questions as $question) {
            $cleaned = $this->clean_up($question->correctfeedback);
            if($cleaned != $question->correctfeedback) {
                echo "Cleaned up qtype_multichoice_options.correctfeedback with ID {$question->id}\n";
//                var_dump($cleaned);  var_dump($question->questiontext);
            }

            $cleaned = $this->clean_up($question->partiallycorrectfeedback);
            if($cleaned != $question->partiallycorrectfeedback) {
                echo "Cleaned up qtype_multichoice_options.partiallycorrectfeedback with ID {$question->id}\n";
//                var_dump($cleaned);  var_dump($question->questiontext);
            }

            $cleaned = $this->clean_up($question->incorrectfeedback);
            if($cleaned != $question->incorrectfeedback) {
                echo "Cleaned up qtype_multichoice_options.incorrectfeedback with ID {$question->id}\n";
//                var_dump($cleaned);  var_dump($question->questiontext);
            }
        }
    }

    protected function process_pages($courseid) {
        global $DB;

        $sql = "SELECT * FROM {page} WHERE course = ?";
        $results = $DB->get_records_sql($sql,array($courseid));
        foreach($results as $result) {
            $cleaned = $this->clean_up($result->content);
            if($cleaned != $result->content) {
                echo "Cleaned up page.content with ID {$result->id}\n";
            }
        }
    }

    protected function process_lesson($courseid) {
        global $DB;

        $sql = "SELECT p.* FROM {lesson} l JOIN {lesson_pages} p ON l.id = p.lessonid WHERE l.course = ?";
        $results = $DB->get_records_sql($sql,array($courseid));
        foreach($results as $result) {
            $cleaned = $this->clean_up($result->contents);
            if($cleaned != $result->contents) {
                echo "Cleaned up lesson_pages.contents with ID {$result->id}\n";
            }
        }
    }

    protected function clean_up($text) {
        return purify_html($text, FORMAT_HTML);
    }
}
