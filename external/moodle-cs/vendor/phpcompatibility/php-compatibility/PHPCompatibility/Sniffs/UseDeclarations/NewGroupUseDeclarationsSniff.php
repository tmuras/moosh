<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2020 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\UseDeclarations;

use PHPCompatibility\Helpers\ScannedCode;
use PHPCompatibility\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Detect group use declarations as introduced in PHP 7.0.
 *
 * Checks for:
 * - Group use statements as introduced in PHP 7.0.
 * - Trailing commas in group use statements as allowed since PHP 7.2.
 *
 * PHP version 7.0
 * PHP version 7.2
 *
 * @link https://www.php.net/manual/en/migration70.new-features.php#migration70.new-features.group-use-declarations
 * @link https://www.php.net/manual/en/migration72.new-features.php#migration72.new-features.trailing-comma-in-grouped-namespaces
 * @link https://wiki.php.net/rfc/group_use_declarations
 * @link https://wiki.php.net/rfc/list-syntax-trailing-commas
 * @link https://www.php.net/manual/en/language.namespaces.importing.php#language.namespaces.importing.group
 *
 * @since 7.0.0
 * @since 8.0.1 Now also checks for trailing commas in group `use` declarations.
 */
class NewGroupUseDeclarationsSniff extends Sniff
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
        return [
            \T_OPEN_USE_GROUP,
            \T_CLOSE_USE_GROUP,
        ];
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 7.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if (ScannedCode::shouldRunOnOrBelow('7.1') === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === \T_OPEN_USE_GROUP
            && ScannedCode::shouldRunOnOrBelow('5.6') === true
        ) {
            $phpcsFile->addError(
                'Group use declarations are not allowed in PHP 5.6 or earlier',
                $stackPtr,
                'Found'
            );
        }

        if ($tokens[$stackPtr]['code'] === \T_CLOSE_USE_GROUP) {
            $prevToken = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
            if ($tokens[$prevToken]['code'] === \T_COMMA) {
                $phpcsFile->addError(
                    'Trailing commas are not allowed in group use statements in PHP 7.1 or earlier',
                    $prevToken,
                    'TrailingCommaFound'
                );
            }
        }
    }
}
