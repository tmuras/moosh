<?php

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\PHPUnit;

defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

/**
 * @category test
 */
class category_valid {}

/**
 * @category core
 */
class category_invalid {}

/**
 * Some docblock without a category.
 */
class category_missing {}

class no_docblock {}
