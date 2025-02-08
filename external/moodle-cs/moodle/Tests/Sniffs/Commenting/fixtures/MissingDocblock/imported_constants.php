<?php

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\PHPUnit;

use these\dont\actually\need\to\point\to\anything;
use function ns\fun_1;
use function ns\fun_2 as alias;
use const ns\CONST_1;
use const ns\CONST_2 as ALIAS;

use {
    function ns\fun_3,
    const ns\const_3
};

const UNDOCUMENTED_CONST = 1;

/** @var bool A documented bool */
const DOCUMENTED_BOOL = true;

/**
 * Another documented constant which does things.
 *
 * @var int
 */
const DOCUMENTED_INT = 1;

/**
 * Example class.
 */
class example_class {
    const UNDOCUMENTED_CONST = 1;

    /** @var bool A documented bool */
    const DOCUMENTED_BOOL = true;

    /**
     * Another documented constant which does things.
     *
     * @var int
     */
    const DOCUMENTED_INT = 1;

}
