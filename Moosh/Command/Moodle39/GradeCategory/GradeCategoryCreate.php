<?php

/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\GradeCategory;
use Moosh\MooshCommand;

/**
 * Adds a new GradeCategory to the specified course
 *
 * @copyright 2013 David Monllaó
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class GradeCategoryCreate extends MooshCommand
{

    public function __construct()
    {
        parent::__construct('create', 'gradecategory');

        $this->addOption('n|name:', 'grade category name');
        $this->addOption('a|aggregation', 'grade aggregation', null);
        $this->addOption('o|options:', 'any options that should be passed for grade category creation', null);

        $this->addArgument('parent');
        $this->addArgument('courseid');

        $this->minArguments = 2;
    }

    /**
     * Uses the data generator to create grade category
     *
     * @return Displays the grade category id.
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
        $categorydata = new \stdClass();
	$categorydata->parent = $this->arguments[0];
        $categorydata->courseid = $this->arguments[1];

        // $options are create_grade_category options.
        $options = $this->expandedOptions;

        if (!empty($options['name'])) {
            $categorydata->fullname = $options['name'];
        }
        $record = $generator->create_grade_category($categorydata);

        if ($this->verbose) {
            echo "Grade_category {$options['name']} for course {$categorydata->courseid} created successfully\n";
        }

        // Return the activity id.
        echo "{$record->id}\n";
    }

}
