<?php

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\PHPUnit;

use example;

/**
 * Interface level docblock.
 */
#[example_attribute]
#[with_multiple_attributes, and_another_attribute]

interface interface_only_with_attributes_incorrect_whitespace {
}
