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

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\PHPUnit;

use MoodleHQ\MoodleCS\moodle\Tests\MoodleCSBaseTestCase;

/**
 * Test the TestClassesFinalSniff sniff.
 *
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\PHPUnit\TestClassesFinalSniff
 */
class TestclassesFinalSniffTest extends MoodleCSBaseTestCase
{
    /**
     * Data provider for self::testPHPUnitClassesFinal
     */
    public static function phpunitClassesFinalProvider(): array {
        return [
            'Standard fixes' => [
                'fixture' => 'testclassesfinal',
                'errors' => [
                ],
                'warnings' => [
                    15 => 'example_abstract_test_with_abstract_children_test should be declared as final and not abstract.',
                    19 => 'example_abstract_test should be declared as final and not abstract.',
                    23 => 'example_standard_test should be declared as final.',
                ],
            ],
        ];
    }

    /**
     * @dataProvider phpunitClassesFinalProvider
     */
    public function testPHPUnitClassesFinal(
        string $fixture,
        array $errors,
        array $warnings
    ): void {
        $this->setStandard('moodle');
        $this->setSniff('moodle.PHPUnit.TestClassesFinal');
        $this->setFixture(sprintf("%s/fixtures/%s.php", __DIR__, $fixture));
        $this->setWarnings($warnings);
        $this->setErrors($errors);

        $this->verifyCsResults();
    }
}
