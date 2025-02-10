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
 * Checks that valid docblock tags are in use.
 *
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ValidTagsSniff implements Sniff
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

        while ($docPtr = $phpcsFile->findNext(T_DOC_COMMENT_OPEN_TAG, $stackPtr)) {
            $docblock = $tokens[$docPtr];
            foreach ($docblock['comment_tags'] as $tagPtr) {
                $tagName = ltrim($tokens[$tagPtr]['content'], '@');
                if (!Docblocks::isValidTag($phpcsFile, $tagPtr)) {
                    if (Docblocks::shouldRemoveTag($tagName)) {
                        $fix = $phpcsFile->addFixableError(
                            'Invalid docblock tag "@%s" is not supported.',
                            $tagPtr,
                            'Invalid',
                            [$tagName]
                        );
                        if ($fix) {
                            $phpcsFile->fixer->beginChangeset();
                            foreach ($this->getTokensOnTokenLine($phpcsFile, $tagPtr) as $tokenPtr) {
                                $phpcsFile->fixer->replaceToken($tokenPtr, '');
                            }
                            $phpcsFile->fixer->endChangeset();
                        }
                    } elseif ($renameTo = Docblocks::getRenameTag($tagName)) {
                        $fix = $phpcsFile->addFixableError(
                            'Incorrect docblock tag "@%s". Should be "@%s".',
                            $tagPtr,
                            'Invalid',
                            [$tagName, $renameTo]
                        );
                        if ($fix) {
                            $phpcsFile->fixer->beginChangeset();
                            $phpcsFile->fixer->replaceToken($tagPtr, "@{$renameTo}");
                            $phpcsFile->fixer->endChangeset();
                        }
                    } else {
                        $phpcsFile->addError(
                            'Invalid docblock tag "@%s".',
                            $tagPtr,
                            'Invalid',
                            [$tagName]
                        );
                    }
                } elseif (!Docblocks::isRecommendedTag($tagName)) {
                    // The tag is valid, but not recommended.
                    $phpcsFile->addWarning(
                        'Docblock tag "@%s" is not recommended.',
                        $tagPtr,
                        'Invalid',
                        [$tagName]
                    );
                }
            }
            $stackPtr = $docPtr + 1;
        }
    }

    /**
     * Get the tokens on the same line as the given token.
     *
     * @param File $phpcsFile
     * @param int $ptr
     * @return int[]
     */
    protected function getTokensOnTokenLine(File $phpcsFile, int $ptr): array {
        $tokens = $phpcsFile->getTokens();
        $line = $tokens[$ptr]['line'];
        $lineTokens = [];
        for ($i = $ptr; $i >= 0; $i--) {
            if ($tokens[$i]['line'] === $line) {
                array_unshift($lineTokens, $i);
                continue;
            }
            break;
        }

        $lineTokens[] = $ptr;

        for ($i = $ptr; $i < count($tokens); $i++) {
            if ($tokens[$i]['line'] === $line) {
                $lineTokens[] = $i;
                continue;
            }
            break;
        }

        return $lineTokens;
    }
}
