#!/usr/bin/env php

<?php
require __DIR__ . '/lib/GetOptionKit/Init.php';
require 'includes/MooshCommand.php';
require 'commands/user/UserCreate.php';
require 'commands/course/CourseCreate.php';
require 'commands/course/CourseEnrol.php';
require 'commands/role/RoleCreate.php';
require 'commands/role/RoleDelete.php';
require 'includes/functions.php';

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

// subcommand stack
$subcommands = array('user-create' => $user_create,
    'role-create' => $role_create, 'role-delete' => $role_delete,
    'course-create' => $course_create,'course-enrol' => $course_enrol,
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
    echo implode("\n\t", $subcommands);
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
    //require($moodlerc);
    //for dev always use default_options.php
    require_once('includes/default_options.php');

}


/*
echo "RC options:\n";
print_r($options);

echo "Global options:\n";
print_r(array_keys($app_options->keys));

echo "Command: $subcommand\n";

echo "Command options:\n";
//var_dump($subcommand_options[$subcommand]);
//var_dump($subcommand_options[$subcommand]['password']->value);
//die();
print_r(array_keys($subcommand_options[$subcommand]->keys));

echo "Arguments:\n";
print_r($arguments);
*/
/**
 * @var MooshCommand $subcommand
 *
 */
$subcommand = $subcommands[$subcommand];
$subcommand->setParsedOptions($subcommand_options[$subcommand->getName()]);
$subcommand->setArguments($arguments);


//echo "User home dir: " . home_dir() . "\n";



if ($subcommand->isBootstraped()) {
    define('CLI_SCRIPT', true);
    if ($app_options->has('moodle-path')) {
        require_once($app_options['moodle-path']->value . DIRECTORY_SEPARATOR . 'config.php');
    } else {
        require_once('config.php');
    }
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

