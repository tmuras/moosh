<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class MooshCommand
{
    /**
     * @var \GetOptionKit\OptionSpecCollection
     */
    public $spec;

    public $options;

    public $arguments;

    protected $name, $group;

    /**
     * How many arguments minimum are required.
     * @var int
     */
    protected $minArguments = 0;

    /**
     * How many arguments max can be provided..
     * @var int
     */
    protected $maxArguments = 0;

    /**
     *
     * @var array
     */
    protected $argumentNames = array();

    /**
     * @var \GetOptionKit\OptionResult
     */
    protected $parsedOptions;

    /**
     * Before expanding
     * @var array
     */
    public $finalOptions = array();

    /**
     * After expanding
     * @var array
     */
    public $expandedOptions = array();

    public $verbose = false;

    public function __construct($name, $group = NULL)
    {
        $this->spec = new \GetOptionKit\OptionSpecCollection();
        $this->addOption('h|help', 'help information');
        $this->name = $name;
        $this->group = $group;
    }

    public function getName()
    {
        return $this->group . '-' . $this->name;
    }

    /**
     * Define required argument. Call function again to add another argument.
     * @param string $name
     */
    public function addRequiredArgument($name)
    {
        $this->argumentNames[] = $name;
        $this->minArguments++;
        $this->maxArguments++;
    }

    public function addOption($optionSpec, $description = NULL, $default = NULL)
    {
        $option = $this->spec->add($optionSpec, $description);
        if (!$option->long) {
            die("Provide a long option for '$optionSpec'");
        }
        $this->options[$option->long] = $default;
    }

    public function processOptions($defaults)
    {
        foreach ($this->options as $k => $default) {
            if ($this->verbose) {
                echo "Processing option '$k''\n";
            }

            $compiled_options[$k] = $default;
            if ($this->group && isset($defaults[$this->group][$k])) {
                $compiled_options[$k] = $defaults[$this->group][$k];
                if ($this->verbose) {
                    echo "'$k' option is set in the RC group defaults '" . $defaults[$this->group][$k] . "'\n";
                }
            }

            if (isset($defaults[$this->group . '-' . $this->name][$k])) {
                $compiled_options[$k] = $defaults[$this->group . '-' . $this->name][$k];
                if ($this->verbose) {
                    echo "'$k' option is set in the RC name defaults '" . $defaults[$this->group . '-' . $this->name][$k] . "'\n";
                }
            }

            if ($this->parsedOptions->has($k)) {
                $compiled_options[$k] = $this->parsedOptions[$k]->value;
                if ($this->verbose) {
                    echo "'$k' option is set on the command line to '" . $this->parsedOptions[$k]->value . "'\n";
                }
            }

            //we need to remember options before they were expanded
            $this->finalOptions[$k] = $compiled_options[$k];
        }
    }

    /**
     * Make the special replacements of %s in the options
     */
    public function expandOptions()
    {
        //first copy the options
        $this->expandedOptions = $this->finalOptions;

        foreach ($this->arguments as $arg) {
            //process all options
            //TODO handle %%
            $current_options = array();
            foreach ($this->expandedOptions as $k => $v) {
                $expanded = str_replace('%s', $arg, $v);
                if ($this->verbose && $v != $expanded) {
                    echo "'$k' expanded from '$v' to '$expanded'\n";
                }
                $this->expandedOptions[$k] = $expanded;
            }
        }
    }

    /**
     * Make the special replacements of %s in the options with custom list of arguments
     */
    public function expandOptionsManually($replacements)
    {
        //first copy the options
        $this->expandedOptions = $this->finalOptions;

        foreach ($replacements as $arg) {
            //process all options
            //TODO handle %%
            foreach ($this->expandedOptions as $k => $v) {
                $expanded = str_replace('%s', $arg, $v);
                if ($this->verbose && $v != $expanded) {
                    echo "'$k' manually expanded from '$v' to '$expanded'\n";
                }
                $this->expandedOptions[$k] = $expanded;
            }
        }
    }

    public function setArguments($arguments)
    {
        if (count($arguments) < $this->minArguments) {
            echo "Not enough arguments provided. Please specify:\n";
            echo implode(' ', $this->argumentNames);
            echo "\n";
            exit(1);
        }
        if (count($arguments) > $this->maxArguments) {
            echo "Too many argument provided (" . count($arguments) . "), the maximum is: {$this->maxArguments}\n";
            exit(1);
        }
        $this->arguments = $arguments;
    }

    public function setParsedOptions($parsedOptions)
    {
        $this->parsedOptions = $parsedOptions;

        //early detect if -h is given, display help and finish processing
        if ($this->parsedOptions->has('help')) {
            $this->printOptions();
            die();
        }
    }

    public function status()
    {
        //print my name & group
        echo "Command: {$this->name} ($this->group)\n";

        //print my options
        echo "Options:\n";
        foreach ($this->options as $k => $default) {
            echo "\t$k ($default): '" . $this->expandedOptions[$k] . "'\n";
        }

        //print my arguments
        echo "Arguments:\n";
        echo "\t" . implode(' ', $this->arguments) . "\n";
    }

    public function printOptions()
    {
        echo '*** ' . $this->getName() . " ***\n";
        echo "OPTIONS:\n";
        $this->spec->printOptions();

        echo $this->getArgumentsHelp();

        echo "\n";
    }

    /**
     * Can be overwritten by child classes to provide custom description.
     */
    protected function getArgumentsHelp()
    {
        if (!count($this->argumentNames)) {
            return '';
        }

        $ret = "\n\nARGUMENTS:";
        $ret .= "\n\t";

        $ret .= implode(' ', $this->argumentNames);
        if (count($this->argumentNames) < $this->maxArguments) {
            $ret .= " ...\n";
        }
        return $ret;
    }

    /**
     * Should command be bootstrapped as CLI_SCRIPT and include config.php?
     * @return bool
     */
    public function isBootstraped()
    {
        return true;
    }

}
