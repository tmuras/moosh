<?php

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\PHPUnit;

defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

/**
 * @package local_codechecker
 * @return string
 */
function package_test(): string {
    return 'test';
}

/**
 * @return string
 */
function package_missing(): string {
    return 'test';
}

/**
 * @package local_codechecker
 */
class package_present {
}

/**
 * Package is absent.
 */
class package_absent {
}

function missing_docblock_in_function(): void {
    return;
}

class missing_docblock_in_class {
}

/**
 * @package wrong_package
 */
function package_wrong_in_function(): void {
}

/**
 * @package wrong_package
 */
class package_wrong_in_class {
}

/**
 * @package local_codechecker
 * @package some_other
 */
function package_multiple_in_function(): void {
}

/**
 * @package local_codechecker
 * @package some_other
 */
class package_multiple_in_class {
}

/**
 * @package local_codecheckers
 * @package some_other
 */
function package_multiple_in_function_all_wrong(): void {
}

/**
 * @package local_codecheckers
 * @package some_other
 */
class package_multiple_in_class_all_wrong {
}

/**
 * @package local_codecheckers
 * @package some_other
 */
interface package_multiple_in_interface_all_wrong {
}

/**
 * @package local_codecheckers
 * @package some_other
 */
trait package_multiple_in_trait_all_wrong {
}

interface missing_docblock_interface {
}

/**
 * Missing package
 */
interface missing_package_interface {
}

/**
 * Incorrect package.
 * @package local_codecheckers
 */
interface incorrect_package_interface {
}

/**
 * Correct package.
 * @package local_codechecker
 */
interface correct_package_interface {
}

trait missing_docblock_trait {
}

/**
 * Missing package
 */
trait missing_package_trait {
}

/**
 * Incorrect package.
 * @package local_codecheckers
 */
trait incorrect_package_trait {
}

/**
 * Correct package.
 * @package local_codechecker
 */
trait correct_package_trait {
}

/**
 * @package local_codechecker
 */
class example_class_with_content {
    /**
     * Some method.
     */
    public static function test_method(): void {
    }

    public function test_method2(): void {
    }
}

/**
 * @package local_codechecker
 */
interface example_interface_with_content {
    public function test_method(): void;
}

/**
 * @package local_codechecker
 */
trait example_trait_with_content {
    public function test_method(): void {
    }
}

class example_extends extends example_class_with_content {
    public function test_method(): void {
    }
}

class example_implements implements example_interface_with_content {
    public function test_method(): void {
    }

    /**
     * Found method.
     */
    public function test_something_else(): void {
    }
}
