<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

class provider_prefix_test extends base_test {
    /**
     * @dataProvider test_provider
     */
    public function test_one(): void {
        // Nothing to test.
    }

    public static function test_provider(): array {
        return [];
    }
}
