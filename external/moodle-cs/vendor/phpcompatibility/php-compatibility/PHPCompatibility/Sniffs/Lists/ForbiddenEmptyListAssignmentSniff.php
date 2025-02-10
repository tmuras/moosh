<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2020 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Lists;

use PHPCompatibility\Helpers\ScannedCode;
use PHPCompatibility\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\Lists;

/**
 * Support for empty `list()` expressions has been removed in PHP 7.0.
 *
 * PHP version 7.0
 *
 * @link https://www.php.net/manual/en/migration70.incompatible.php#migration70.incompatible.variable-handling.list.empty
 * @link https://wiki.php.net/rfc/abstract_syntax_tree#changes_to_list
 * @link https://www.php.net/manual/en/function.list.php
 *
 * @since 7.0.0
 */
class ForbiddenEmptyListAssignmentSniff extends Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 7.0.0
     *
     * @return array<int|string>
     */
    public function register()
    {
        return Collections::listOpenTokensBC();
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 7.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if (ScannedCode::shouldRunOnOrAbove('7.0') === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        if (isset(Collections::shortArrayListOpenTokensBC()[$tokens[$stackPtr]['code']]) === true
            && Lists::isShortList($phpcsFile, $stackPtr) === false
        ) {
            // Real square brackets or short array, not short list.
            return;
        }

        try {
            $assignments = Lists::getAssignments($phpcsFile, $stackPtr);
        } catch (RuntimeException $e) {
            // Parse error/live coding.
            return;
        }

        if (empty($assignments) === false) {
            foreach ($assignments as $assign) {
                if ($assign['assignment_token'] !== false) {
                    // Either a variable or a nested list. I.e. not an empty list.
                    return;
                }
            }
        }

        $phpcsFile->addError(
            'Empty list() assignments are not allowed since PHP 7.0',
            $stackPtr,
            'Found'
        );
    }
}
