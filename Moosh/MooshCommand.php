<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh;

define('MOOSH_CODE_MARKER', '/** MOOSH AUTO-GENERATED */');

/**
 * @var int Generic argument
 */
define('ARG_GENERIC', 0);

/**
 * @var int File or path to existing file
 */
define('ARG_EXISTING_FILENAME', 1);

class MooshCommand {

    /**
     * @var int Do not include config.php at all
     */
    public static $BOOTSTRAP_NONE = 0;

    /**
     * @var int set CLI_SCRIPT, ABORT_AFTER_CONFIG and include config.php
     */
    public static $BOOTSTRAP_CONFIG = 1;

    /**
     * @var int set CLI_SCRIPT and include config.php
     */
    public static $BOOTSTRAP_FULL = 2;

    /**
     * @var int no CLI_SCRIPT, include config.php
     */
    public static $BOOTSTRAP_FULL_NOCLI = 3;

    /**
     * @var int Load only minimum to connect to DB.
     */
    public static $BOOTSTRAP_DB_ONLY = 4;

    /**
     * @var \GetOptionKit\OptionSpecCollection
     */
    public $spec;

    public $options;

    public $arguments;

    protected $name, $group;

    /**
     * How many arguments minimum are required.
     *
     * @var int
     */
    protected $minArguments = 0;

    /**
     * How many arguments max can be provided..
     *
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
     *
     * @var array
     */
    public $finalOptions = array();

    /**
     * Possible information on plugin in a current directory.
     *
     * @var array
     */
    protected $pluginInfo;

    /**
     * Temporary session information.
     *
     * @var array
     */
    protected $session;

    /**
     * After expanding
     *
     * @var array
     */
    public $expandedOptions = array();

    public $verbose = false;

    /**
     * Current working directory
     *
     * @var string
     */
    public $cwd;

    /**
     * Directory relative to the current Moodle root dir.
     *
     * @var string
     */
    public $relativeDir;

    /**
     * Top Moodle installation directory.
     *
     * @var string
     */
    public $topDir;

    /**
     * moosh installation directory.
     *
     * @var string
     */
    public $mooshDir;

    /**
     * Default global options
     *
     * @var array
     */
    public $defaults;

    public function __construct($name, $group = null) {
        $this->spec = new \GetOptionKit\OptionCollection();
        $this->addOption('h|help', 'help information');
        $this->name = $name;
        $this->group = $group;
    }

    public function setPluginInfo($pluginInfo) {
        $this->pluginInfo = $pluginInfo;
    }

    public function getName() {
        if ($this->group) {
            return $this->group . '-' . $this->name;
        } else {
            return $this->name;
        }
    }

    /**
     * Define required argument. Call function again to add another argument.
     *
     * @param string $name
     */
    public function addArgument($name, $type = ARG_GENERIC) {
        $this->minArguments++;
        $this->maxArguments++;
        $this->argumentNames[] = $name;

        if ($type == ARG_EXISTING_FILENAME) {
            if ($name[0] != '/') {
                $name = $this->cwd . DIRECTORY_SEPARATOR . $name;
            }

            if (!file_exists($name)) {
                cli_error("Input file '" . $name . "' does not exist.");
            }

            if (!is_readable($name)) {
                cli_error("Input file '" . $name . "' is not readable.");
            }
        }

    }

    public function addOption($optionSpec, $description = null, $default = null) {
        $option = $this->spec->add($optionSpec, $description);
        if (!$option->long) {
            die("Provide a long option for '$optionSpec'");
        }
        $this->options[$option->long] = $default;
    }

    public function processOptions($defaults) {
        foreach ($this->options as $k => $default) {
            if ($this->verbose) {
                echo "Processing command option '$k''\n";
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
    public function expandOptions() {
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
    public function expandOptionsManually($replacements) {
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

    public function setArguments($arguments) {
        if (count($arguments) < $this->minArguments) {
            echo "Not enough arguments provided. Please specify:\n";
            echo implode(' ', $this->argumentNames);
            echo "\n";
            echo $this->onErrorHelp();
            exit(1);
        }
        if (count($arguments) > $this->maxArguments) {
            echo "Too many argument provided (" . count($arguments) . "), the maximum is: {$this->maxArguments}\n";
            echo $this->onErrorHelp();
            exit(1);
        }
        $this->arguments = $arguments;
    }

    /**
     * Overwrite to display extra information (e.g. help) when error occured (e.g. wrong arguments were given)
     *
     * @return string
     */
    protected function onErrorHelp() {
        return '';
    }

    public function setParsedOptions($parsedOptions) {
        $this->parsedOptions = $parsedOptions;

        //early detect if -h is given, display help and finish processing
        if ($this->parsedOptions->has('help')) {
            $this->printOptions();
            die();
        }
    }

    public function status() {
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

    public function printOptions() {
        echo '*** ' . $this->getName() . " ***\n";
        echo "OPTIONS:\n";
        $printer = new \GetOptionKit\OptionPrinter\ConsoleOptionPrinter;
        echo $printer->render($this->spec);

        echo $this->getArgumentsHelp();

        echo "\n";
    }

    /**
     * Can be overwritten by child classes to provide custom description.
     */
    protected function getArgumentsHelp() {
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
     *
     * @return int
     */
    public function bootstrapLevel() {
        return self::$BOOTSTRAP_FULL;
    }

    /**
     * Does the command require writing to ~/.moosh/ directory?
     *
     * @return int
     */
    public function requireHomeWriteable() {
        return false;
    }

    /**
     * Loads temporary session information from the temp file.
     */
    protected function loadSession() {
        $tmpFile = $this->defaults['global']['tmpfile'];
        if (!file_exists($tmpFile)) {
            $this->session = array();
        } else {
            $this->session = unserialize(file_get_contents($tmpFile));
        }
        return $this->session;
    }

    /**
     * Saves session information to the temp file.
     */
    protected function saveSession() {
        $tmpFile = $this->defaults['global']['tmpfile'];
        file_put_contents($tmpFile, serialize($this->session));
    }

    protected function getLangCategory() {
        if ($this->pluginInfo['type'] == 'mod' || $this->pluginInfo['type'] == 'unknown') {
            $langCategory = $this->pluginInfo['name'];
        } else {
            $langCategory = $this->pluginInfo['type'] . '_' . $this->pluginInfo['name'];
        }

        return $langCategory;
    }

    public function checkPathArg($name) {
        $filepath = '';
        if ($name[0] == '/') {
            $filepath = $name;
        } else {
            $filepath = $this->cwd . '/' . $name;
        }

        if (!file_exists($filepath)) {
            cli_error("File '$filepath' does not exist");
        }

        if (!is_readable($filepath)) {
            cli_error("File '$filepath' is not readable");
        }
        return $filepath;
    }

    public function checkFileArg($name) {
        $filepath = $this->checkPathArg($name);
        if (!is_file($filepath)) {
            cli_error("'$filepath' is not a file");
        }
        if (!is_readable($filepath)) {
            cli_error("'$filepath' is not readable");
        }

        return $filepath;
    }

    public function checkDirArg($name) {
        $filepath = $this->checkPathArg($name);
        if (!is_dir($filepath)) {
            cli_error("'$filepath' is not a directory");
        }
        return $filepath;
    }

    protected function display($data, $json = false, $humanreadable=true) {
        if ($json) {
            echo json_encode($data);
            return;
        }

        if(!$data) {
            return;
        }

        foreach ($data as $k => $v) {
            echo "$k: ";
            if ($humanreadable && is_numeric($v)) {
                echo display_size($v);
            } else {
                echo $v;
            }
            echo "\n";
        }
    }
}
