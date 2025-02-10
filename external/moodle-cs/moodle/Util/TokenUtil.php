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

namespace MoodleHQ\MoodleCS\moodle\Util;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Utils\ObjectDeclarations;

class TokenUtil
{
    /**
     * Get the human-readable object type.
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return string
     */
    public static function getObjectType(
        File $phpcsFile,
        int $stackPtr
    ): string {
        $tokens = $phpcsFile->getTokens();

        if (!isset($tokens[$stackPtr])) {
            return '';
        }

        if ($tokens[$stackPtr]['code'] === T_OPEN_TAG) {
            return 'file';
        }
        return $tokens[$stackPtr]['content'];
    }

    /**
     * Get the human readable object name.
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return null|string
     */
    public static function getObjectName(
        File $phpcsFile,
        int $stackPtr
    ): ?string {
        $tokens = $phpcsFile->getTokens();
        if (!isset($tokens[$stackPtr])) {
            return '';
        }

        if ($tokens[$stackPtr]['code'] === T_OPEN_TAG) {
            return basename($phpcsFile->getFilename());
        }

        if ($tokens[$stackPtr]['code'] === T_ANON_CLASS) {
            return 'anonymous class';
        }

        if ($tokens[$stackPtr]['code'] === T_CLOSURE) {
            return 'closure';
        }

        return ObjectDeclarations::getName($phpcsFile, $stackPtr);
    }

    /**
     * Count the number of global scopes in a file.
     *
     * @param File $phpcsFile
     * @return int
     */
    public static function countGlobalScopesInFile(
        File $phpcsFile
    ): int {
        $tokens = $phpcsFile->getTokens();
        $artifactCount = 0;
        $find = Tokens::$ooScopeTokens;
        $find[] = T_FUNCTION;

        $typePtr = 0;
        while ($typePtr = $phpcsFile->findNext($find, $typePtr + 1)) {
            $token = $tokens[$typePtr];
            if ($token['code'] === T_FUNCTION && !empty($token['conditions'])) {
                // Skip methods of classes, traits and interfaces.
                continue;
            }

            $artifactCount++;
        }

        return $artifactCount;
    }
}
