<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Questioncategory;
use Moosh\MooshCommand;

class QuestioncategoryCreate extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('create', 'questioncategory');

        $this->addOption('d|description:', 'info');
        $this->addOption('f|infoformat', 'info: description format. Defaults to FORMAT_MARKDOWN', 4);
        $this->addOption('p|parent:', 'categoryid of the parent category');
        $this->addOption('c|context:', 'contextid of the parent, new category');
        $this->addOption('i|idnumber', 'idnumber');
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
            $category->info = $options['description'];
            $category->infoformat = $options['infoformat'];
            $category->parent = $options['parent'];
            $category->contextid = $options['context'];
            $category->idnumber = $options['idnumber']; 
            if ($options['reuse'] && $existing = $this->find_category($category)) {
                echo $existing->id . "\n";
            } else {
                $newcategoryid = $this->create_category($category);
                echo $newcategoryid . "\n";

            }
        }
    }

    protected function create_category($category)
    {
        # global $CFG;
        global $DB;

        if ((string) $category->idnumber === '') {
            $category->idnumber = null;
        } else if (!empty($category->contextid)) {
            // While this check already exists in the form validation, this is a backstop preventing unnecessary errors.
            if ($DB->record_exists('question_categories',
                    ['idnumber' => $category->idnumber, 'contextid' => $category->contextid])) {
                $category->idnumber = null;
            }
        }

        $category->sortorder = 999;
        $category->stamp = make_unique_id_code();
        $categoryid = $DB->insert_record("question_categories", $category);

        // Log the creation of this category.
        $entry = new \stdClass();
        $entry->id = $categoryid;
        $entry->contextid = $category->contextid;
        $event = \core\event\question_category_created::create_from_question_category_instance($entry);
        $event->trigger();
        
        return $categoryid;
    }

    protected function find_category($category)
    {
        global $DB;
        $params = array('name' => $category->name);
        foreach (array('parent', 'contextid') as $param) {
            if ($category->$param) {
                $params[$param] = $category->$param;
            }
        }
        $categories = $DB->get_records('question_categories', $params);
        if (count($categories) == 1) {
            return array_pop($categories);
        } else {
            return null;
        }
    }
}

