<?php

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\PHPUnit;

/**
 * Class level docblock.
 */
class class_with_anonymous_class_in_method {
    /**
     * Documented method.
     */
    public function test(): string {
        return new class() extends \stdClass {};
    }
}
