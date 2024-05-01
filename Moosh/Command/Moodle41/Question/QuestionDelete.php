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

        if ($this->questions_in_use($questionsToCheck)) {
            mtrace("[ID:$questionid] Question in use in activities - can not delete");
            if ($checkonly) {
                return;
            }
        }
        if (!$checkonly) {
            question_delete_question($question->id);
        }
    }


    private function questions_in_use($questionids) {

        // Are they used by the core question system?
        if (\question_engine::questions_in_use($questionids)) {
            return 'question_engine';
        }

        // Check if any plugins are using these questions.
        $callbacksbytype = get_plugins_with_function('questions_in_use');
        foreach ($callbacksbytype as $callbacks) {
            foreach ($callbacks as $function) {
                if ($function($questionids)) {
                    return true;
                }
            }
        }

        // Finally check legacy callback.
        $legacycallbacks = get_plugin_list_with_function('mod', 'question_list_instances');
        foreach ($legacycallbacks as $plugin => $function) {
            debugging($plugin . ' implements deprecated method ' . $function .
                '. ' . $plugin . '_questions_in_use should be implemented instead.', DEBUG_DEVELOPER);

            if (isset($callbacksbytype['mod'][substr($plugin, 4)])) {
                continue; // Already done.
            }

            foreach ($questionids as $questionid) {
                if (!empty($function($questionid))) {
                    return true;
                }
            }
        }

        return false;
    }

}
