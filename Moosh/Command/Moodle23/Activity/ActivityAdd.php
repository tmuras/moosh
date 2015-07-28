<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Activity;
use Moosh\MooshCommand;

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
        $moduledata = new \stdClass();
        $moduledata->course = $this->arguments[1];

        // $options are course module options.
        $options = $this->expandedOptions;

        // Name is a module instance attr.
        if (!empty($options['name'])) {
            $moduledata->name = $options['name'];
            unset($options['name']);
        }

        $record = $generator->create_module($this->arguments[0], $moduledata, $options);

        if ($this->verbose) {
            echo "Activity {$this->arguments[0]} created successfully\n";
        }

        // Return the activity id.
        echo "{$record->id}\n";
    }

}
