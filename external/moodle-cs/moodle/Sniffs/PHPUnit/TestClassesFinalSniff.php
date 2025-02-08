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
use PHPCSUtils\Utils\ObjectDeclarations;

/**
 * Checks that test classes are declared as final.
 *
 * @copyright  2024 Andrew Lyons <adrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class TestClassesFinalSniff implements Sniff
{
    public function register() {
        return [
            T_OPEN_TAG,
        ];
    }

    public function process(File $file, $pointer) {
        // If the file is not a unit test file, nothing to check.
        if (!MoodleUtil::isUnitTest($file) && !MoodleUtil::isUnitTestRunning()) {
            return; // @codeCoverageIgnore
        }

        // If we aren't checking Moodle 4.4dev (404) and up, nothing to check.
        // Make and exception for codechecker phpunit tests, so they are run always.
        if (!MoodleUtil::meetsMinimumMoodleVersion($file, 404) && !MoodleUtil::isUnitTestRunning()) {
            return; // @codeCoverageIgnore
        }

        // Get the file tokens, for ease of use.
        $tokens = $file->getTokens();

        // We only want to do this once per file.
        $prevopentag = $file->findPrevious(T_OPEN_TAG, $pointer - 1);
        if ($prevopentag !== false) {
            return; // @codeCoverageIgnore
        }

        $cStart = $pointer;
        while ($cStart = $file->findNext(T_CLASS, $cStart + 1)) {
            if (MoodleUtil::isUnitTestCaseClass($file, $cStart) === false) {
                // This class does not relate to a unit test.
                continue;
            }
            $className = ObjectDeclarations::getName($file, $cStart);
            if (substr($className, -5) !== '_test') {
                continue;
            }

            $classInfo = ObjectDeclarations::getClassProperties($file, $cStart);

            if ($classInfo['is_final']) {
                // Already final.
                continue;
            }

            if ($classInfo['is_abstract']) {
                // See if this class has any abstract methods.
                $mStart = $cStart + 1;
                $hasAbstractMethod = false;

                while ($mStart = $file->findNext(T_ABSTRACT, $mStart, $tokens[$cStart]['scope_closer']) !== false) {
                    $hasAbstractMethod = true;
                    break;
                }
                if ($hasAbstractMethod) {
                    $file->addWarning(
                        'Unit test %s should be declared as final and not abstract.',
                        $cStart,
                        'UnitTestClassesFinal',
                        [$className],
                    );
                } else {
                    $fix = $file->addFixableWarning(
                        'Unit test %s should be declared as final and not abstract.',
                        $cStart,
                        'UnitTestClassesFinal',
                        [$className],
                    );

                    if ($fix) {
                        $file->fixer->beginChangeset();
                        $file->fixer->replaceToken($classInfo['abstract_token'], 'final');
                        $file->fixer->endChangeset();
                    }
                }
            } elseif (!$classInfo['is_final']) {
                $fix = $file->addFixableWarning(
                    'Unit test %s should be declared as final.',
                    $cStart,
                    'UnitTestClassesFinal',
                    [$className],
                );

                if ($fix) {
                    $file->fixer->beginChangeset();
                    $file->fixer->addContentBefore($cStart, 'final ');
                    $file->fixer->endChangeset();
                }
            }
        }
    }
}
