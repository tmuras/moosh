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

namespace MoodleHQ\MoodleCS\moodle\Sniffs\PHPUnit;

use MoodleHQ\MoodleCS\moodle\Util\MoodleUtil;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHPCSUtils\Utils\FunctionDeclarations;
use PHPCSUtils\Utils\ObjectDeclarations;

/**
 * Checks that a test file has the @coversxxx annotations properly defined.
 *
 * @copyright  2022 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class TestReturnTypeSniff implements Sniff
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
     * @param File $file The file being scanned.
     * @param int $pointer The position in the stack.
     */
    public function process(File $file, $pointer)
    {
        // Before starting any check, let's look for various things.

        // If we aren't checking Moodle 4.4dev (404) and up, nothing to check.
        // Make and exception for codechecker phpunit tests, so they are run always.
        if (!MoodleUtil::meetsMinimumMoodleVersion($file, 404) && !MoodleUtil::isUnitTestRunning()) {
            return; // @codeCoverageIgnore
        }

        // If the file is not a unit test file, nothing to check.
        if (!MoodleUtil::isUnitTest($file) && !MoodleUtil::isUnitTestRunning()) {
            return; // @codeCoverageIgnore
        }

        // We have all we need from core, let's start processing the file.

        // Get the file tokens, for ease of use.
        $tokens = $file->getTokens();

        // We only want to do this once per file.
        $prevopentag = $file->findPrevious(
            T_OPEN_TAG,
            $pointer - 1
        );
        if ($prevopentag !== false) {
            return; // @codeCoverageIgnore
        }

        // Iterate over all the classes (hopefully only one, but that's not this sniff problem).
        $cStart = $pointer;
        while ($cStart = $file->findNext(T_CLASS, $cStart + 1)) {
            if (MoodleUtil::isUnitTestCaseClass($file, $cStart) === false) {
                // This class does not related to a uit test.
                continue;
            }

            $classInfo = ObjectDeclarations::getClassProperties($file, $cStart);

            // Iterate over all the methods in the class.
            $mStart = $cStart;
            while ($mStart = $file->findNext(T_FUNCTION, $mStart + 1, $tokens[$cStart]['scope_closer'])) {
                $method = $file->getDeclarationName($mStart);

                // Ignore non test_xxxx() methods.
                if (strpos($method, 'test_') !== 0) {
                    continue;
                }

                // Get the function declaration.
                $functionInfo = FunctionDeclarations::getProperties($file, $mStart);

                // 'return_type_token'     => int|false, // The stack pointer to the start of the return type.
                if ($functionInfo['return_type_token'] !== false) {
                    // This method has a return type.
                    // In most cases that will be void, but it could be anything in the
                    // case of chained or dependant tests.
                    // TODO: Detect all cases of chained tests and dependant tests.
                    continue;
                }

                $methodNamePtr = $file->findNext(T_STRING, $mStart + 1, $tokens[$mStart]['parenthesis_opener']);
                $methodEnd = $file->findNext(T_CLOSE_PARENTHESIS, $mStart + 1);

                // Detect if the method has a return statement.
                // If it does, then see if it has a value or not.
                $hasReturn = $file->findNext(T_RETURN, $mStart + 1, $tokens[$mStart]['scope_closer']);
                $probablyVoid = !$hasReturn;
                if ($hasReturn) {
                    $next = $file->findNext(
                        T_WHITESPACE,
                        $hasReturn + 1,
                        $tokens[$mStart]['scope_closer'],
                        true
                    );
                    if ($tokens[$next]['code'] === T_SEMICOLON) {
                        $probablyVoid = true;
                    }
                }

                $fix = false;
                if ($probablyVoid) {
                    $fix = $file->addFixableWarning(
                        'Test method %s() is missing a return type',
                        $methodNamePtr,
                        'MissingReturnType',
                        [$method]
                    );
                } else {
                    $file->addWarning(
                        'Test method %s() is missing a return type',
                        $methodNamePtr,
                        'MissingReturnType',
                        [$method]
                    );
                }

                if ($fix) {
                    $file->fixer->beginChangeset();
                    $file->fixer->addContent($methodEnd, ': void');
                    $file->fixer->endChangeset();
                }
            }
        }
    }
}
