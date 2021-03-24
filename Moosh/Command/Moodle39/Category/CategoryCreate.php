<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Category;
use Moosh\MooshCommand;

class CategoryCreate extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('create', 'category');

        $this->addOption('d|description:', 'description');
        $this->addOption('p|parent:', 'format');
        $this->addOption('i|idnumber:', 'idnumber');
        $this->addOption('v|visible:', 'visible');
        $this->addOption('r|reuse', 'reuse existing category if it is the only matching one', false);

        $this->addArgument('name');

        $this->maxArguments = 255;
    }

    public function execute()
    {
        foreach ($this->arguments as $argument) {
            $this->expandOptionsManually(array($argument));
            $options = $this->expandedOptions;

            $category = new \stdClass();
            $category->name = $argument;
            $category->description = $options['description'];
            $category->parent = $options['parent'];
            $category->idnumber = $options['idnumber'];
            $category->visible = $options['visible'];
            if ($options['reuse'] && $existing = $this->find_category($category)) {
                $newcategory = $existing;
            } else {
                $newcategory = $this->create_category($category);
            }

            //either use API create_course
            echo $newcategory->id . "\n";
        }
    }

    protected function create_category($category)
    {
        return \core_course_category::create($category);
    }

    protected function find_category($category)
    {
        global $DB;
        $params = array('name' => $category->name);
        foreach (array('idnumber', 'parent', 'description') as $param) {
            if ($category->$param) {
                $params[$param] = $category->$param;
            }
        }
        $categories = $DB->get_records('course_categories', $params);
        if (count($categories) == 1) {
            return array_pop($categories);
        } else {
            return null;
        }
    }

}