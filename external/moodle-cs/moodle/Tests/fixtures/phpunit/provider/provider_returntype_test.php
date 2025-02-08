<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

class provider_returntype_test extends base_test {
    /**
     * @dataProvider provider_no_return
     */
    public function test_one(): void {
        // Nothing to test.
    }

    public static function provider_no_return() {
        return [];
    }

    /**
     * @dataProvider provider_wrong_return
     */
    public function test_two(): void {
        // Nothing to test.
    }

    public static function provider_wrong_return(): \stdClass {
        return (object) [];
    }

    /**
     * @dataProvider provider_returns_generator
     */
    public function test_three(): void {
        // Nothing to test.
    }

    public static function provider_returns_generator(): \Generator {
        yield [
            'a',
        ];
    }

    /**
     * @dataProvider provider_returns_iterator
     */
    public function test_four(): void {
        // Nothing to test.
    }

    public static function provider_returns_iterator(): \Iterator {
        yield [
            'a',
        ];
    }

}
