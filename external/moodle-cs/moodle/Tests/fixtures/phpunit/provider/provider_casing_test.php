<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

class provider_casing_test extends base_test {
    /**
     * @dataprovider provider
     */
    public function test_one(): void {
        // Nothing to test.
    }

    public static function provider(): array {
        return [];
    }
}
