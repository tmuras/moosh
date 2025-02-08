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

namespace MoodleHQ\MoodleCS\moodle\Tests;

/**
 * Test the PHPUnitTestReturnTypeSniff sniff.
 *
 * @copyright  2022 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\PHPUnit\TestReturnTypeSniff
 */
class PHPUnitTestReturnTypeTest extends MoodleCSBaseTestCase
{
    /**
     * Data provider for self::testPHPUnitTestReturnType
     */
    public function providerPHPUnitTestReturnType(): array {
        return [
            'Provider Casing' => [
                'fixture' => 'fixtures/phpunit/TestReturnType/returntypes.php',
                'errors' => [
                ],
                'warnings' => [
                    6 => 'Test method test_one() is missing a return type',
                    27 => 'Test method test_with_a_return() is missing a return type',
                    32 => 'Test method test_with_another_return() is missing a return type',
                    38 => 'Test method test_with_empty_return() is missing a return type',
                ],
            ],
        ];
    }

    /**
     * Test the moodle.PHPUnit.TestReturnType sniff
     *
     * @param string $fixture relative path to fixture to use.
     * @param array $errors array of errors expected.
     * @param array $warnings array of warnings expected.
     * @dataProvider providerPHPUnitTestReturnType
     */
    public function testPHPUnitTestReturnType(
        string $fixture,
        array $errors,
        array $warnings
    ): void {
        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('moodle.PHPUnit.TestReturnType');
        $this->setFixture(__DIR__ . '/' . $fixture);

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors($errors);
        $this->setWarnings($warnings);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }
}
