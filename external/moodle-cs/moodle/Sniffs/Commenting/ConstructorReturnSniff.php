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

/**
 * Checks that all files an classes have appropriate docs.
 *
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ConstructorReturnSniff implements Sniff
{
    /**
     * Register for class tags.
     */
    public function register() {

        return [
            T_CLASS,
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
        $endClassPtr = $tokens[$stackPtr]['scope_closer'];

        while (
            ($methodPtr = $phpcsFile->findNext(T_FUNCTION, $stackPtr + 1, $endClassPtr)) !== false
        ) {
            $this->processClassMethod($phpcsFile, $methodPtr);
            $stackPtr = $methodPtr;
        }
    }

    /**
     * Processes the class method.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position in the stack.
     */
    protected function processClassMethod(File $phpcsFile, int $stackPtr): void {
        $objectName = TokenUtil::getObjectName($phpcsFile, $stackPtr);
        if ($objectName !== '__constructor') {
            // We only care about constructors.
            return;
        }

        // Get docblock.
        $docblockPtr = Docblocks::getDocBlockPointer($phpcsFile, $stackPtr);
        if ($docblockPtr === null) {
            // No docblocks for this constructor.
            return;
        }

        $returnTokens = Docblocks::getMatchingDocTags($phpcsFile, $docblockPtr, '@return');
        if (count($returnTokens) === 0) {
            // No @return tag in the docblock.
            return;
        }

        $fix = $phpcsFile->addFixableError(
            'Constructor should not have a return tag in the docblock',
            $returnTokens[0],
            'ConstructorReturn'
        );
        if ($fix) {
            $tokens = $phpcsFile->getTokens();
            $phpcsFile->fixer->beginChangeset();

            // Find the tokens at the start and end of the line.
            $lineStart = $phpcsFile->findFirstOnLine(T_DOC_COMMENT_STAR, $returnTokens[0]);
            if ($lineStart === false) {
                $lineStart = $returnTokens[0];
            }

            $ptr = $phpcsFile->findNext(T_DOC_COMMENT_WHITESPACE, $lineStart);
            for ($lineEnd = $lineStart; $lineEnd < $tokens[$docblockPtr]['comment_closer']; $lineEnd++) {
                if ($tokens[$lineEnd]['line'] !== $tokens[$lineStart]['line']) {
                    break;
                }
            }

            if ($tokens[$lineEnd]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
                $lineEnd--;
            }

            for ($ptr = $lineStart; $ptr <= $lineEnd; $ptr++) {
                $phpcsFile->fixer->replaceToken($ptr, '');
            }

            $phpcsFile->fixer->endChangeset();
        }
    }
}
