<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

class correct_setup_teardown_test extends Something {
    public function setUp(): void {
        // Call parent.
        parent::setUp();
    }

    public function tearDown(): void {
        parent::tearDown();
        // Parent called.
    }

    public static function setUpBeforeClass(): void {
        // Call parent.
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass(): void {
        parent::tearDownAfterClass();
        // Parent called.
    }
}

class another_correct_setup_teardown_test extends Something {
    public function ignoredMethod(): void {
         parent::setUp();
         parent::tearDown();
     }
}

class yet_another_correct_setup_teardown_test extends Something {
    public function setUp(): void {
        global $CFG;
        parent::someThing();
        parent::setUp();
    }
    public function tearDown(): void {
        parent::setup(); // No case matching, aka, valid method to call.
        parent::tearDown();
    }
    public static function setUpBeforeClass(): void {
        parent::anotherThing();
        parent::setUpBeforeClass();
    }
    public static function tearDownAfterClass(): void {
        parent::tearDownAfterClass();
        parent::yetAnotherThing();
    }
}

class not_test_hence_ignored extends Something {
    public function setUp(): void {
    }

    public function tearDown(): void {
    }

    public static function setUpBeforeClass(): void {
    }

    public static function tearDownAfterClass(): void {
    }
}
