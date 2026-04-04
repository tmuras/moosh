#!/usr/bin/php
<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require __DIR__ . '/vendor/autoload.php';

use Moosh2\Application;

$app = new Application();
$app->run();
