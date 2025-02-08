<?php

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\PHPUnit;

/**
 * File level docblock.
 */

/**
 * Class level docblock.
 */
class class_with_docblock {
    /**
     * Method level docblock.
     */
    public function method_with_docblock() {}
    public function method_no_docblock() {}

    /**
     * @param string $example
     */
    public function method_with_param_docblock($example) {}
}
class no_docblock {}

/**
 * Description of the interface.
 */
interface int_with_docblock {}
interface int_no_docblock {}

/**
 * Description of the Trait.
 */
trait trait_with_docblock {}
trait trait_no_docblock {}

/**
 * Description of the function.
 */
function function_with_docblock() {}
function function_no_docblock() {}

/**
 * @license
 */
class class_with_docblock_but_no_description {}
/**
 * @license
 */
interface int_with_docblock_but_no_description {}
/**
 * @license
 */
trait trait_with_docblock_but_no_description {}

/**
 * @license
 */
function function_with_docblock_but_no_description() {}

/**
 * @deprecated
 */
function function_with_deprecated_tag() {}
