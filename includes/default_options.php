<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//used in the code templates as default author
$defaultOptions['global']['author'] = '<author>';

//path to temporary file
$defaultOptions['global']['tmpfile'] = '/tmp/moosh.tmp';

$defaultOptions['user']['firstname'] = '%s';
$defaultOptions['user']['lastname'] = '%s';
$defaultOptions['user']['password'] = "a";
$defaultOptions['user']['email'] = "%s@moodle.org";
$defaultOptions['user']['country'] = 'PL';
$defaultOptions['user']['city'] = 'Szczecin'; //my home city - why not !? ;)

$defaultOptions['role']['name'] = '%s';
$defaultOptions['role']['description'] = '%s';

$defaultOptions['course']['fullname'] = '%s';
$defaultOptions['course']['description'] = '%s';
$defaultOptions['course']['visible'] = 1;
$defaultOptions['course']['category'] = 1;


$defaultOptions['course']['role'] = 'student';


//use xdotool to automate some of the tasks
$defaultOptions['global']['xdotool'] = false;
$defaultOptions['global']['browser_string'] = 'Mozilla Firefox';
