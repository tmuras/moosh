<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class CategoryCreate extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('create', 'category');

        $this->addOption('d|description:', 'description');
        $this->addOption('p|parent:', 'format');
        $this->addOption('v|visible:', 'visible');

        $this->addArgument('name');

        $this->maxArguments = 255;
    }

    public function execute()
    {
        global $CFG;

        require_once $CFG->dirroot . '/course/lib.php';

        foreach ($this->arguments as $argument) {
            $this->expandOptionsManually(array($argument));
            $options = $this->expandedOptions;

            $category = new stdClass();
            $category->name = $argument;
            $category->description = $options['description'];
            $category->parent = $options['parent'];
            $category->visible = $options['visible'];
            $newcategory = create_course_category($category);

            //either use API create_course
            echo $newcategory->id . "\n";
        }
    }
}

