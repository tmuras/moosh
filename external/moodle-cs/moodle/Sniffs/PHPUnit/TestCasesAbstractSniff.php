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
 * Checks that testcase classes are declared as abstract.
 *
 * @copyright  2024 Andrew Lyons <adrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class TestCasesAbstractSniff implements Sniff
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
            if (substr($className, -9) !== '_testcase') {
                continue;
            }

            $classInfo = ObjectDeclarations::getClassProperties($file, $cStart);

            if (!$classInfo['is_abstract']) {
                $fix = $file->addFixableWarning(
                    'Testcase %s should be declared as abstract.',
                    $cStart,
                    'UnitTestClassesAbstract',
                    [$className],
                );

                if ($fix) {
                    $file->fixer->beginChangeset();
                    $file->fixer->addContentBefore($cStart, 'abstract ');
                    $file->fixer->endChangeset();
                }
            }
        }
    }
}
