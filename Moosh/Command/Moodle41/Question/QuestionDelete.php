<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle41\Question;

use Moosh\MooshCommand;

class QuestionDelete extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('delete', 'question');
        $this->addArgument('questionid');
        $this->addOption('check-only', 'Only check if the question is in use, do not delete');
    }

    public function execute()
    {
        global $DB, $CFG;
        $questionid = $this->arguments[0];
        $checkonly = $this->expandedOptions['check-only'];

        require_once($CFG->dirroot . '/question/engine/lib.php');
        require_once($CFG->dirroot . '/question/type/questiontypebase.php');

        $question = $DB->get_record('question', ['id' => $questionid]);

        if (!$question) {
            mtrace("[ID:$questionid] No question found with id");
            return;
        }

        $questionsToCheck = [$question->id];

        if ($question->parent) {
            $questionsToCheck[] = $question->parent;
        }
        if (questions_in_use($questionsToCheck)) {
            mtrace("[ID:$questionid] Question in use in activities - can not delete");
            if ($checkonly) {
                return;
            }
        }
        if (!$checkonly) {
            question_delete_question($question->id);
        }
    }
}
