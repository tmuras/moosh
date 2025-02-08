<?php
namespace local_codechecker;
defined("MOODLE_INTERNAL") || die(); // Make this always the 1st line in all CS fixtures.

/**
 * Class which is an abstract testcase.
 */
abstract class testcasenames_with_abstract_test extends base_test {
    abstract public function generate_test_data(): array;

    abstract public function generate_example_data();

    abstract public function test_something(): void;
}
