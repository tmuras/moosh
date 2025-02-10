<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.
// All these are ok.
if ($value === true || $other === true) {
    yes();
}

if ($value === true && $other === true) {
    yes();
}

if ($one === true && ($two === true || $three === true)) {
    yes();
}

$a = 1 || 2 || 3 && 4 && 5;
$a = $a ^ $b;
$a = $a xor $b;

// And these are wrong.
if ($value === true or $other === true) {
    yes();
}

if ($value === true and $other === true) {
    yes();
}

if ($one === true AND ($two === true OR $three === true)) {
    yes();
}

$a = 1 OR 2 or 3 AND 4 and 5;
