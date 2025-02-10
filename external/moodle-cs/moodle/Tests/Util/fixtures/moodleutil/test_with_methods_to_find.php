<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

class test_with_methods_to_find extends base_test {
    public function instance_method(): array {
        return [];
    }
    protected function protected_method(): array {
        return [];
    }
    private function private_method(): array {
        return [];
    }
    public static function static_method(): array {
        return [];
    }
    protected static function protected_static_method(): array {
        return [];
    }
    private static function private_static_method(): array {
        return [];
    }
}
