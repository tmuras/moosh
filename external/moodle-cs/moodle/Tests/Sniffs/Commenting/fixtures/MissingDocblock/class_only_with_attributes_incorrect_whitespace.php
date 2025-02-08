<?php

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\PHPUnit;

use example;

/**
 * Class level docblock.
 */
#[example_attribute]
#[with_multiple_attributes, and_another_attribute]

class class_only_with_attributes_incorrect_whitespace {
    /**
     * Method level docblock.
     */
    #[example_attribute]
    #[with_multiple_attributes, and_another_attribute]

    function method_only_with_attributes_incorrect_whitespace(): void {
    }
}
