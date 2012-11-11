#!/usr/bin/env php
<?php
require_once 'lib/GetOptionKit/Init.php';
require_once 'includes/MooshCommand.php';
require_once 'commands/user/UserCreate.php';
require_once 'commands/user/UserMod.php';
require_once 'commands/sql/SqlRun.php';
require_once 'commands/course/CourseCreate.php';
require_once 'commands/course/CourseEnrol.php';
require_once 'commands/role/RoleCreate.php';
require_once 'commands/role/RoleDelete.php';
require_once 'includes/functions.php';
require_once 'includes/default_options.php';

use GetOptionKit\GetOptionKit;
use GetOptionKit\ContinuousOptionParser;
use GetOptionKit\OptionSpecCollection;

error_reporting(E_ALL);

$appspecs = new OptionSpecCollection;
$spec_verbose = $appspecs->add('v|verbose');
$spec_moodle_path = $appspecs->add('p|moodle-path:');
$spec_moodle_path->setDescription("Path to Moodle installation directory.");

$cmdspecs = new OptionSpecCollection;
$cmdspecs->add('s', 'short option name only.');


$user_create = new \UserCreate();

$course_create = new \CourseCreate();
$course_enrol = new \CourseEnrol();
$role_create = new \RoleCreate();
$role_delete = new \RoleDelete();
$user_mod = new \UserMod();
$sql_run = new \SqlRun();

// subcommand stack
$subcommands = array('user-create' => $user_create,'user-mod' => $user_mod,
    'role-create' => $role_create, 'role-delete' => $role_delete,
    'course-create' => $course_create,'course-enrol' => $course_enrol,
    'sql-run' => $sql_run,
);

$subcommand_specs = array();
foreach($subcommands as $k=>$v) {
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
    echo "No command provided, possible commands:\n\t";
    echo implode("\n\t", array_keys($subcommands));
    echo "\n";
    die(1);
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
        require_once('config.php');
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

