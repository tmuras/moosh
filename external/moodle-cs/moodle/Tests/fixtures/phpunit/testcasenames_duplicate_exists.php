<?php
namespace local_codechecker;
defined("MOODLE_INTERNAL") || die(); // Make this always the 1st line in all CS fixtures.

/**
 * Correct class but with name not matching the file name.
 */
class testcasenames_duplicate_exists extends \local_codechecker_test {
    public function test_something() {
    }
}
