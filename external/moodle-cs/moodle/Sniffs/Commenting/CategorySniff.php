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

namespace MoodleHQ\MoodleCS\moodle\Sniffs\Commenting;

use MoodleHQ\MoodleCS\moodle\Util\MoodleUtil;
use MoodleHQ\MoodleCS\moodle\Util\Docblocks;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

/**
 * Checks that all test classes and global functions have appropriate @package tags.
 *
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class CategorySniff implements Sniff
{
    /**
     * Register for open tag (only process once per file).
     */
    public function register() {
        return [
            T_DOC_COMMENT_OPEN_TAG,
        ];
    }

    /**
     * Processes php files and perform various checks with file.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position in the stack.
     */
    public function process(File $phpcsFile, $stackPtr) {
        $docPtr = Docblocks::getDocBlockPointer($phpcsFile, $stackPtr);
        if (empty($docPtr)) {
            // It should not be possible to reach this line. It is a safety check.
            return; // @codeCoverageIgnore
        }

        $categoryTokens = Docblocks::getMatchingDocTags($phpcsFile, $docPtr, '@category');
        if (empty($categoryTokens)) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        $docblock = $tokens[$docPtr];
        $apis = MoodleUtil::getMoodleApis($phpcsFile);

        foreach ($categoryTokens as $tokenPtr) {
            $categoryValuePtr = $phpcsFile->findNext(
                T_DOC_COMMENT_STRING,
                $tokenPtr,
                $docblock['comment_closer']
            );
            $categoryValue = $tokens[$categoryValuePtr]['content'];
            if (!in_array($categoryValue, $apis)) {
                $phpcsFile->addError(
                    'Invalid @category tag value "%s".',
                    $categoryValuePtr,
                    'Invalid',
                    [$categoryValue]
                );
            }
        }
    }
}
