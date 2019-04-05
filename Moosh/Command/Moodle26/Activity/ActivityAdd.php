<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle26\Activity;
use Moosh\MooshCommand;

use GetOptionKit\Argument;

/**
 * Adds a new activity to the specified course
 *
 * @copyright 2013 David Monllaó
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ActivityAdd extends MooshCommand
{

    public function __construct()
    {
        parent::__construct('add', 'activity');

        $this->addOption('n|name:', 'activity instance name');
        $this->addOption('s|section:', 'section number', '1');
        $this->addOption('i|idnumber:', 'idnumber', null);
        $this->addOption('c|gradecat:', 'gradecategory id', null);
        $this->addOption('o|options:', 'any options that should be passed for activity creation', null);

        $this->addArgument('activitytype');
        $this->addArgument('course');

        $this->minArguments = 2;
    }

    /**
     * Uses the data generator to create module instances.
     *
     * @return Displays the activity id (not the course module, it depends on the activity type).
     */
    public function execute()
    {

        // Getting moodle's data generator.
        $generator = get_data_generator();

        // All data provided by the data generator.
/*
        * @param array|stdClass $record data for module being generated. Requires 'course' key
        *     (an id or the full object). Also can have any fields from add module form.
        * @param null|array $options general options for course module. Since 2.6 it is
        *     possible to omit this argument by merging options into $record
*/
        $moduledata = new \stdClass();
        $moduledata->course = $this->arguments[1];
        $moduledata->requiresubmissionstatement = 1;

        // $options are course module options.
        $options = $this->expandedOptions;

        if (!empty($options['options'])) {
            $course_module_options = preg_split( '/\s+(?=--)/', $options['options']);
            foreach ( $course_module_options as $option ) {
                $arg = new Argument( $option );
                $name = $this->getOptionName($arg);
                $value = $arg->getOptionValue();
                $moduledata->$name = $value;
                if ($this->verbose) {
                    echo "\"$option\" -> $name=" . $value . "\n";
                }
            }
        }

        if (!empty($options['name'])) {
            $moduledata->name = $options['name'];
        }
        if (!empty($options['idnumber'])) {
            $moduledata->idnumber = $options['idnumber'];
        }
        if (!empty($options['gradecat'])) {
            $moduledata->gradecat = $options['gradecat'];
        }

        $moduledata->section = $options['section'];

        $record = $generator->create_module($this->arguments[0], $moduledata);

        if ($this->verbose) {
            echo "Activity {$this->arguments[0]} created successfully\n";
        }

        // Return the activity id.
        echo "{$record->id}\n";
    }

    private function getOptionName($arg)
    {
        if (preg_match('/^[-]+([_a-zA-Z0-9-]+)/', $arg->arg, $regs)) {
            return $regs[1];
        }
    }
}
