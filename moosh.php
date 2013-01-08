#!/usr/bin/env php
<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require 'vendor/autoload.php';
require_once 'includes/MooshCommand.php';

//load all commands
$all_commands = array();
foreach (glob(__DIR__ . '/commands/*', GLOB_ONLYDIR) as $dir) {
    foreach (glob("$dir/*php") as $file) {
        $all_commands[] = substr(basename($file), 0, -4);
        require_once($file);
    }
}

require_once 'includes/functions.php';
require_once 'includes/default_options.php';

use GetOptionKit\ContinuousOptionParser;
use GetOptionKit\OptionSpecCollection;

error_reporting(E_ALL);

define('MOOSH_VERSION', '0.4');

$appspecs = new OptionSpecCollection;
$spec_verbose = $appspecs->add('v|verbose', "be verbose");
$spec_moodle_path = $appspecs->add('p|moodle-path:', "Moodle directory.");

$all_objects = array();
$subcommands = array();
foreach($all_commands as $command) {
    $object = new $command();
    $subcommands[$object->getName()] = $object;
}

$subcommand_specs = array();
foreach ($subcommands as $k => $v) {
    $subcommand_specs[$k] = $v->spec;
}

// for saved options
$subcommand_options = array();

// command arguments
$arguments = array();

$parser = new ContinuousOptionParser($appspecs);
$app_options = $parser->parse($argv);
$subcommand = NULL;
while (!$parser->isEnd()) {
    if (isset($subcommand_specs[$parser->getCurrentArgument()])) {
        $subcommand = $parser->advance();
        $parser->setSpecs($subcommand_specs[$subcommand]);
        $subcommand_options[$subcommand] = $parser->continueParse();
    } else {
        $arguments[] = $parser->advance();
    }
}

if (!$subcommand) {
    echo "moosh version " . MOOSH_VERSION . "\n";
    echo "No command provided, possible commands:\n\t";
    echo implode("\n\t", array_keys($subcommands));
    echo "\n";
    echo "Global options:\n";
    $appspecs->printOptions();
    echo "\n";
    exit(1);
}

//read config file if available
$moodlerc = NULL;

if (file_exists(home_dir() . DIRECTORY_SEPARATOR . ".mooshrc.php")) {
    $moodlerc = home_dir() . DIRECTORY_SEPARATOR . ".mooshrc.php";
} elseif (file_exists("/etc/moosh/mooshrc.php")) {
    $moodlerc = "/etc/moosh/mooshrc.php";
} elseif (file_exists(home_dir() . DIRECTORY_SEPARATOR . "mooshrc.php")) {
    $moodlerc = home_dir() . DIRECTORY_SEPARATOR . "mooshrc.php";
} else {
    require_once('includes/default_options.php');
}

$options = NULL;
if ($moodlerc) {
    if (isset($app_options['verbose'])) {
        echo "Using '$moodlerc' as moosh runtime configuration file\n'";
    }
    require($moodlerc);
    $options = array_merge($defaultOptions, $options);
} else {
    $options = $defaultOptions;
}

/**
 * @var MooshCommand $subcommand
 *
 */
$subcommand = $subcommands[$subcommand];
$subcommand->setParsedOptions($subcommand_options[$subcommand->getName()]);
$subcommand->setArguments($arguments);

if ($subcommand->isBootstraped()) {
    define('CLI_SCRIPT', true);
    if ($app_options->has('moodle-path')) {
        require_once($app_options['moodle-path']->value . DIRECTORY_SEPARATOR . 'config.php');
    } else {
        //find config.php in current or higher level directories
        if (file_exists('config.php')) {
            require_once('config.php');
        } elseif (file_exists('../config.php')) {
            require_once('../config.php');
        } elseif (file_exists('../../config.php')) {
            require_once('../../config.php');
        } elseif (file_exists('../../../config.php')) {
            require_once('../../../config.php');
        }
    }
    @error_reporting(E_ALL | E_STRICT);
    @ini_set('display_errors', '1');
    $CFG->debug = (E_ALL | E_STRICT);
    $CFG->debugdisplay = 1;
}

if ($app_options->has('verbose')) {
    $subcommand->verbose = true;
}

//process the arguments
$subcommand->processOptions($options);
$subcommand->expandOptions();

//some more debug if requested
if ($app_options->has('verbose')) {
    $subcommand->status();
}

//execute the actual logic
$subcommand->execute();

