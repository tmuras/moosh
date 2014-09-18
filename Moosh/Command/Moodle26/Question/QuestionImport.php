<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle26\Question;

use Moosh\MooshCommand;

class QuestionImport extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('import', 'question');
        $this->addArgument('questions.xml');
        $this->addArgument('quiz_id');
    }

    public function execute()
    {
        global $DB,$CFG;

        require_once($CFG->dirroot . '/question/editlib.php');
        require_once($CFG->dirroot . '/question/import_form.php');
        require_once($CFG->dirroot . '/question/format.php');

        $user = get_admin();
        if (!$user) {
            echo "Error: No admin account was found\n";
            exit(1);
        }

        complete_user_login($user);

        $arguments = $this->arguments;
        if ($arguments[0][0] != '/')
        {
            $arguments[0] = $this->cwd . DIRECTORY_SEPARATOR . $arguments[0];
        }

        if (!file_exists($arguments[0]))
        {
            cli_error("Input file '" . $arguments[0] . "' does not exist.");
        }

        if (!is_readable($arguments[0])) {
            cli_error("Input file '" . $arguments[0] . "' is not readable.");
        }

        $file = $arguments[0];
        $quiz = $arguments[1];
        $quiz = $DB->get_record('quiz', array('id'=>$quiz),'*',MUST_EXIST);
        $course = $DB->get_record('course', array('id'=>$quiz->course),'*',MUST_EXIST);
        $coursecontext = \context_course::instance($course->id);
        $coursemodule = get_coursemodule_from_instance('quiz',$quiz->id);
        $quizcontext = \context_module::instance($coursemodule->id,MUST_EXIST);
        //var_dump($coursecontext); die();

        // Use existing questions category for quiz.
        $category = $DB->get_record('question_categories',array('contextid'=>$coursecontext->id));
        //$category = $DB->get_record("question_categories", array('id' => $category),MUST_EXIST);

        $formatfile = $CFG->dirroot .  '/question/format/xml/format.php';
        if (!is_readable($formatfile)) {
            throw new moodle_exception('formatnotfound', 'question', '', 'xml');
        }

        require_once($formatfile);

        $qformat = new \qformat_xml();

        // load data into class
        $qformat->setCategory($category);
        $qformat->setContexts(array($quizcontext));
        $qformat->setCourse($course);
        $qformat->setFilename($file);
        $qformat->setRealfilename($file);
        $qformat->setMatchgrades('nearest');
        $qformat->setStoponerror(true);

        // Do anything before that we need to
        if (!$qformat->importpreprocess()) {
            print_error('cannotimport', '');
        }

        // Process the uploaded file
        if (!$qformat->importprocess($category)) {
            print_error('cannotimport', '');
        }

        // In case anything needs to be done after
        if (!$qformat->importpostprocess()) {
            print_error('cannotimport', '');
        }
    }

    public function importQuiz($courseid, $quizid)
    {
        global $CFG;

        $course = get_record('course', 'id', $courseid);
        $quiz = get_record('quiz', 'id', $quizid);
        $fileformat = 'xml'; //Moodle XML format
        $questioncat = null;


    }



}

