<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle35\Dev;

use Moosh\MooshCommand;
use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\Node;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Symfony\Component\Finder\Finder;

class LangFinder extends NodeVisitorAbstract {
    public $list = [];
    public $showwarnings = false;

    protected function printTypeDebug(Node $node = null) {
        if(is_null($node)) {
            echo "\ttype: null\n";
        } else {
            echo "\ttype: " . $node->getType() . "\n";
            echo "\tline: " . $node->getLine() . "\n";
        }
    }

    protected function processClass(Expr\New_ $node) {
        if (!isset($node->class->parts)) {
            if ($this->showwarnings) {
                echo "Can't handle new with type: " . $node->class->getType() . "\n";
                $this->printTypeDebug($node->class);
            }
            return;
        }
        $name = $node->class->parts[0];
        if ($name != 'lang_string') {
            return;
        }
        $args = $node->args;

        if (count($args) < 1) {
            echo "Invalid get_string invocation - with no arguments.";
            return;
        }

        if (count($args) == 1) {
            $value1 = $args[0]->value;
            if ($value1 instanceof Scalar\String_) {
                /** @var $value1 Scalar\String_ */
                $this->list['moodle'][$value1->value] = true;
            } else {
                if ($this->showwarnings) {
                    echo "Can't process get_string() with argument different than simple string: \n";
                    $this->printTypeDebug($value1);
                }
                return;
            }
        }

        if (count($args) > 1) {
            $value1 = $args[0]->value;
            $value2 = $args[1]->value;
            if (!$value1 instanceof Scalar\String_) {
                if ($this->showwarnings) {
                    echo "Can't process get_string() with argument different than simple string:\n";
                    $this->printTypeDebug($value1);
                }
                return;
            }

            if (!$value2 instanceof Scalar\String_) {
                if ($this->showwarnings) {
                    echo "Can't process get_string() with argument different than simple string:\n";
                    $this->printTypeDebug($value2);
                }
                return;
            }

            $this->list[$value2->value][$value1->value] = true;
        }

    }

    protected function processFunction(FuncCall $node) {
        switch ($node->name) {
            case 'get_string':
                $this->process_get_string($node);
                break;
            case 'get_strings':
                if ($this->showwarnings) {
                    cli_problem("get_strings() not implemented yet! PR welcome.");
                }
                break;
            case 'get_string_manager':
                if ($this->showwarnings) {
                    cli_problem("get_string_manager() not implemented yet! PR welcome.");
                }
                break;
            case 'print_string':
                // Compatible with get_string();
                $this->process_get_string($node);
                break;
        }
    }

    protected function processMethod(Expr\MethodCall $node) {
        switch ($node->name) {
            case 'string_for_js':
                $this->process_get_string($node);
                break;
            case 'strings_for_js':
                $args = $node->args;
                $value1 = $args[0]->value;
                $value2 = $args[1]->value;
                if (!$value1 instanceof Expr\Array_) {
                    if ($this->showwarnings) {
                        echo "Can't process strings_for_js() with 1st argument different than array: '" .
                            $value1->getType() . "''\n";
                    }
                    return;
                }
                if (!$value2 instanceof Scalar\String_) {
                    if ($this->showwarnings) {
                        echo "Can't process strings_for_js() with 2nd argument different than simple string: '" .
                            $value2->getType() . "''\n";
                    }
                    return;
                }

                foreach ($value1->items as $item) {
                    if (!$item->value instanceof Scalar\String_) {
                        if ($this->showwarnings) {
                            echo "Can't process strings_for_js() with array items different than simple strings: '" .
                                $item->getType() . "''\n";
                        }
                    } else {
                        $this->list[$value2->value][$item->value->value] = true;
                    }
                }
                break;

            case 'addHelpButton':
                $args = $node->args;
                $value1 = $args[1]->value;
                $value2 = null;
                if (isset($args[2])) {
                    $value2 = $args[2]->value;
                }
                if (!$value1 instanceof Scalar\String_) {
                    if ($this->showwarnings) {
                        echo "Can't process addHelpButton() with 2nd argument different than array:\n";
                        $this->printTypeDebug($value2);
                    }
                    return;
                }
                if (!$value2 instanceof Scalar\String_) {
                    if ($this->showwarnings) {
                        echo "Can't process addHelpButton() with 3rd argument different than simple string:\n";
                        $this->printTypeDebug($value2);
                    }
                    return;
                }
                $this->list[$value2->value][$value1->value] = true;
                $this->list[$value2->value][$value1->value . '_help'] = true;
                break;
        }

    }

    protected function process_get_string(Expr $node) {
        $args = $node->args;
        if (count($args) < 1) {
            echo "Invalid get_string invocation - with no arguments.";
            return;
        }

        if (count($args) == 1) {
            $value1 = $args[0]->value;
            if ($value1 instanceof Scalar\String_) {
                /** @var $value1 Scalar\String_ */
                $this->list['moodle'][$value1->value] = true;
            } else {
                if ($this->showwarnings) {
                    echo "Can't process get_string() with 1st argument different than simple string:\n";
                    $this->printTypeDebug($value1);
                }
                return;
            }
        }

        if (count($args) > 1) {
            $value1 = $args[0]->value;
            $value2 = $args[1]->value;
            if (!$value1 instanceof Scalar\String_) {
                if ($this->showwarnings) {
                    echo "Can't process get_string() with 1st argument different than simple string:\n";
                    $this->printTypeDebug($value1);
                }
                return;
            }

            if (!$value2 instanceof Scalar\String_) {
                if ($this->showwarnings) {
                    echo "Can't process get_string() with 2nd argument different than simple string:\n";
                    $this->printTypeDebug($value2);
                }
                return;
            }

            $this->list[$value2->value][$value1->value] = true;
        }

    }

    public function enterNode(Node $node) {
        //echo "entering node type: " . $node->getType() . " class: " . get_class($node) . "\n";

        if ($node instanceof Expr\New_) {
            $this->processClass($node);
        } else if ($node instanceof FuncCall) {
            $this->processFunction($node);
        } else if ($node instanceof Expr\MethodCall) {
            $this->processMethod($node);
        }
    }
}

class DevLangusage extends MooshCommand {
    public function __construct() {
        parent::__construct('langusage', 'dev');

        $this->addArgument('path');

        $this->addOption('l|lang:', 'check if translation in this language exists', null);
        $this->addOption('c|component:', 'search for this component only', null);
        $this->addOption('n|no-warnings', "don't show warnings", null);
    }

    public function execute() {
        // Some variables you may want to use
        //  $this->cwd - the directory where moosh command was executed
        //  $this->mooshDir - moosh installation directory
        //  $this->expandedOptions - commandline provided options, merged with defaults
        //  $this->topDir - top Moodle directory
        //  $this->arguments[0] - first argument passed
        //  $this->pluginInfo - array with information about the current plugin (based on cwd), keys:'type','name','dir'
        //  $this->verbose - if set to true, then "moosh -v" was run - add more verbose / debug information

        $options = $this->expandedOptions;
        $code = <<<'CODE'
<?php
new other_class();

/*
get_string("one");
get_string("one", "two");
get_string($one);
$PAGE->set_title(get_string('pluginname', 'local_type') . ' ' . $course->shortname);
abort_message(get_string('error:explicitconfigphp', 'local_datacleaner'), '');
$modname = get_string('onetwo', 'local_module');
$OUTPUT->pix_icon("t/download", get_string('download'));
echo "get_string('fake','call')";
print_string("one1", "two1");
new other_class('aa');
new lang_string('fromcore');
new lang_string('showchildrendesc', 'block_course_overview');
$settings->add(new admin_setting_configcheckbox('block_course_overview/showwelcomearea', new lang_string('showwelcomearea', 'block_course_overview')));

$this->page->requires->string_for_js(
    'js1',
    'js2',
    block_xxx()
);
$this->page->requires->strings_for_js(array(
    'item1', 'item2', 
), 'js2');
*/
CODE;
        //$this->parseCode($code);die('ok');

        $files = [];
        $path = $this->arguments[0];

        if (is_file($this->cwd . '/' . $path)) {
            $files = [$this->cwd . '/' . $path];
        } else if (is_dir($this->cwd . '/' . $path)) {
            $files = $this->getFilesFromDir($this->cwd . '/' . $path);
        } else {
            cli_error("Can not find " . $this->cwd . '/' . $path);
        }

        foreach ($files as $file) {
            if ($this->verbose) {
                echo "Processing $file... ";
            }
            $langstrings = $this->parseCode(file_get_contents($file));
            if ($this->verbose) {
                echo count($langstrings, COUNT_RECURSIVE) . " strings found";
                echo "\n";
            }

            // With all the strings gathered, check if they exist.
            $manager = get_string_manager();
            foreach ($langstrings as $component => $componentstrings) {
                // Skip check if it's a component we don't care about.
                if ($options['component'] && $component != $options['component']) {
                    continue;
                }

                foreach ($componentstrings as $componentstring => $ignore) {

                    echo "Checking $component/$componentstring... ";

                    if (!$options['lang']) {
                        $exists = $manager->string_exists($componentstring, $component);
                    } else {
                        // Checking against particular lang file - eg different language.
                        $string = [];
                        if ($this->verbose) {
                            echo "Including " . $this->cwd . '/' . $options['lang'] . "\n";
                        }
                        include($this->cwd . '/' . $options['lang']);
                        $exists = isset($options['lang']);
                        //$foreignstrings = $manager->load_component_strings($component, $options['lang'], true, false);
                        //$exists = isset($foreignstrings[$componentstring]);
                    }

                    if ($exists) {
                        echo "OK\n";
                    } else {
                        echo "missing!\n";
                    }

                }
            }

        }

    }

    private function parseCode(string $code) {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse($code);
        } catch (Error $error) {
            echo "Parse error: {$error->getMessage()}\n";
            return;
        }

        $traverser = new NodeTraverser();
        $visitor = new LangFinder();
        $traverser->addVisitor($visitor);
        $ast = $traverser->traverse($ast);

        //$dumper = new NodeDumper; echo $dumper->dump($ast) . "\n";

        return $visitor->list;
    }

    private function getFilesFromDir(string $dir) {
        $finder = new Finder();
        $files = [];
        $iterator = $finder
                ->files()
                ->name('*.php')
                ->in($dir);
        foreach ($iterator as $file) {
            $files[] = $file->getRealpath();
        }

        return $files;
    }
}
