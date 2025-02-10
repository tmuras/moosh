<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

class correct_test extends base_test {
    /**
     * Test without a provider
     */
    public function test_one(): void {
        // Nothing to test.
    }

    /**
     * @dataProvider provider
     */
    public function test_two(): void {
        // Nothing to test.
    }

    public static function provider(): array {
        return [];
    }
}
