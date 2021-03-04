<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle33\Dev;
use Moosh\MooshCommand;

class RandomLabel extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('label', 'random');

        $this->addArgument('courseid');

        $this->addOption('i|include-text:', 'make sure this piece of text is included in the random content', NULL);

    }


    public function execute()
    {
        //some variables you may want to use
        //$this->cwd - the directory where moosh command was executed
        //$this->mooshDir - moosh installation directory
        //$this->expandedOptions - commandline provided options, merged with defaults
        //$this->topDir - top Moodle directory

        global $CFG, $DB, $USER;

        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->dirroot . '/course/modlib.php');

        $length = 64;

        if ($this->expandedOptions['include-text']) {
            $split = rand(0, $length);
            $text = generate_html_page($split) . $this->expandedOptions['include-text'] . generate_html_page($length - $split);
        } else {
            $text = generate_html_page($length);
        }

        $moduleinfo = new \stdClass();
        $moduleinfo->introeditor =
            array(
                'text' => $text,
                'format' => '1',
                'itemid' => NULL,
            );
        $moduleinfo->visible = '1';
        $moduleinfo->visibleoncoursepage = 1;
        $moduleinfo->course = $this->arguments[0];
        $moduleinfo->coursemodule = 0;

        //choose random section from a course
        $sections = $DB->get_records('course_sections', array('course' => $this->arguments[0]), '', 'section');

        $moduleinfo->section = array_rand($sections);

        $moduleinfo->module = 12;
        $moduleinfo->modulename = 'label';
        $moduleinfo->instance = 0;
        $moduleinfo->add = 'label';
        $moduleinfo->update = 0;
        $moduleinfo->return = 0;
        $moduleinfo->sr = 0;



        $course = $DB->get_record('course', array('id' => $this->arguments[0]), '*', MUST_EXIST);
        add_moduleinfo($moduleinfo, $course);
    }
}


