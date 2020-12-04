<?php
/**
 * moosh quiz-delete-attempts [-v, --verbose] <quizid>
 *
 * @copyright  2020 onwards Jakub Kleban
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Quiz;
use Moosh\MooshCommand;

class QuizDeleteAttempts extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('delete-attempts', 'quiz');
        $this->addOption('v|verbose', 'verbose');
        $this->addArgument('quizid');

    }

    public function execute()
    {
        global $CFG, $DB;
        require_once($CFG->dirroot.'/mod/quiz/locallib.php');

        $qid = $this->arguments[0];
        $verbose = $this->expandedOptions['verbose'];

        //Make sure qid is valid.
        if ($qid <= 0) {
            echo "Invalid quiz id. Must be > 0\n";
            exit;
        }

        if (!$cm = get_coursemodule_from_id('quiz', $qid)) {
            echo "Invalid course module id. Must be > 0\n";
            exit;
        }
        if (!$quiz = $DB->get_record('quiz', array('id' => $cm->instance))) {
            echo "Invalid course module.\n";
            exit;
        }

        $attempts = $DB->get_records('quiz_attempts', array('quiz' => $quiz->id));

        foreach ($attempts as $attempt){
            quiz_delete_attempt($attempt, $quiz);

            //print out all of the deleted attempts, if 'verbose' is set
            if ($verbose){
                echo "Deleted attempt: $attempt->id\n";
            }
        }

        echo 'Deleted '.sizeof($attempts).' questions'."\n";
    }
}
