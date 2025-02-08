<?php

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\PHPUnit;

defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// Testcases should always be abstract.
class example_testcase extends \advanced_testcase {
}

abstract class abstract_example_testcase extends \advanced_testcase {
}

// Test classes cannot be abstract.
abstract class example_abstract_test_with_abstract_children extends \advanced_testcase {
    abstract public function test_something();
}

abstract class example_abstract_test extends \advanced_testcase {
}

// A regular test should be final.
class example_standard_test extends \advanced_testcase {
}

// A final test is already final.
final class example_final_test extends \advanced_testcase {
}

class not_a_test_class {
}
