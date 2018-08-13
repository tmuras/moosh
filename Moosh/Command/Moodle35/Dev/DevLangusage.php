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

class LangFinder extends NodeVisitorAbstract {
    public $list = [];

    protected function processClass(Expr\New_ $node) {
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
                echo "Can't process get_string() with argument different than simple string: '" .
                        $value1->getType() . "''\n";
                return;
            }
        }

        if (count($args) > 1) {
            $value1 = $args[0]->value;
            $value2 = $args[1]->value;
            if (!$value1 instanceof Scalar\String_) {
                echo "Can't process get_string() with argument different than simple string: '" .
                        $value1->getType() . "''\n";
                return;
            }

            if (!$value2 instanceof Scalar\String_) {
                echo "Can't process get_string() with argument different than simple string: '" .
                        $value2->getType() . "''\n";
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
                warn("get_strings() not implemented yet! PR welcome.");
                break;
            case 'get_string_manager':
                warn("get_string_manager() not implemented yet! PR welcome.");
                break;


            case 'print_string':
                // Compatible with get_string();
                $this->process_get_string($node);
                break;
        }

    }

    protected function process_get_string(FuncCall $node) {
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
                echo "Can't process get_string() with argument different than simple string: '" .
                        $value1->getType() . "''\n";
                return;
            }
        }

        if (count($args) > 1) {
            $value1 = $args[0]->value;
            $value2 = $args[1]->value;
            if (!$value1 instanceof Scalar\String_) {
                echo "Can't process get_string() with argument different than simple string: '" .
                        $value1->getType() . "''\n";
                return;
            }

            if (!$value2 instanceof Scalar\String_) {
                echo "Can't process get_string() with argument different than simple string: '" .
                        $value2->getType() . "''\n";
                return;
            }

            $this->list[$value2->value][$value1->value] = true;
        }

    }

    public function enterNode(Node $node) {
        echo "entering node type: " . $node->getType() . " class: " . get_class($node) . "\n";

        if ($node instanceof Expr\New_) {
            $this->processClass($node);
        } else if ($node instanceof FuncCall) {
            $this->processFunction($node);
        }
        // TODO: Expr_MethodCall
    }
}

class DevLangusage extends MooshCommand {
    public function __construct() {
        parent::__construct('langusage', 'dev');

        //$this->addArgument('name');

        //$this->addOption('t|test', 'option with no value');
        //$this->addOption('o|option:', 'option with value and default', 'default');

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

        /* if verbose mode was requested, show some more information/debug messages
        if($this->verbose) {
            echo "Say what you're doing now";
        }
        */

        // Find all get_string function invocations.
        // If component empty, then = moodle.

        $code = <<<'CODE'
<?php
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
new lang_string('fromcore');
new lang_string('showchildrendesc', 'block_course_overview');
$settings->add(new admin_setting_configcheckbox('block_course_overview/showwelcomearea', new lang_string('showwelcomearea', 'block_course_overview')));
*/

$this->page->requires->string_for_js(
    'js1',
    'js2',
    block_xxx()
);
$this->page->requires->strings_for_js(array(
    'js1',
), 'js2');

CODE;

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
        print_r($visitor->list);

        $dumper = new NodeDumper;
        echo $dumper->dump($ast) . "\n";
    }
}
