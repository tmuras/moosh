// phpcs:set moodle.PHPUnit.TestCaseProvider autofixStaticProviders true
<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

class complex_provider_test extends base_test {
    /**
     * @dataProvider provider
     */
    public function test_one(): void {
        // Nothing to test.
    }

    /**
     * @dataProvider second_provider
     */
    public function test_two(): void {
        // Nothing to test.
    }

    public function second_provider(): array {
        return [];
    }
}

class some_other_class extends some_class {
}

class yet_other_class {
}

class different_provider_test extends base_test {
    public function provider(): array {
        return [];
    }

    public function second_provider(): array {
        return [];
    }

    public function another_method(): array {
        return $this->second_provider();
    }
}
