<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// We accept these as valid functions.
require('file.txt');
require_once('file.txt');
include('file.txt');
include_once('file.txt');
clone($object);

// And, of course, they are also allowed as PHP tokens.
require 'file.txt';
require_once 'file.txt';
include 'file.txt';
include_once 'file.txt';
clone $object;

// But these cannot be used as functions.
abstract($object);
callable($object);
catch($object);
final($object);
instanceof($object);
private($object);
throw($object);
trait($object);
