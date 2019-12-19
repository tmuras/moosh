<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace Moosh\Command\Moodle26\Category;

use Moosh\MooshCommand;

class CategoryImport extends MooshCommand
{

    public function __construct()
    {
        parent::__construct('import', 'category');

        $this->addArgument('categories.xml');
        $this->addOption('p|parent:', 'create under this category', null);
    }


    public function execute()
    {
        global $CFG;

        require_once($CFG->dirroot . '/course/lib.php');

        $options = $this->expandedOptions;
        $arguments = $this->arguments;
        $file = $this->checkFileArg($arguments[0]);

        $this->categorystack = array();
        if ($options['parent']) {
            $this->categorystack[] = $options['parent'];
        } else {
            $this->categorystack[] = 0;
        }

        $this->coursesmap = array();


        $this->parser = xml_parser_create();
        xml_set_element_handler(
            $this->parser,
            array(&$this, "start_element"),
            array(&$this, "end_element")
        );


        if (!($fp = fopen($file, "r"))) {
            die("could not open XML input");
        }

        while ($data = fread($fp, 4096)) {
            if (!xml_parse($this->parser, $data, feof($fp))) {
                die(sprintf("XML error: %s at line %d",
                    xml_error_string(xml_get_error_code($this->parser)),
                    xml_get_current_line_number($this->parser)));
            }
        }
        xml_parser_free($this->parser);
        fix_course_sortorder();
    }

    public function create_category($category)
    {
        global $CFG;
        require_once $CFG->libdir . '/coursecatlib.php';
        return \coursecat::create($category);
    }

    public function start_element($parser, $name, $attrs)
    {
        $current = end($this->categorystack);
        if ($name == 'CATEGORY') {
            echo "Creating new category " . $attrs['NAME'] . " under $current\n";
            $category = new \stdClass();
            $category->name = $attrs['NAME'];
            if(isset($attrs['IDNUMBER'])) {
                $category->idnumber = $attrs['IDNUMBER'];
            }            
            $category->parent = $current;

            $newcat = $this->create_category($category);
            if(isset($attrs['OLDID'])) {
                $this->coursesmap[$attrs['OLDID']] = $newcat->id;
            }
            $this->categorystack[] = $newcat->id;
        }
    }

    public function end_element($parser, $name)
    {
        if ($name == 'CATEGORY') {
            array_pop($this->categorystack);
        }
    }
}

