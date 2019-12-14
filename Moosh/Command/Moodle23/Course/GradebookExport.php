<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Course;
use Moosh\MooshCommand;

class GradebookExport extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('export', 'gradebook');

        //$this->addArgument('name');

        $this->addOption('c|id:', 'course id', null);
        $this->addOption('i|itemids:', 'exercise grade ids', null);
        $this->addOption('g|groupid:', 'group id', 0);
        $this->addOption('x|exportfeedback:', 'exportfeedback', 0);
        $this->addOption('a|onlyactive:', 'onlyactive', 1);
        $this->addOption('d|displaytype:', 'displaytype. real=1, percentage=2, letter=3', '1');
        $this->addOption('p|decimalpoints:', 'decimalpoints', 2);
        $this->addOption('s|separator:', 'separator, eg, tab, comma', 'comma');
        $this->addOption('f|format:', 'export format, ie, ods, txt, xls, xml', 'txt');

        $this->addArgument('grade_item_id(s)');
        $this->addArgument('course_id');

        $this->minArguments = 2;
        $this->maxArguments = 2;
    }

    public function execute()
    {

        global $CFG, $DB;

        require_once($CFG->dirroot . '/grade/export/lib.php');
        require_once($CFG->dirroot . '/grade/export/txt/grade_export_txt.php');
        require_once($CFG->dirroot . '/grade/export/ods/grade_export_ods.php');
        require_once($CFG->dirroot . '/grade/export/xls/grade_export_xls.php');
        require_once($CFG->dirroot . '/grade/export/xml/grade_export_xml.php');
        require_once($CFG->libdir . '/grade/grade_item.php');
        require_once($CFG->libdir . '/csvlib.class.php');


        // Some variables you may want to use
        //  $this->cwd - the directory where moosh command was executed
        //  $this->mooshDir - moosh installation directory
        //  $this->expandedOptions - commandline provided options, merged with defaults
        //  $this->topDir - top Moodle directory
        //  $this->arguments[0] - first argument passed
        //  $this->pluginInfo - array with information about the current plugin (based on cwd), keys:'type','name','dir'
        //  $this->verbose - if set to true, then "moosh -v" was run - add more verbose / debug information

        $itemids = $this->arguments[0];
        $id = $this->arguments[1];

        $options = $this->expandedOptions;
        if (isset($options['groupid'])) {
            $groupid = $options['groupid'];
        }
        if (isset($options['exportfeedback'])) {
            $exportfeedback = $options['exportfeedback'];
        }
        if (!empty($options['onlyactive'])) {
            $onlyactive = $options['onlyactive'];
        }
        if (isset($options['displaytype'])) {
            $displaytype = $options['displaytype'];
        }
        if (isset($options['decimalpoints'])) {
            $decimalpoints = $options['decimalpoints'];
        }
        if (!empty($options['separator'])) {
            $separator = $options['separator'];
        }
        if (isset($options['format'])) {
            $format = $options['format'];
        }

        if (!$course = $DB->get_record('course', array('id'=>$id))) {
                    print_error('invalidcourseid');
        }

        $formdata = \grade_export::export_bulk_export_data($id, $itemids, $exportfeedback, $onlyactive, $displaytype,
                $decimalpoints, null, $separator);

        if ( $format == 'odt' ) {
                $export = new \grade_export_ods($course, $groupid, $formdata);
        }
        if ( $format == 'txt' ) {
                $export = new \grade_export_txt($course, $groupid, $formdata);
        }
        if ( $format == 'xls' ) {
                $export = new \grade_export_xls($course, $groupid, $formdata);
        }
        if ( $format == 'xml' ) {
                $export = new \grade_export_xml($course, $groupid, $formdata);
        }
        if ($this->verbose) { var_dump( $export ); }
        $export->print_grades();

        // if verbose mode was requested, show some more information/debug messages
    }
}
