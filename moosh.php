#!/usr/bin/env php
<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once 'lib/GetOptionKit/Init.php';
require_once 'includes/MooshCommand.php';
require_once 'commands/user/UserCreate.php';
require_once 'commands/user/UserMod.php';
require_once 'commands/user/UserList.php';
require_once 'commands/sql/SqlRun.php';
require_once 'commands/course/CourseCreate.php';
require_once 'commands/course/CourseEnrol.php';
require_once 'commands/role/RoleCreate.php';
require_once 'commands/role/RoleDelete.php';
require_once 'commands/config/ConfigGet.php';
require_once 'commands/config/ConfigSet.php';
require_once 'commands/config/ConfigPlugins.php';
require_once 'commands/file/FileList.php';
require_once 'commands/file/FileDelete.php';
require_once 'commands/file/FilePath.php';

require_once 'includes/functions.php';
require_once 'includes/default_options.php';

use GetOptionKit\ContinuousOptionParser;
use GetOptionKit\OptionSpecCollection;

error_reporting(E_ALL);

define('MOOSH_VERSION', '0.3');

$appspecs = new OptionSpecCollection;
$spec_verbose = $appspecs->add('v|verbose', "be verbose");
$spec_moodle_path = $appspecs->add('p|moodle-path:', "Moodle directory.");

$user_create = new \UserCreate();
$user_mod = new \UserMod();
$user_list = new \UserList();

$course_create = new \CourseCreate();
$course_enrol = new \CourseEnrol();

$role_create = new \RoleCreate();
$role_delete = new \RoleDelete();

$sql_run = new \SqlRun();

$config_get = new \ConfigGet();
$config_set = new \ConfigSet();
$config_plugins = new \ConfigPlugins();

$file_list = new \FileList();
$file_delete = new \FileDelete();
$file_path = new \FilePath();


// subcommand stack
$subcommands = array('user-create' => $user_create, 'user-mod' => $user_mod, 'user-list' => $user_list,
    'role-create' => $role_create, 'role-delete' => $role_delete,
    'course-create' => $course_create, 'course-enrol' => $course_enrol,
    'sql-run' => $sql_run,
    'config-get' => $config_get,'config-set' => $config_set,'config-plugins' => $config_plugins,
    'file-list' => $file_list, 'file-delete' => $file_delete, 'file-path' => $file_path,
);

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

