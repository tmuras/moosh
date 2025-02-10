<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

class someclass {
    protected $okprop;
    public static $okstaticprop;

    $missingprop;
    static $missingstaticprop;

    public function okfunc() {
        $something = 2;
        $anotherthing = "Another {$something}";
    }
}
