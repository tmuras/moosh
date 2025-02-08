<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

class provider_not_found_test extends base_test {
    /**
     * @dataProvider provider
     */
    public function test_one(): void {
        // Nothing to test.
    }

    /**
     * @dataProvider
     */
    public function test_two(): void {
        // Nothing to test.
    }
}
