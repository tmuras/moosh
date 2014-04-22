#!/usr/bin/env php
<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use \Moosh\MooshCommand;

$cwd = getcwd();

//try to detect if we are packaged moosh version - e.g. dude where are my libraries
if (file_exists(__DIR__ . '/Moosh')) {
    $moosh_dir = __DIR__;
} elseif (file_exists('/usr/share/moosh')) {
    $moosh_dir = '/usr/share/moosh';
} else {
    die("I can't find my own libraries\n");
}

$loader = require $moosh_dir . '/vendor/autoload.php';
$loader->add('Moosh\\', $moosh_dir);

$options = array('debug' => true, 'optimizations' => 0);

require_once $moosh_dir . '/includes/functions.php';
require_once $moosh_dir . '/includes/default_options.php';

use GetOptionKit\ContinuousOptionParser;
use GetOptionKit\OptionSpecCollection;

error_reporting(E_ALL);

define('MOOSH_VERSION', '0.14');

$appspecs = new OptionSpecCollection;
$spec_verbose = $appspecs->add('v|verbose', "be verbose");
$spec_moodle_path = $appspecs->add('p|moodle-path:', "Moodle directory.");

$parser = new ContinuousOptionParser($appspecs);
$app_options = $parser->parse($argv);

if ($app_options->has('moodle-path')) {
    $top_dir = $app_options['moodle-path']->value;
} else {
    $top_dir = find_top_moodle_dir($cwd);
}

$moodle_version = moosh_moodle_version($top_dir);
$local_dir = home_dir() . DIRECTORY_SEPARATOR . '.moosh';
$viable_versions = moosh_generate_version_list($moodle_version);
$viable_versions[] = 'Generic';
$namespaced_commands = moosh_load_all_commands($moosh_dir, $viable_versions);
$namespaced_commands_extra = moosh_load_all_commands($local_dir, $viable_versions);

if ($namespaced_commands_extra) {
    $namespaced_commands = array_merge($namespaced_commands, $namespaced_commands_extra);
    $loader->set(false, $local_dir);
}

$subcommands = array();
foreach ($namespaced_commands as $command) {
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

// The first argument must be a subcommand.
$subcommand = NULL;
$possible_matches = array();

if (!$parser->isEnd()) {
    $subcommand = $parser->advance();
}


if (!isset($subcommand_specs[$subcommand])) {
    $possible_matches = array();
    foreach ($subcommands as $k => $v) {
        if (strpos($k, $subcommand) !== false) {
            $possible_matches[] = $k;
        }
    }
    if (count($possible_matches) == 1) {
        $subcommand = $possible_matches[0];
    } else {
        $subcommand = NULL;
    }
}

if (!$subcommand && !$possible_matches) {
    echo "moosh version " . MOOSH_VERSION . "\n";
    echo "No command provided, possible commands:\n\t";
    echo implode("\n\t", array_keys($subcommands));
    echo "\n";
    echo "Global options:\n";
    $appspecs->printOptions();
    echo "\n";
    exit(1);
}

if (!$subcommand && $possible_matches) {
    foreach ($possible_matches as $match) {
        echo $match . "\n";
    }
    exit(1);
}

$parser->setSpecs($subcommand_specs[$subcommand]);
$subcommand_options[$subcommand] = $parser->continueParse();

while (!$parser->isEnd()) {
    $arguments[] = $parser->advance();
}

//read config file if available
$moodlerc = NULL;

if (file_exists(home_dir() . DIRECTORY_SEPARATOR . ".mooshrc.php")) {
    $moodlerc = home_dir() . DIRECTORY_SEPARATOR . ".mooshrc.php";
} elseif (file_exists("/etc/moosh/mooshrc.php")) {
    $moodlerc = "/etc/moosh/mooshrc.php";
} elseif (file_exists(home_dir() . DIRECTORY_SEPARATOR . "mooshrc.php")) {
    $moodlerc = home_dir() . DIRECTORY_SEPARATOR . "mooshrc.php";
}

$options = NULL;
if ($moodlerc) {
    if (isset($app_options['verbose'])) {
        echo "Using '$moodlerc' as moosh runtime configuration file\n";
    }
    $options = array();
    require($moodlerc);
    $options = array_merge_recursive_distinct($defaultOptions, $options);
} else {
    $options = $defaultOptions;
}

/**
 * @var Moosh\MooshCommand $subcommand
 *
 */
$subcommand = $subcommands[$subcommand];


if ($subcommand->bootstrapLevel()) {
    define('CLI_SCRIPT', true);
    if ($subcommand->bootstrapLevel() == MooshCommand::$BOOTSTRAP_CONFIG) {
        define('ABORT_AFTER_CONFIG', true);
    }
    if (!$top_dir) {
        echo "Could not find Moodle installation!\n";
        exit(1);
    }
    require_once($top_dir . '/config.php');

    //gather more info based on the directory where moosh was run
    $relative_dir = substr($cwd, strlen($top_dir));
    $relative_dir = trim($relative_dir, '/');
    if ($app_options->has('verbose')) {
        echo "Top Moodle dir: $top_dir\n";
        echo "Current working dir: " . $cwd . "\n";
        echo "Relative Moodle dir: $relative_dir\n";
    }
    $plugin_info = detect_plugin($relative_dir);
    $subcommand->setPluginInfo($plugin_info);
    $subcommand->topDir = $top_dir;
    $subcommand->relativeDir = $relative_dir;

    //set up debugging
    @error_reporting(E_ALL | E_STRICT);
    @ini_set('display_errors', '1');
    $CFG->debug = (E_ALL | E_STRICT);
    $CFG->debugdisplay = 1;

    //by default set up $USER to admin user


}

if ($app_options->has('verbose')) {
    $subcommand->verbose = true;
}

$subcommand->cwd = $cwd;
$subcommand->mooshDir = $moosh_dir;
$subcommand->defaults = $options;

//process the arguments
$subcommand->setParsedOptions($subcommand_options[$subcommand->getName()]);
$subcommand->setArguments($arguments);
$subcommand->processOptions($options);
$subcommand->expandOptions();

//some more debug if requested
if ($app_options->has('verbose')) {
    $subcommand->status();
}

//execute the actual logic
$subcommand->execute();
