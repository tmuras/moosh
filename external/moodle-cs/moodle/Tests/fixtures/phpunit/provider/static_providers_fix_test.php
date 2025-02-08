// phpcs:set moodle.PHPUnit.TestCaseProvider autofixStaticProviders true
<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// A class with 3 methods, using all the covers options correctly.

/**
 * @coversDefaultClass \some\namespace\one
 * @covers ::all()
 */
class static_providers_test extends base_test {
    /**
     * @dataProvider fixable_provider
     */
    public function test_fixable(): void {
        // Nothing to test.
    }

    public function fixable_provider(): array {
        return [];
    }

    /**
     * @dataProvider unfixable_provider
     */
    public function test_unfixable_provider(): void {
        // Nothing to test.
    }

    public function unfixable_provider(): array {
        return $this->provider();
    }

    /**
     * @dataProvider partially_fixable_provider
     */
    public function test_partially_fixable(): void {
        // Nothing to test.
    }

    public function partially_fixable_provider(): array {
        $foo = $this->call_something();
        $foo->bar();
        $foo();
        $this();
        $this;
        return $this->fixable_provider();
    }
}
