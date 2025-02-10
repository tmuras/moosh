<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2020 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Syntax;

use PHPCompatibility\Helpers\ScannedCode;
use PHPCompatibility\Sniff;
use PHP_CodeSniffer\Files\File;

/**
 * Detect usage of flexible heredoc/nowdoc and related cross-version incompatibilities.
 *
 * As of PHP 7.3:
 * - The body and the closing marker of a heredoc/nowdoc can be indented;
 * - The closing marker no longer needs to be on a line by itself;
 * - The heredoc/nowdoc body may no longer contain the closing marker at the
 *   start of any of its lines.
 *
 * PHP version 7.3
 *
 * @link https://www.php.net/manual/en/migration73.new-features.php#migration73.new-features.core.heredoc
 * @link https://wiki.php.net/rfc/flexible_heredoc_nowdoc_syntaxes
 *
 * @since 9.0.0
 */
class NewFlexibleHeredocNowdocSniff extends Sniff
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
        $targets = [
            \T_END_HEREDOC,
            \T_END_NOWDOC,
        ];

        if (\PHP_VERSION_ID < 70300) {
            // Start identifier of a PHP 7.3 flexible heredoc/nowdoc.
            $targets[] = \T_STRING;
        }

        return $targets;
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
        if (ScannedCode::shouldRunOnOrBelow('7.2') === true) {
            $this->detectIndentedNonStandAloneClosingMarker($phpcsFile, $stackPtr);
        }

        $tokens = $phpcsFile->getTokens();
        if (ScannedCode::shouldRunOnOrAbove('7.3') === true && $tokens[$stackPtr]['code'] !== \T_STRING) {
            $this->detectClosingMarkerInBody($phpcsFile, $stackPtr);
        }
    }


    /**
     * Detect indented and/or non-stand alone closing markers.
     *
     * @since 9.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    protected function detectIndentedNonStandAloneClosingMarker(File $phpcsFile, $stackPtr)
    {
        $tokens            = $phpcsFile->getTokens();
        $indentError       = 'Heredoc/nowdoc with an indented closing marker is not supported in PHP 7.2 or earlier.';
        $indentErrorCode   = 'IndentedClosingMarker';
        $trailingError     = 'Having code - other than a semi-colon or new line - after the closing marker of a heredoc/nowdoc is not supported in PHP 7.2 or earlier.';
        $trailingErrorCode = 'ClosingMarkerNoNewLine';

        if (\PHP_VERSION_ID >= 70300) {
            /*
             * Check for indented closing marker.
             */
            if (\ltrim($tokens[$stackPtr]['content']) !== $tokens[$stackPtr]['content']) {
                $phpcsFile->addError($indentError, $stackPtr, $indentErrorCode);
            }

            /*
             * Check for tokens after the closing marker.
             */
            $nextNonWhitespace = $phpcsFile->findNext([\T_WHITESPACE, \T_SEMICOLON], ($stackPtr + 1), null, true);
            if ($tokens[$stackPtr]['line'] === $tokens[$nextNonWhitespace]['line']) {
                $phpcsFile->addError($trailingError, $stackPtr, $trailingErrorCode);
            }
        } else {
            // For PHP < 7.3, we're only interested in T_STRING tokens.
            if ($tokens[$stackPtr]['code'] !== \T_STRING) {
                return;
            }

            if (\preg_match('`^<<<([\'"]?)([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\1[\r\n]+`', $tokens[$stackPtr]['content'], $matches) !== 1) {
                // Not the start of a PHP 7.3 flexible heredoc/nowdoc.
                return;
            }

            $identifier = $matches[2];

            for ($i = ($stackPtr + 1); $i <= $phpcsFile->numTokens; $i++) {
                if ($tokens[$i]['code'] !== \T_ENCAPSED_AND_WHITESPACE) {
                    continue;
                }

                $trimmed = \ltrim($tokens[$i]['content']);

                if (\strpos($trimmed, $identifier) !== 0) {
                    continue;
                }

                // OK, we've found the PHP 7.3 flexible heredoc/nowdoc closing marker.

                /*
                 * Check for indented closing marker.
                 */
                if ($trimmed !== $tokens[$i]['content']) {
                    // Indent found before closing marker.
                    $phpcsFile->addError($indentError, $i, $indentErrorCode);
                }

                /*
                 * Check for tokens after the closing marker.
                 */
                // Remove the identifier.
                $afterMarker = \substr($trimmed, \strlen($identifier));
                // Remove a potential semi-colon at the beginning of what's left of the string.
                $afterMarker = \ltrim($afterMarker, ';');
                // Remove new line characters at the end of the string.
                $afterMarker = \rtrim($afterMarker, "\r\n");

                if ($afterMarker !== '') {
                    $phpcsFile->addError($trailingError, $i, $trailingErrorCode);
                }

                break;
            }
        }
    }


    /**
     * Detect heredoc/nowdoc identifiers at the start of lines in the heredoc/nowdoc body.
     *
     * @since 9.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    protected function detectClosingMarkerInBody(File $phpcsFile, $stackPtr)
    {
        $tokens    = $phpcsFile->getTokens();
        $error     = 'The body of a heredoc/nowdoc can not contain the heredoc/nowdoc closing marker as text at the start of a line since PHP 7.3.';
        $errorCode = 'CloserFoundInBody';

        if (\PHP_VERSION_ID >= 70300) {
            $nextNonWhitespace = $phpcsFile->findNext(\T_WHITESPACE, ($stackPtr + 1), null, true, null, true);
            if ($nextNonWhitespace === false
                || $tokens[$nextNonWhitespace]['code'] === \T_SEMICOLON
                || (($tokens[$nextNonWhitespace]['code'] === \T_COMMA
                    || $tokens[$nextNonWhitespace]['code'] === \T_STRING_CONCAT)
                    && $tokens[$nextNonWhitespace]['line'] !== $tokens[$stackPtr]['line'])
            ) {
                // This is most likely a correctly identified closing marker.
                return;
            }

            // The real closing tag has to be before the next heredoc/nowdoc.
            $nextHereNowDoc = $phpcsFile->findNext([\T_START_HEREDOC, \T_START_NOWDOC], ($stackPtr + 1));
            if ($nextHereNowDoc === false) {
                $nextHereNowDoc = null;
            }

            $identifier        = \trim($tokens[$stackPtr]['content']);
            $realClosingMarker = $stackPtr;

            while (($realClosingMarker = $phpcsFile->findNext(\T_STRING, ($realClosingMarker + 1), $nextHereNowDoc, false, $identifier)) !== false) {

                $prevNonWhitespace = $phpcsFile->findPrevious(\T_WHITESPACE, ($realClosingMarker - 1), null, true);
                if ($prevNonWhitespace === false
                    || $tokens[$prevNonWhitespace]['line'] === $tokens[$realClosingMarker]['line']
                ) {
                    // Marker text found, but not at the start of the line.
                    continue;
                }

                // The original T_END_HEREDOC/T_END_NOWDOC was most likely incorrect as we've found
                // a possible alternative closing marker.
                $phpcsFile->addError($error, $stackPtr, $errorCode);

                break;
            }
        } else {
            if (isset($tokens[$stackPtr]['scope_closer'], $tokens[$stackPtr]['scope_opener']) === true
                && $tokens[$stackPtr]['scope_closer'] === $stackPtr
            ) {
                $opener = $tokens[$stackPtr]['scope_opener'];
            }

            $quotedIdentifier = \preg_quote($tokens[$stackPtr]['content'], '`');

            // Throw an error for each line in the body which starts with the identifier.
            for ($i = ($opener + 1); $i < $stackPtr; $i++) {
                if (\preg_match('`^[ \t]*' . $quotedIdentifier . '\b`', $tokens[$i]['content']) === 1) {
                    $phpcsFile->addError($error, $i, $errorCode);
                }
            }
        }
    }
}
