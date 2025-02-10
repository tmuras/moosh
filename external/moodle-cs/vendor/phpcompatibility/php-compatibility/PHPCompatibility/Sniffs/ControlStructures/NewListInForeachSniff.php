<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2020 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\ControlStructures;

use PHPCompatibility\Helpers\ScannedCode;
use PHPCompatibility\Sniff;
use PHP_CodeSniffer\Files\File;

/**
 * Detect unpacking nested arrays with `list()` in a `foreach()` as available since PHP 5.5.
 *
 * PHP version 5.5
 *
 * @link https://www.php.net/manual/en/migration55.new-features.php#migration55.new-features.foreach-list
 * @link https://wiki.php.net/rfc/foreachlist
 * @link https://www.php.net/manual/en/control-structures.foreach.php#control-structures.foreach.list
 *
 * @since 9.0.0
 */
class NewListInForeachSniff extends Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 9.0.0
     *
     * @return array<int|string>
     */
    public function register()
    {
        return [\T_FOREACH];
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 9.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if (ScannedCode::shouldRunOnOrBelow('5.4') === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['parenthesis_opener'], $tokens[$stackPtr]['parenthesis_closer']) === false) {
            return;
        }

        $opener = $tokens[$stackPtr]['parenthesis_opener'];
        $closer = $tokens[$stackPtr]['parenthesis_closer'];

        $asToken = $phpcsFile->findNext(\T_AS, ($opener + 1), $closer);
        if ($asToken === false) {
            return;
        }

        $hasList = $phpcsFile->findNext([\T_LIST, \T_OPEN_SHORT_ARRAY], ($asToken + 1), $closer);
        if ($hasList === false) {
            return;
        }

        /*
         * @internal No need to check for short array vs short list as if this token is found after the `as`
         * in a `foreach`, it will always be a short list.
         * Also not affected by any known tokenizer bugs which would tokenize the open bracket as
         * `T_OPEN_SQUARE_BRACKET`.
         */

        $phpcsFile->addError(
            'Unpacking nested arrays with list() in a foreach is not supported in PHP 5.4 or earlier.',
            $hasList,
            'Found'
        );
    }
}
