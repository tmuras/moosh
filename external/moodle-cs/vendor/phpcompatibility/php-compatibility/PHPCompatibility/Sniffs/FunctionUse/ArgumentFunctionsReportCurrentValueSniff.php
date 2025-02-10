<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2020 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\FunctionUse;

use PHPCompatibility\Helpers\ScannedCode;
use PHPCompatibility\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\BackCompat\BCFile;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\FunctionDeclarations;
use PHPCSUtils\Utils\Operators;
use PHPCSUtils\Utils\PassedParameters;
use PHPCSUtils\Utils\TextStrings;

/**
 * Functions inspecting function arguments report the current parameter value
 * instead of the original since PHP 7.0.
 *
 * `func_get_arg()`, `func_get_args()`, `debug_backtrace()` and exception backtraces
 * will no longer report the original parameter value as was passed to the function,
 * but will instead provide the current value (which might have been modified).
 *
 * PHP version 7.0
 *
 * @link https://www.php.net/manual/en/migration70.incompatible.php#migration70.incompatible.other.func-parameter-modified
 *
 * @since 9.1.0
 */
class ArgumentFunctionsReportCurrentValueSniff extends Sniff
{

    /**
     * A list of functions that, when called, can behave differently in PHP 7
     * when dealing with parameters of the function they're called in.
     *
     * @since 9.1.0
     *
     * @var array<string, true>
     */
    protected $changedFunctions = [
        'func_get_arg'          => true,
        'func_get_args'         => true,
        'debug_backtrace'       => true,
        'debug_print_backtrace' => true,
    ];

    /**
     * Tokens to look out for to allow us to skip past nested scoped structures.
     *
     * @since 9.1.0
     *
     * @var array<string, true>
     */
    private $skipPastNested = [
        'T_CLASS'      => true,
        'T_ANON_CLASS' => true,
        'T_INTERFACE'  => true,
        'T_TRAIT'      => true,
        'T_FUNCTION'   => true,
        'T_CLOSURE'    => true,
    ];

    /**
     * The tokens for variable incrementing/decrementing.
     *
     * @since 9.1.0
     *
     * @var array<int|string, true>
     */
    private $plusPlusMinusMinus = [
        \T_DEC => true,
        \T_INC => true,
    ];

    /**
     * Tokens to ignore when determining the start of a statement for call to one of the functions.
     *
     * @since 9.1.0
     *
     * @var array<int|string>
     */
    private $ignoreForStartOfStatement = [
        \T_COMMA,
        \T_DOUBLE_ARROW,
        \T_OPEN_SQUARE_BRACKET,
        \T_OPEN_PARENTHESIS,
    ];

    /**
     * Tokens to ignore when determining the start of a statement for a used variable.
     *
     * @since 10.0.0
     *
     * @var array<int|string>
     */
    private $ignoreForStartOfStatementVarUse = [
        \T_COMMA,
        \T_DOUBLE_ARROW,
        \T_OPEN_SQUARE_BRACKET,
        \T_OPEN_PARENTHESIS,
        \T_OPEN_SHORT_ARRAY,
    ];

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 9.1.0
     *
     * @return array<int|string>
     */
    public function register()
    {
        return [
            \T_FUNCTION,
            \T_CLOSURE,
        ];
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 9.1.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if (ScannedCode::shouldRunOnOrAbove('7.0') === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['scope_opener'], $tokens[$stackPtr]['scope_closer']) === false) {
            // Abstract function, interface function, live coding or parse error.
            return;
        }

        $scopeOpener = $tokens[$stackPtr]['scope_opener'];
        $scopeCloser = $tokens[$stackPtr]['scope_closer'];

        // Does the function declaration have parameters ?
        $params = FunctionDeclarations::getParameters($phpcsFile, $stackPtr);
        if (empty($params)) {
            // No named arguments found, so no risk of them being changed.
            return;
        }

        $paramNames = [];
        foreach ($params as $param) {
            $paramNames[] = $param['name'];
        }

        for ($i = ($scopeOpener + 1); $i < $scopeCloser; $i++) {
            if (isset($this->skipPastNested[$tokens[$i]['type']]) && isset($tokens[$i]['scope_closer'])) {
                // Skip past nested structures.
                $i = $tokens[$i]['scope_closer'];
                continue;
            }

            if ($tokens[$i]['code'] !== \T_STRING) {
                continue;
            }

            $foundFunctionName = \strtolower($tokens[$i]['content']);

            if (isset($this->changedFunctions[$foundFunctionName]) === false) {
                // Not one of the target functions.
                continue;
            }

            /*
             * Ok, so is this really a function call to one of the PHP native functions ?
             */
            $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($i + 1), null, true);
            if ($next === false || $tokens[$next]['code'] !== \T_OPEN_PARENTHESIS) {
                // Live coding, parse error or not a function call.
                continue;
            }

            $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($i - 1), null, true);
            if ($prev !== false) {
                if (isset(Collections::objectOperators()[$tokens[$prev]['code']])) {
                    continue;
                }

                // Check for namespaced functions, ie: \foo\bar() not \bar().
                if ($tokens[ $prev ]['code'] === \T_NS_SEPARATOR) {
                    $pprev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($prev - 1), null, true);
                    if ($pprev !== false && $tokens[ $pprev ]['code'] === \T_STRING) {
                        continue;
                    }
                }
            }

            /*
             * Address some special cases.
             */
            if ($foundFunctionName !== 'func_get_args') {
                $paramOne = PassedParameters::getParameter($phpcsFile, $i, 1);
                if ($paramOne !== false) {
                    switch ($foundFunctionName) {
                        /*
                         * Check if `debug_(print_)backtrace()` is called with the
                         * `DEBUG_BACKTRACE_IGNORE_ARGS` option.
                         */
                        case 'debug_backtrace':
                        case 'debug_print_backtrace':
                            $hasIgnoreArgs = $phpcsFile->findNext(
                                \T_STRING,
                                $paramOne['start'],
                                ($paramOne['end'] + 1),
                                false,
                                'DEBUG_BACKTRACE_IGNORE_ARGS'
                            );

                            if ($hasIgnoreArgs !== false) {
                                // Debug_backtrace() called with ignore args option.
                                continue 2;
                            }
                            break;

                        /*
                         * Collect the necessary information to only throw a notice if the argument
                         * touched/changed is in line with the passed $arg_num.
                         *
                         * Also, we can ignore `func_get_arg()` if the argument offset passed is
                         * higher than the number of named parameters.
                         *
                         * {@internal Note: This does not take calculations into account!
                         *  Should be exceptionally rare and can - if needs be - be addressed at a later stage.}
                         */
                        case 'func_get_arg':
                            $number = $phpcsFile->findNext(\T_LNUMBER, $paramOne['start'], ($paramOne['end'] + 1));
                            if ($number !== false) {
                                $argNumber = $tokens[$number]['content'];

                                if (isset($paramNames[$argNumber]) === false) {
                                    // Requesting a non-named additional parameter. Ignore.
                                    continue 2;
                                }
                            }
                            break;
                    }
                }
            } else {
                /*
                 * Check if the call to func_get_args() happens to be in an array_slice() or
                 * array_splice() with an $offset higher than the number of named parameters.
                 * In that case, we can ignore it.
                 *
                 * {@internal Note: This does not take offset calculations into account!
                 *  Should be exceptionally rare and can - if needs be - be addressed at a later stage.}
                 */
                if ($prev !== false && $tokens[$prev]['code'] === \T_OPEN_PARENTHESIS) {

                    $maybeFunctionCall = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($prev - 1), null, true);
                    if ($maybeFunctionCall !== false
                        && $tokens[$maybeFunctionCall]['code'] === \T_STRING
                        && ($tokens[$maybeFunctionCall]['content'] === 'array_slice'
                        || $tokens[$maybeFunctionCall]['content'] === 'array_splice')
                    ) {
                        $parentFuncParamTwo = PassedParameters::getParameter($phpcsFile, $maybeFunctionCall, 2);
                        $number             = $phpcsFile->findNext(
                            \T_LNUMBER,
                            $parentFuncParamTwo['start'],
                            ($parentFuncParamTwo['end'] + 1)
                        );

                        if ($number !== false && isset($paramNames[$tokens[$number]['content']]) === false) {
                            // Requesting non-named additional parameters. Ignore.
                            continue ;
                        }

                        // Slice starts at a named argument, but we know which params are being accessed.
                        $paramNamesSubset = \array_slice($paramNames, $tokens[$number]['content']);
                    }
                }
            }

            /*
             * For debug_backtrace(), check if the result is being dereferenced and if so,
             * whether the `args` index is used.
             * I.e. whether `$index` in `debug_backtrace()[$stackFrame][$index]` is a string
             * with the content `args`.
             *
             * Note: We already know that $next is the open parenthesis of the function call.
             */
            if ($foundFunctionName === 'debug_backtrace' && isset($tokens[$next]['parenthesis_closer'])) {
                $afterParenthesis = $phpcsFile->findNext(
                    Tokens::$emptyTokens,
                    ($tokens[$next]['parenthesis_closer'] + 1),
                    null,
                    true
                );

                if ($tokens[$afterParenthesis]['code'] === \T_OPEN_SQUARE_BRACKET
                    && isset($tokens[$afterParenthesis]['bracket_closer'])
                ) {
                    $afterStackFrame = $phpcsFile->findNext(
                        Tokens::$emptyTokens,
                        ($tokens[$afterParenthesis]['bracket_closer'] + 1),
                        null,
                        true
                    );

                    if ($tokens[$afterStackFrame]['code'] === \T_OPEN_SQUARE_BRACKET
                        && isset($tokens[$afterStackFrame]['bracket_closer'])
                    ) {
                        $arrayIndex = $phpcsFile->findNext(
                            \T_CONSTANT_ENCAPSED_STRING,
                            ($afterStackFrame + 1),
                            $tokens[$afterStackFrame]['bracket_closer']
                        );

                        if ($arrayIndex !== false
                            && TextStrings::stripQuotes($tokens[$arrayIndex]['content']) !== 'args'
                        ) {
                            continue;
                        }
                    }
                }
            }

            /*
             * Only check for variables before the start of the statement to
             * prevent false positives on the return value of the function call
             * being assigned to one of the parameters, i.e.:
             * `$param = func_get_args();`.
             */
            $startOfStatement = BCFile::findStartOfStatement($phpcsFile, $i, $this->ignoreForStartOfStatement);

            /*
             * Ok, so we've found one of the target functions in the right scope.
             * Now, let's check if any of the passed parameters were touched.
             */
            $scanResult = 'clean';
            for ($j = ($scopeOpener + 1); $j < $startOfStatement; $j++) {
                if (isset($this->skipPastNested[$tokens[$j]['type']])
                    && isset($tokens[$j]['scope_closer'])
                ) {
                    // Skip past nested structures.
                    $j = $tokens[$j]['scope_closer'];
                    continue;
                }

                // Ignore return, exit and throw statements completely.
                if ($tokens[$j]['code'] === \T_RETURN
                    || $tokens[$j]['code'] === \T_EXIT
                    || $tokens[$j]['code'] === \T_THROW
                ) {
                    $j = BCFile::findEndOfStatement($phpcsFile, $j);
                    continue;
                }

                // Ignore use of any of the passed parameters in isset() or empty().
                if ($tokens[$j]['code'] === \T_ISSET
                    || $tokens[$j]['code'] === \T_EMPTY
                ) {
                    $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($j + 1), null, true);
                    if ($nextNonEmpty !== false
                        && isset($tokens[$nextNonEmpty]['parenthesis_closer'])
                    ) {
                        $j = $tokens[$nextNonEmpty]['parenthesis_closer'];
                        continue;
                    }
                }

                if ($tokens[$j]['code'] !== \T_VARIABLE) {
                    continue;
                }

                if ($foundFunctionName === 'func_get_arg' && isset($argNumber)) {
                    if (isset($paramNames[$argNumber])
                        && $tokens[$j]['content'] !== $paramNames[$argNumber]
                    ) {
                        // Different param than the one requested by func_get_arg().
                        continue;
                    }
                } elseif ($foundFunctionName === 'func_get_args' && isset($paramNamesSubset)) {
                    if (\in_array($tokens[$j]['content'], $paramNamesSubset, true) === false) {
                        // Different param than the ones requested by func_get_args().
                        continue;
                    }
                } elseif (\in_array($tokens[$j]['content'], $paramNames, true) === false) {
                    // Variable is not one of the function parameters.
                    continue;
                }

                $beforeVar                = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($j - 1), null, true);
                $startOfVariableStatement = BCFile::findStartOfStatement(
                    $phpcsFile,
                    $j,
                    $this->ignoreForStartOfStatementVarUse
                );

                /*
                 * Check if this is only a plain assignment. If so, ignore.
                 *
                 * A "plain assignment" for the purposes of this check is defined as:
                 * - Variable is not nested in parenthesis, i.e. not used in a potential function call.
                 * - Not preceded by a reference operator.
                 * - Has an assignment operator before it and none after.
                 */
                if (empty($tokens[$j]['nested_parenthesis']) === true
                    && $beforeVar !== false
                    && Operators::isReference($phpcsFile, $beforeVar) === false
                    && $tokens[$startOfVariableStatement]['code'] === \T_VARIABLE
                ) {
                    $endOfVariableStatement = $phpcsFile->findNext([\T_SEMICOLON, \T_CLOSE_TAG], ($j + 1));
                    $lastAssignmentOperator = $phpcsFile->findPrevious(
                        Tokens::$assignmentTokens,
                        ($endOfVariableStatement - 1),
                        $startOfVariableStatement
                    );
                    if ($lastAssignmentOperator !== false
                        && $lastAssignmentOperator < $j
                    ) {
                        continue;
                    }
                }

                /*
                 * Ok, so we've found a variable which was passed as one of the parameters.
                 * Now, is this variable being changed, i.e. incremented, decremented, unset
                 * or assigned something ?
                 */
                $scanResult = 'warning';
                if (isset($variableToken) === false) {
                    $variableToken = $j;
                }

                if ($beforeVar !== false && isset($this->plusPlusMinusMinus[$tokens[$beforeVar]['code']])) {
                    // Variable is being (pre-)incremented/decremented.
                    $scanResult    = 'error';
                    $variableToken = $j;
                    break;
                }

                $afterVar = $phpcsFile->findNext(Tokens::$emptyTokens, ($j + 1), null, true);
                if ($afterVar === false) {
                    // Shouldn't be possible, but just in case.
                    continue;
                }

                if (isset($this->plusPlusMinusMinus[$tokens[$afterVar]['code']])) {
                    // Variable is being (post-)incremented/decremented.
                    $scanResult    = 'error';
                    $variableToken = $j;
                    break;
                }

                if (empty($tokens[$j]['nested_parenthesis']) === false) {
                    $parentheses = $tokens[$j]['nested_parenthesis'];
                    \end($parentheses);
                    $openParens   = \key($parentheses);
                    $prevNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($openParens - 1), null, true);
                    if ($prevNonEmpty !== false
                        && $tokens[$prevNonEmpty]['code'] === \T_UNSET
                    ) {
                        // Variable is being unset.
                        $scanResult    = 'error';
                        $variableToken = $j;
                        break;
                    }
                }

                if ($tokens[$afterVar]['code'] === \T_OPEN_SQUARE_BRACKET
                    && isset($tokens[$afterVar]['bracket_closer'])
                ) {
                    // Skip past array access on the variable.
                    while (($afterVar = $phpcsFile->findNext(Tokens::$emptyTokens, ($tokens[$afterVar]['bracket_closer'] + 1), null, true)) !== false) {
                        if ($tokens[$afterVar]['code'] !== \T_OPEN_SQUARE_BRACKET
                            || isset($tokens[$afterVar]['bracket_closer']) === false
                        ) {
                            break;
                        }
                    }
                }

                if ($afterVar !== false
                    && isset(Tokens::$assignmentTokens[$tokens[$afterVar]['code']])
                ) {
                    // Variable is being assigned something.
                    $scanResult    = 'error';
                    $variableToken = $j;
                    break;
                }
            }

            unset($argNumber, $paramNamesSubset);

            if ($scanResult === 'clean') {
                continue;
            }

            $error = 'Since PHP 7.0, functions inspecting arguments, like %1$s(), no longer report the original value as passed to a parameter, but will instead provide the current value. The parameter "%2$s" was %4$s on line %3$s.';
            $data  = [
                $foundFunctionName,
                $tokens[$variableToken]['content'],
                $tokens[$variableToken]['line'],
            ];

            if ($scanResult === 'error') {
                $data[] = 'changed';
                $phpcsFile->addError($error, $i, 'Changed', $data);
            } elseif ($scanResult === 'warning') {
                $data[] = 'used, and possibly changed (by reference),';
                $phpcsFile->addWarning($error, $i, 'NeedsInspection', $data);
            }

            unset($variableToken);
        }
    }
}
