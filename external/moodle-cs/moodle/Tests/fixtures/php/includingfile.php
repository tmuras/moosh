<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// These are ok.
require('file.php');
require_once('file.php');

// Always put them within parenthesis.
require 'file.php';
require_once 'file.php';

// And no space before the parenthesis either.
require ('file.php');
require_once ('file.php');

// Unconditional includes must be requires.
include('file.php');
include_once('file.php');

// But conditional includes are ok.
if (include('file.php')) {
    $something = 1;
}

// Also assignment includes are ok
$a = include_once('file.php');
