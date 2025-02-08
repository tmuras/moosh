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

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\Methods;

use MoodleHQ\MoodleCS\moodle\Tests\MoodleCSBaseTestCase;

/**
 * Test the MethodDeclarationSpacing sniff.
 *
 * @copyright  2024 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Methods\MethodDeclarationSpacingSniff
 */
class MethodDeclarationSpacingSniffTest extends MoodleCSBaseTestCase
{
    public function testMethodDeclarationSpacing(): void {
        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('moodle.Methods.MethodDeclarationSpacing');
        $this->setFixture(__DIR__ . '/../../fixtures/Methods/MethodDeclarationSpacing.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $errors = [
            43 => 4,
            45 => [
                'Expected 0 spaces after "("; 2 found',
                'Expected 0 spaces after "method2"; 2 found',
                'Expected 1 space after "function"; 2 found',
                'Expected 1 space after "final"; 2 found',
            ],
            49 => [
                'ZeroExpectedAfter',
                'ZeroExpectedAfter',
                'OneExpectedAfter',
            ],
            53 => 1,
            61 => 3,
        ];
        $warnings = [];
        $this->setErrors($errors);
        $this->setWarnings($warnings);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }
}
