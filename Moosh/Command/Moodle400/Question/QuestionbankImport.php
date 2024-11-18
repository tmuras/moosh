<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle400\Question;

use Moosh\MooshCommand;

class QuestionbankImport extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('import', 'questionbank');

        $this->addArgument('questions.xml|questions.gift');
        $this->addArgument('question category ID');

    }

    public function execute()
    {
        global $CFG, $DB;
        // Some variables you may want to use
        //  $this->cwd - the directory where moosh command was executed
        //  $this->mooshDir - moosh installation directory
        //  $this->expandedOptions - commandline provided options, merged with defaults
        //  $this->topDir - top Moodle directory
        //  $this->arguments[0] - first argument passed
        //  $this->pluginInfo - array with information about the current plugin (based on cwd), keys:'type','name','dir'
        //  $this->verbose - if set to true, then "moosh -v" was run - add more verbose / debug information


        $fullpath = $this->checkPathArg($this->arguments[0]);
        $file = basename($fullpath);

        if (substr($file, -4) == '.xml') {
            $format = 'xml';
        } else if (substr($file, -5) == '.gift') {
            $format = 'gift';
        } else {
            cli_error("Unknown format. File extension should be .xml or .gift.");
        }

        $categoryid = (int)$this->arguments[1];
        if (!$category = $DB->get_record("question_categories", ['id' => $categoryid])) {
            cli_error('Question category not found');
        }

        $formatfile = $CFG->dirroot . '/question/format/' . $format . '/format.php';
        require_once($formatfile);
        require_once($CFG->dirroot . '/question/engine/bank.php');

        $classname = 'qformat_' . $format;
        $qformat = new $classname();

        // Load data into class.
        $qformat->setCategory($category);
//        $qformat->setContexts($contexts->having_one_edit_tab_cap('import'));
//        $qformat->setCourse($COURSE);
        $qformat->setFilename($fullpath);
        $qformat->setRealfilename($file);
        // error or nearest
        $qformat->setMatchgrades('error');
        $qformat->setCatfromfile(false);
        $qformat->setContextfromfile(false);
        $qformat->setStoponerror(false);

        if($this->verbose) {
            echo "Importing '$file' from '$fullpath'\n";
        }
        // Do anything before that we need to.
        if (!$qformat->importpreprocess()) {
            cli_error('importpreprocess');
        }

        // Process the uploaded file.
        if (!$qformat->importprocess()) {
            cli_error('importprocess');
        }

        // In case anything needs to be done after.
        if (!$qformat->importpostprocess()) {
            cli_error('importpostprocess');
        }
    }
}
