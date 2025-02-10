<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2020 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\ParameterValues;

use PHPCompatibility\AbstractFunctionCallParameterSniff;
use PHPCompatibility\Helpers\ScannedCode;
use PHPCompatibility\Helpers\TokenGroup;
use PHP_CodeSniffer\Files\File;
use PHPCSUtils\Utils\PassedParameters;

/**
 * In PHP 5.2 and lower, the `$initial` parameter for `array_reduce()` had to be an integer.
 *
 * PHP version 5.3
 *
 * @link https://www.php.net/manual/en/migration53.other.php#migration53.other
 * @link https://www.php.net/manual/en/function.array-reduce.php#refsect1-function.array-reduce-changelog
 *
 * @since 9.0.0
 */
class NewArrayReduceInitialTypeSniff extends AbstractFunctionCallParameterSniff
{

    /**
     * Functions to check for.
     *
     * @since 9.0.0
     *
     * @var array<string, true>
     */
    protected $targetFunctions = [
        'array_reduce' => true,
    ];

    /**
     * Tokens which, for the purposes of this sniff, indicate that there is
     * a variable element to the value passed.
     *
     * @since 9.0.0
     *
     * @var array<int|string>
     */
    private $variableValueTokens = [
        \T_VARIABLE,
        \T_STRING,
        \T_SELF,
        \T_PARENT,
        \T_STATIC,
        \T_DOUBLE_QUOTED_STRING,
    ];


    /**
     * Do a version check to determine if this sniff needs to run at all.
     *
     * @since 9.0.0
     *
     * @return bool
     */
    protected function bowOutEarly()
    {
        return (ScannedCode::shouldRunOnOrBelow('5.2') === false);
    }


    /**
     * Process the parameters of a matched function.
     *
     * @since 9.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile    The file being scanned.
     * @param int                         $stackPtr     The position of the current token in the stack.
     * @param string                      $functionName The token content (function name) which was matched.
     * @param array                       $parameters   Array with information about the parameters.
     *
     * @return int|void Integer stack pointer to skip forward or void to continue
     *                  normal file processing.
     */
    public function processParameters(File $phpcsFile, $stackPtr, $functionName, $parameters)
    {
        $targetParam = PassedParameters::getParameterFromStack($parameters, 3, 'initial');
        if ($targetParam === false) {
            return;
        }

        if (TokenGroup::isNumber($phpcsFile, $targetParam['start'], $targetParam['end'], true) !== false) {
            return;
        }

        if (TokenGroup::isNumericCalculation($phpcsFile, $targetParam['start'], $targetParam['end']) === true) {
            return;
        }

        $error = 'Passing a non-integer as the value for $initial to array_reduce() is not supported in PHP 5.2 or lower.';
        if ($phpcsFile->findNext($this->variableValueTokens, $targetParam['start'], ($targetParam['end'] + 1)) === false) {
            $phpcsFile->addError(
                $error . ' Found: %s',
                $targetParam['start'],
                'InvalidTypeFound',
                [$targetParam['clean']]
            );
        } else {
            $phpcsFile->addWarning(
                $error . ' Variable value found. Found: %s',
                $targetParam['start'],
                'VariableFound',
                [$targetParam['clean']]
            );
        }
    }
}
