<?php

// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * This sniff checks that method declarations are correct, spacing-wise.
 *
 * This Sniff completes other Sniffs, already included in the moodle standard,
 * to ensure that the methods declarations make a correct use of whitespace.
 * More specifically, it completes the following:
 * - PSR12.Functions.ReturnTypeDeclaration
 * - PSR12.Functions.NullableTypeDeclaration
 * - PSR2.Methods.MethodDeclaration
 * - Squiz.Whitespace.ScopeKeywordSpacing
 *
 * If any of the above Sniffs is disabled, then we'll have to add more
 * features to this one.

 * @copyright 2024 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleHQ\MoodleCS\moodle\Sniffs\Methods;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;
use PHP_CodeSniffer\Util\Tokens;

class MethodDeclarationSpacingSniff extends AbstractScopeSniff
{
    public function __construct() {
        parent::__construct(Tokens::$ooScopeTokens, [T_FUNCTION]);
    }

    /**
     * Processes the function tokens within the class.
     *
     * @param File $phpcsFile The file where this token was found.
     * @param int $stackPtr  The position where the token was found.
     * @param int $currScope The current scope opener token.
     *
     * @return void
     */
    protected function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope) {
        // List of tokens that require one space after them.
        $oneSpaceTokens = [
            // T_PUBLIC,    // Disabled. Squiz.WhiteSpace.ScopeKeywordSpacing handles it.
            // T_PROTECTED, // Disabled. Squiz.WhiteSpace.ScopeKeywordSpacing handles it.
            // T_PRIVATE,   // Disabled. Squiz.WhiteSpace.ScopeKeywordSpacing handles it.
            // T_STATIC,    // Disabled. Squiz.WhiteSpace.ScopeKeywordSpacing handles it.
            T_ABSTRACT,
            T_FINAL,
            T_FUNCTION,
        ];

        // List of tokens that require no space after them.
        $noSpaceTokens = [
            T_STRING,
            T_OPEN_PARENTHESIS,
        ];

        $tokens = $phpcsFile->getTokens();

        // Determine if this is a function which needs to be examined.
        $conditions = $tokens[$stackPtr]['conditions'];
        end($conditions);
        $deepestScope = key($conditions);
        if ($deepestScope !== $currScope) {
            return;
        }

        $methodName = $phpcsFile->getDeclarationName($stackPtr);
        if ($methodName === null) {
            // Ignore closures.
            return;
        }

        // Find the opening parenthesis of the argument list. That's the last token
        // that this Sniff is interested in. If, for any reason, the opening parenthesis
        // is not found, then we'll start from current T_FUNCTION token.
        $lastToken = $tokens[$stackPtr]['parenthesis_opener'] ?? $stackPtr;

        // These are the tokens that we are interested on.
        $findTokens = array_merge($oneSpaceTokens, $noSpaceTokens);

        // Find the first token that we are interested in.
        $foundToken = $phpcsFile->findPrevious($findTokens, $lastToken);

        // Search backwards and examine all the matches found until we change of line.
        while ($tokens[$foundToken]['line'] === $tokens[$lastToken]['line']) {
            // If it's a token that requires one space after it, check if there is one.
            if (in_array($tokens[$foundToken]['code'], $oneSpaceTokens) === true) {
                $replacement = ' ';
                if (
                    $tokens[$foundToken + 1]['code'] === T_WHITESPACE && // We only check whitespace.
                    $tokens[$foundToken + 1]['content'] !== ' '
                ) {
                    $error = 'Expected 1 space after "%s"; %s found';
                    $data = [
                        $tokens[$foundToken]['content'],
                        strlen($tokens[$foundToken + 1]['content']),
                    ];
                    $fix = $phpcsFile->addFixableError($error, $foundToken, 'OneExpectedAfter', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken($foundToken + 1, $replacement);
                    }
                }
            }

            // If it's a token that requires no space after it, check if there is none.
            if (in_array($tokens[$foundToken]['code'], $noSpaceTokens) === true) {
                $replacement = '';
                $process = true;

                // We only process T_STRING if it matches the method name.
                if ($tokens[$foundToken]['code'] === T_STRING) {
                    if ($tokens[$foundToken]['content'] !== $methodName) {
                        $process = false;
                    }
                }

                // We only process T_OPEN_PARENTHESIS if it has spaces, and only
                // to get rid of them
                if ($tokens[$foundToken]['code'] === T_OPEN_PARENTHESIS) {
                    if (strpos($tokens[$foundToken + 1]['content'], ' ') !== false) {
                        $replacement = str_replace(' ', '', $tokens[$foundToken + 1]['content']);
                    } else {
                        $process = false;
                    }
                }

                if (
                    $tokens[$foundToken + 1]['code'] === T_WHITESPACE && // We only check whitespace.
                    $tokens[$foundToken + 1]['content'] !== '' &&
                    $process === true
                ) {
                    $error = 'Expected 0 spaces after "%s"; %s found';
                    $data = [
                        $tokens[$foundToken]['content'],
                        strlen($tokens[$foundToken + 1]['content']),
                    ];
                    $fix = $phpcsFile->addFixableError($error, $foundToken, 'ZeroExpectedAfter', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken($foundToken + 1, $replacement);
                    }
                }
            }

            // Move to the previous interesting token.
            $foundToken = $phpcsFile->findPrevious($findTokens, $foundToken - 1);
        }
    }

    protected function processTokenOutsideScope(File $phpcsFile, $stackPtr) {
        return; // @codeCoverageIgnore
    }
}
