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
use MoodleHQ\MoodleCS\moodle\Util\TokenUtil;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

/**
 * Checks that all test classes and global functions have appropriate @package tags.
 *
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class PackageSniff implements Sniff
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

        $docPtr = Docblocks::getDocBlockPointer($phpcsFile, $stackPtr);
        if ($docPtr) {
            $filePackageFound = $this->checkDocblock(
                $phpcsFile,
                $stackPtr,
                $docPtr
            );
            if ($filePackageFound) {
                return;
            }
        }

        $find = [
            T_CLASS,
            T_FUNCTION,
            T_TRAIT,
            T_INTERFACE,
        ];
        $typePtr = $stackPtr + 1;
        while ($typePtr = $phpcsFile->findNext($find, $typePtr + 1)) {
            $token = $tokens[$typePtr];
            if ($token['code'] === T_FUNCTION && !empty($token['conditions'])) {
                // Skip methods of classes, traits and interfaces.
                continue;
            }

            $docPtr = Docblocks::getDocBlockPointer($phpcsFile, $typePtr);

            if ($docPtr === null) {
                continue;
            }

            $this->checkDocblock($phpcsFile, $typePtr, $docPtr);
        }
    }

    /**
     * Check the docblock for a @package tag.
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     * @param array $docblock
     * @return bool Whether any package tag was found, whether or not it was correct
     */
    protected function checkDocblock(
        File $phpcsFile,
        int $stackPtr,
        int $docPtr
    ): bool {
        $expectedPackage = MoodleUtil::getMoodleComponent($phpcsFile, true);

        // Nothing to do if we have been unable to determine the package
        // (all the following checks rely on this value).
        if ($expectedPackage === null) {
            return false;
        }

        $tokens = $phpcsFile->getTokens();
        $objectName = TokenUtil::getObjectName($phpcsFile, $stackPtr);
        $objectType = TokenUtil::getObjectType($phpcsFile, $stackPtr);
        $docblock = $tokens[$docPtr];

        $packageTokens = Docblocks::getMatchingDocTags($phpcsFile, $docPtr, '@package');
        if (empty($packageTokens)) {
            $fix = $phpcsFile->addFixableError(
                'DocBlock missing a @package tag for %s %s. Expected @package %s',
                $stackPtr,
                'Missing',
                [$objectType, $objectName, $expectedPackage]
            );

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addContentBefore($docblock['comment_closer'], '* @package ' . $expectedPackage . PHP_EOL . ' ');
                $phpcsFile->fixer->endChangeset();
            }

            return false;
        }

        if (count($packageTokens) > 1) {
            $fix = $phpcsFile->addFixableError(
                'More than one @package tag found in %s %s.',
                $stackPtr,
                'Multiple',
                [$objectType, $objectName]
            );

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $validTokenFound = false;

                foreach ($packageTokens as $i => $packageToken) {
                    $packageValuePtr = $phpcsFile->findNext(
                        T_DOC_COMMENT_STRING,
                        $packageToken,
                        $docblock['comment_closer']
                    );
                    $packageValue = $tokens[$packageValuePtr]['content'];
                    if (!$validTokenFound && $packageValue === $expectedPackage) {
                        $validTokenFound = true;
                        continue;
                    }
                    $lineNo = $tokens[$packageToken]['line'];
                    foreach (array_keys(MoodleUtil::getTokensOnLine($phpcsFile, $lineNo)) as $lineToken) {
                        $phpcsFile->fixer->replaceToken($lineToken, '');
                    }
                }
                if (!$validTokenFound) {
                    $phpcsFile->fixer->addContentBefore($packageTokens[0], ' * @package ' . $expectedPackage . PHP_EOL);
                }
                $phpcsFile->fixer->endChangeset();
            }
            return true;
        }

        $packageToken = reset($packageTokens);

        // Check the value of the package tag.
        $packageValuePtr = $phpcsFile->findNext(
            T_DOC_COMMENT_STRING,
            $packageToken,
            $docblock['comment_closer']
        );
        $packageValue = $tokens[$packageValuePtr]['content'];

        // Compare to expected value.
        if ($packageValue === $expectedPackage) {
            return true;
        }

        $fix = $phpcsFile->addFixableError(
            'Incorrect @package tag for %s %s. Expected %s, found %s.',
            $packageToken,
            'Incorrect',
            [$objectType, $objectName, $expectedPackage, $packageValue]
        );

        if ($fix) {
            $phpcsFile->fixer->beginChangeset();
            $phpcsFile->fixer->replaceToken($packageValuePtr, $expectedPackage);
            $phpcsFile->fixer->endChangeset();
        }

        return true;
    }
}
