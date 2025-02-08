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

use MoodleHQ\MoodleCS\moodle\Util\MoodleUtil;

/**
 * Test the ValidFunctionName sniff.
 *
 * @copyright  2022 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\NamingConventions\ValidFunctionNameSniff
 */
class NamingConventionsValidFunctionNameTest extends MoodleCSBaseTestCase
{
    /**
     * Data provider for self::testNamingConventionsValidFunctionName
     */
    public function providerNamingConventionsValidFunctionName() {
        return [
            'Correct' => [
                'fixture' => 'fixtures/namingconventions/validfunctionname_correct.php',
                'errors' => [],
                'warnings' => [],
            ],
            'Lower' => [
                'fixture' => 'fixtures/namingconventions/validfunctionname_lower.php',
                'errors' => [
                    5 => 'Public method name "class_with_correct_function_names::notUpperPlease" must be in lower-case',
                    11 => 'moodle.NamingConventions.ValidFunctionName.LowercaseMethod',
                    15 => '@Message: method name "interface_with_correct_function_names::withoutScope"',
                    20 => 'moodle.NamingConventions.ValidFunctionName.LowercaseFunction',
                ],
                'warnings' => [],
            ],
            'Global' => [
                'fixture' => 'fixtures/namingconventions/validfunctionname_global.php',
                'errors' => [
                    4 => 'moodle.NamingConventions.ValidFunctionName.MagicLikeFunction',
                    8 => '"jsonSerialize" must be lower-case letters only',
                ],
                'warnings' => [],
            ],
            'Scoped' => [
                'fixture' => 'fixtures/namingconventions/validfunctionname_scoped.php',
                'errors' => [
                    '5' => '__magiclike" is invalid; only PHP magic methods should be prefixed with a double underscore',
                ],
                'warnings' => [],
            ],
        ];
    }

    /**
     * Test the moodle.NamingConventions.ValidFunctionName sniff
     *
     * @param string $fixture relative path to fixture to use.
     * @param array $errors array of errors expected.
     * @param array $warnings array of warnings expected.
     * @dataProvider providerNamingConventionsValidFunctionName
     */
    public function testNamingConventionsValidFunctionName(string $fixture, array $errors, array $warnings) {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('moodle.NamingConventions.ValidFunctionName');
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
