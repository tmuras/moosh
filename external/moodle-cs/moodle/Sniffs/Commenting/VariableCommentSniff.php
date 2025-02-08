<?php

// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANdTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace MoodleHQ\MoodleCS\moodle\Sniffs\Commenting;

use MoodleHQ\MoodleCS\moodle\Util\TypeUtil;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\ObjectDeclarations;

/**
 * Parses and verifies the variable doc comment.
 *
 * The Sniff is based upon the Squiz Labs version, but it has been modified to accept int, rather than integer.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author Andrew Lyons <andrew@nicols.co.uk>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
class VariableCommentSniff extends AbstractVariableSniff
{
    /**
     * Called to process class member vars.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function processMemberVar(File $phpcsFile, $stackPtr) {
        $tokens = $phpcsFile->getTokens();

        $ignore = [
            T_WHITESPACE => T_WHITESPACE,
            T_NULLABLE => T_NULLABLE,
        ]
            + Collections::propertyModifierKeywords()
            + Collections::parameterTypeTokens();

        for ($commentEnd = ($stackPtr - 1); $commentEnd >= 0; $commentEnd--) {
            if (isset($ignore[$tokens[$commentEnd]['code']]) === true) {
                continue;
            }

            if (
                $tokens[$commentEnd]['code'] === T_ATTRIBUTE_END
                && isset($tokens[$commentEnd]['attribute_opener']) === true
            ) {
                $commentEnd = $tokens[$commentEnd]['attribute_opener'];
                continue;
            }

            break;
        }

        if (
            $commentEnd === false
            || ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG
                && $tokens[$commentEnd]['code'] !== T_COMMENT)
        ) {
            $phpcsFile->addError('Missing member variable doc comment', $stackPtr, 'Missing');
            return;
        }

        if ($tokens[$commentEnd]['code'] === T_COMMENT) {
            $phpcsFile->addError('You must use "/**" style comments for a member variable comment', $stackPtr, 'WrongStyle');
            return;
        }

        $commentStart = $tokens[$commentEnd]['comment_opener'];

        $foundVar = null;
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if ($tokens[$tag]['content'] === '@var') {
                if ($foundVar !== null) {
                    $error = 'Only one @var tag is allowed in a member variable comment';
                    $phpcsFile->addError($error, $tag, 'DuplicateVar');
                } else {
                    $foundVar = $tag;
                }
            } elseif ($tokens[$tag]['content'] === '@see') {
                // Make sure the tag isn't empty.
                $string = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $tag, $commentEnd);
                if ($string === false || $tokens[$string]['line'] !== $tokens[$tag]['line']) {
                    $error = 'Content missing for @see tag in member variable comment';
                    $phpcsFile->addError($error, $tag, 'EmptySees');
                }
            } else {
                $error = '%s tag is not allowed in member variable comment';
                $data = [$tokens[$tag]['content']];
                $phpcsFile->addWarning($error, $tag, 'TagNotAllowed', $data);
            }
        }

        // The @var tag is the only one we require.
        if ($foundVar === null) {
            $error = 'Missing @var tag in member variable comment';
            $phpcsFile->addError($error, $commentEnd, 'MissingVar');
            return;
        }

        $firstTag = $tokens[$commentStart]['comment_tags'][0];
        if ($foundVar !== null && $tokens[$firstTag]['content'] !== '@var') {
            $error = 'The @var tag must be the first tag in a member variable comment';
            $phpcsFile->addError($error, $foundVar, 'VarOrder');
        }

        // Make sure the tag isn't empty and has the correct padding.
        $string = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $foundVar, $commentEnd);
        if ($string === false || $tokens[$string]['line'] !== $tokens[$foundVar]['line']) {
            $error = 'Content missing for @var tag in member variable comment';
            $phpcsFile->addError($error, $foundVar, 'EmptyVar');
            return;
        }

        // Support both a var type and a description.
        preg_match('`^((?:\|?(?:array\([^\)]*\)|[\\\\a-z0-9\[\]]+))*)( .*)?`i', $tokens[($foundVar + 2)]['content'], $varParts);
        $varType = $varParts[1];

        $suggestedType = TypeUtil::getValidatedType($phpcsFile, $string, $varType);
        if ($varType !== $suggestedType) {
            $error = 'Expected "%s" but found "%s" for @var tag in member variable comment';
            $data = [
                $suggestedType,
                $varType,
            ];

            $fix = $phpcsFile->addFixableError($error, $foundVar, 'IncorrectVarType', $data);
            if ($fix === true) {
                $replacement = $suggestedType;
                if (empty($varParts[2]) === false) {
                    $replacement .= $varParts[2];
                }

                $phpcsFile->fixer->replaceToken(($foundVar + 2), $replacement);
                unset($replacement);
            }
        }
    }

    /**
     * Processes normal variables within a method.
     *
     * @param File $file The file where this token was found.
     * @param int $stackptr The position where the token was found.
     *
     * @return void
     */
    protected function processVariable(File $phpcsFile, $stackPtr) {
        // Find the method that this variable is declared in.
        $methodPtr = $phpcsFile->findPrevious(T_FUNCTION, $stackPtr);
        if ($methodPtr === false) {
            // Not in a method.
            return;  // @codeCoverageIgnore
        }

        $methodName = ObjectDeclarations::getName($phpcsFile, $methodPtr);
        if ($methodName !== '__construct') {
            // Not in a constructor.
            return;
        }

        $method = $phpcsFile->getTokens()[$methodPtr];
        if ($method['parenthesis_opener'] < $stackPtr && $method['parenthesis_closer'] > $stackPtr) {
            // Only apply to properties declared in the constructor.
            // Constructor Promoted Properties canbe detected by a visbility keyword.
            // These can be found, amongst others like READONLY in Collections::propertyModifierKeywords().
            // When searching, only look back to the previous arg (comma), or the opening parenthesis.
            $lookBackTo = max(
                $method['parenthesis_opener'],
                $phpcsFile->findPrevious(T_COMMA, $stackPtr)
            );
            $modifierPtr = $phpcsFile->findPrevious(
                Collections::propertyModifierKeywords(),
                $stackPtr,
                $lookBackTo
            );
            if ($modifierPtr === false) {
                // No modifier found, so not a promoted property.
                return;
            }

            // This is a promoted property. Handle it in the same way as other properties.
            $this->processMemberVar($phpcsFile, $stackPtr);
            return;
        }
    }

    /**
     * @codeCoverageIgnore
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr) {
    }
}
