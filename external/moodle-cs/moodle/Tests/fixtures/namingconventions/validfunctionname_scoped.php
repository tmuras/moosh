<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

class someclass {
    public function __magiclike() {
        echo 'hi';
    }
    public function __invoke() {
        echo 'hi';
    }
    public function __debuginfo() {
        echo 'hi';
    }
}
