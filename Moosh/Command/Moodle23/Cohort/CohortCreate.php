<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Cohort;
use Moosh\MooshCommand;
use context_coursecat;

class CohortCreate extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('create', 'cohort');

        $this->addOption('d|description:', 'description');
        $this->addOption('i|idnumber:', 'idnumber');
        $this->addOption('c|category:', 'category');

        $this->addArgument('name');

        $this->maxArguments = 255;
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/cohort/lib.php';

        foreach ($this->arguments as $argument) {
            $this->expandOptionsManually(array($argument));
            $options = $this->expandedOptions;

            if ($cohort = $DB->get_record('cohort',array('name'=>$argument))) {
                echo "Cohort already exists\n";
                exit(0);
            }

            if (!empty($options['category'])) {
                if ($category = $DB->get_record('course_categories',array('id'=>$options['category']))) {
                    $categorycontext = context_coursecat::instance($category->id);
                }
            }

            $cohort = new \stdClass();
            if (!empty($categorycontext)) {
                $cohort->contextid = $categorycontext->id;
            } else {
                $cohort->contextid = 1;
            }
            $cohort->name = $argument;
            $cohort->idnumber = $options['idnumber'];
            $cohort->description = $options['description'];
            $cohort->descriptionformat = FORMAT_HTML;

            $newcohort = cohort_add_cohort($cohort);

            echo $newcohort . "\n";
        }
    }
}
