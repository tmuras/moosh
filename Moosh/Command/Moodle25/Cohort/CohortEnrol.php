<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace Moosh\Command\Moodle25\Cohort;

class CohortEnrol extends \Moosh\Command\Moodle23\Cohort\CohortEnrol
{
    protected function enrol_cohort_sync($courseid)
    {
        $trace = new \null_progress_trace();
        enrol_cohort_sync($trace, $courseid);
        $trace->finished();
    }
}

