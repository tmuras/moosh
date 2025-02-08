<?php

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\PHPUnit;

defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// Wrong components are not reported because the expected component is needed and we don't know it

/**
 * @package wrong_package
 */
class package_wrong_in_class {
}

/**
 * @package wrong_package
 */
interface package_wrong_in_interface {
}

/**
 * @package wrong_package
 */
interface package_wrong_in_trait {
}

 /**
 * @package wrong_package
 */
function package_wrong_in_function(): void {
}

// All these (missing) continue being reported because the expected component is not needed.

class missing_docblock_in_class {
}

interface missing_docblock_in_interface {
}

trait missing_docblock_in_trait {
}

function missing_docblock_in_function(): void {
    return;
}

