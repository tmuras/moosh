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

use MoodleHQ\MoodleCS\moodle\Util\Docblocks;
use MoodleHQ\MoodleCS\moodle\Util\TokenUtil;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Checks that all docblocks for a main scope have a simple description.
 *
 * This is typically a one-line description.
 *
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class DocblockDescriptionSniff implements Sniff
{
    /**
     * Register for open tag (only process once per file).
     */
    public function register() {
        return [
            T_OPEN_TAG,
        ];
    }

    /**
     * Processes php files and perform various checks with file.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position in the stack.
     */
    public function process(File $phpcsFile, $stackPtr) {
        $tokens = $phpcsFile->getTokens();
        $toCheck = [];

        $docblockPtr = Docblocks::getDocBlockPointer($phpcsFile, $stackPtr);
        if ($docblockPtr !== null) {
            $toCheck[$stackPtr] = $docblockPtr;
        }
        $find = Tokens::$ooScopeTokens;
        $find[T_FUNCTION] = T_FUNCTION;

        $typePtr = $stackPtr + 1;
        while ($typePtr = $phpcsFile->findNext($find, $typePtr + 1)) {
            $docblockPtr = Docblocks::getDocBlockPointer($phpcsFile, $typePtr);
            if ($docblockPtr === null) {
                // Other sniffs check for missing blocks. Not my job.
                continue;
            }

            $toCheck[$typePtr] = $docblockPtr;
        }

        foreach ($toCheck as $typePtr => $docblockPtr) {
            $docblock = $tokens[$docblockPtr];
            if (count($docblock['comment_tags'])) {
                $stopAt = reset($docblock['comment_tags']);
            } else {
                $stopAt = $docblock['comment_closer'];
            }
            $faultAtLine = $tokens[$stopAt]['line'];

            $deprecatedTagPtrs = Docblocks::getMatchingDocTags($phpcsFile, $docblockPtr, '@deprecated');
            if (count($deprecatedTagPtrs) > 0) {
                // Skip if the docblock contains a @deprecated tag.
                continue;
            }

            // Skip to the next T_DOC_COMMENT_STAR line. We do not accept single line docblocks.
            $docblockLinePtr = $docblockPtr;
            while ($docblockLinePtr = $phpcsFile->findNext(T_DOC_COMMENT_STAR, $docblockLinePtr + 1, $stopAt)) {
                if ($tokens[$docblockLinePtr]['line'] !== $faultAtLine) {
                    continue 2;
                }
                break;
            }

            $objectName = TokenUtil::getObjectName($phpcsFile, $typePtr);
            $objectType = TokenUtil::getObjectType($phpcsFile, $typePtr);

            $phpcsFile->addError(
                'No one-line description found in phpdocs for docblock of %s %s',
                $typePtr,
                'Missing',
                [$objectType, $objectName]
            );
        }
    }
}
