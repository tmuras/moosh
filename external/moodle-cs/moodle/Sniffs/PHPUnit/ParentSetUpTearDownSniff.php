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
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Checks that a test file setUp and tearDown methods are always calling to parent.
 *
 * @copyright  2024 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ParentSetUpTearDownSniff implements Sniff
{
    /**
     * @var string[] Methods to verify that they are calling to parent (setup like).
     */
    private static array $setUpMethods = [
        'setUp',
        'setUpBeforeClass',
    ];

    /**
     * @var string[] Methods to verify that they are calling to parent (teardown like).
     */
    private static array $tearDownMethods = [
        'tearDown',
        'tearDownAfterClass',
    ];

    /**
     * Register for open tag (only process once per file).
     */
    public function register(): array {
        return [T_OPEN_TAG];
    }

    /**
     * Processes php files and perform various checks with file.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position in the stack.
     */
    public function process(File $phpcsFile, $stackPtr): void {

        // Before starting any check, let's look for various things.

        // If we aren't checking Moodle 4.5dev (405) and up, nothing to check.
        // Make and exception for codechecker phpunit tests, so they are run always.
        if (!MoodleUtil::meetsMinimumMoodleVersion($phpcsFile, 405) && !MoodleUtil::isUnitTestRunning()) {
            return; // @codeCoverageIgnore
        }

        // If the file is not a unit test file, nothing to check.
        if (!MoodleUtil::isUnitTest($phpcsFile) && !MoodleUtil::isUnitTestRunning()) {
            return; // @codeCoverageIgnore
        }

        // We only want to do this once per file.
        $prevopentag = $phpcsFile->findPrevious(T_OPEN_TAG, $stackPtr - 1);
        if ($prevopentag !== false) {
            return; // @codeCoverageIgnore
        }

        // Get the file tokens, for ease of use.
        $tokens = $phpcsFile->getTokens();

        // These are the methods we are going to check.
        $allMethods = array_merge(self::$setUpMethods, self::$tearDownMethods);

        // Iterate over all the classes (hopefully only one, but that's not this sniff problem).
        $cStart = $stackPtr;
        while ($cStart = $phpcsFile->findNext(T_CLASS, $cStart + 1)) {
            // Only interested in classes that are unit test classes.
            if (MoodleUtil::isUnitTestCaseClass($phpcsFile, $cStart) === false) {
                continue;
            }

            // Iterate over all the methods in the class.
            $mStart = $cStart;
            while ($mStart = $phpcsFile->findNext(T_FUNCTION, $mStart + 1, $tokens[$cStart]['scope_closer'])) {
                $method = $phpcsFile->getDeclarationName($mStart);
                // Only interested in setUp and tearDown methods.
                if (!in_array($method, $allMethods)) {
                    continue;
                }

                // Iterate over all the parent:: calls in the method.
                $pStart = $mStart;
                $correctParentCalls = [];
                while ($pStart = $phpcsFile->findNext(T_PARENT, $pStart + 1, $tokens[$mStart]['scope_closer'])) {
                    // The next-next token should be the parent method being named.
                    $methodCall = $phpcsFile->findNext(T_STRING, $pStart + 1, $pStart + 3);
                    // If we are calling to an incorrect parent method, report it. No fixable.
                    if (
                        $methodCall !== false &&
                        $tokens[$methodCall]['content'] !== $method &&
                        in_array($tokens[$methodCall]['content'], $allMethods) // Other parent calls may be correct.
                    ) {
                        $wrongMethod = $tokens[$methodCall]['content'];
                        // We are calling to incorrect parent method.
                        $phpcsFile->addError(
                            'The %s() method in unit tests cannot call to parent::%s().',
                            $pStart,
                            'Incorrect' . ucfirst($method),
                            [$method, $wrongMethod]
                        );
                    }

                    // If we are calling to the correct parent method, annotate it.
                    if (
                        $methodCall !== false &&
                        $tokens[$methodCall]['content'] === $method
                    ) {
                        $correctParentCalls[] = $pStart;
                    }
                }

                // If there are multiple calls to correct parent, report it. Not fixable.
                if (count($correctParentCalls) > 1) {
                    $phpcsFile->addError(
                        'The %s() method in unit tests must call to parent::%s() only once.',
                        end($correctParentCalls),
                        'Multiple' . ucfirst($method),
                        [$method, $method]
                    );
                }

                // If there are no calls to correct parent, report it.
                if (count($correctParentCalls) === 0) {
                    // Unlikely case of empty method, report it and continue. Not fixable.
                    // Find the next thing that is not an empty token.
                    $ignore = \PHP_CodeSniffer\Util\Tokens::$emptyTokens;

                    $nextValidStatement = $phpcsFile->findNext(
                        $ignore,
                        $tokens[$mStart]['scope_opener'] + 1,
                        $tokens[$mStart]['scope_closer'],
                        true
                    );
                    if ($nextValidStatement === false) {
                        $phpcsFile->addError(
                            'The %s() method in unit tests must not be empty',
                            $mStart,
                            'Empty' . ucfirst($method),
                            [$method]
                        );

                        continue;
                    }

                    // If the method is not empty, let's report the missing call. Fixable.
                    $fix = $phpcsFile->addFixableError(
                        'The %s() method in unit tests must always call to parent::%s().',
                        $mStart,
                        'Missing' . ucfirst($method),
                        [$method, $method]
                    );

                    if ($fix) {
                        // If the current method is a setUp one, let's add the call at the beginning.
                        if (in_array($method, self::$setUpMethods)) {
                            $phpcsFile->fixer->beginChangeset();
                            $phpcsFile->fixer->addContent(
                                $this->findSetUpInsertionPoint($phpcsFile, $mStart),
                                "\n" . '        parent::' . $method . '();'
                            );
                            $phpcsFile->fixer->endChangeset();
                        }

                        // If the current method is a tearDown one, let's add the call at the end.
                        if (in_array($method, self::$tearDownMethods)) {
                            $phpcsFile->fixer->beginChangeset();
                            $phpcsFile->fixer->addContentBefore(
                                $tokens[$mStart]['scope_closer'] - 1,
                                '        parent::' . $method . '();' . "\n"
                            );
                            $phpcsFile->fixer->endChangeset();
                        }
                    }
                }
            }
        }
    }

    /**
     * Find the best insertion point for parent::setUp calls.
     *
     * While it's technically correct to insert the parent::setUp call at the beginning of the method,
     * it's better to insert it after some statements, like global or require/include ones.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $mStart The position of the method.
     * @return int The position where the parent::setUp method should be inserted.
     */
    private function findSetUpInsertionPoint(File $phpcsFile, int $mStart): int {
        // By default, we are going to insert it at the beginning.
        $insertionPoint = $phpcsFile->getTokens()[$mStart]['scope_opener'];

        // Let's find the first token in the method that is not a global, require or include.
        // Do it ignoring both whitespace and comments.
        $tokens = $phpcsFile->getTokens();
        $mEnd = $tokens[$mStart]['scope_closer'];

        $skipTokens = [T_WHITESPACE, T_COMMENT];
        $allowedTokens = [T_GLOBAL, T_REQUIRE, T_REQUIRE_ONCE, T_INCLUDE, T_INCLUDE_ONCE];

        while ($findPtr = $phpcsFile->findNext($skipTokens, $insertionPoint + 1, $mEnd, true)) {
            // If we find a token that is not allowed, stop looking, insertion point determined.
            if (!in_array($tokens[$findPtr]['code'], $allowedTokens)) {
                break;
            }

            // Arrived here, we can advance the insertion point until the end of the allowed statement.
            $insertionPoint = $phpcsFile->findEndOfStatement($findPtr, [T_COMMA]);
        }

        return $insertionPoint;
    }
}
