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

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\Commenting;

use MoodleHQ\MoodleCS\moodle\Tests\MoodleCSBaseTestCase;

/**
 * Test the CategorySniff sniff.
 *
 * @copyright  2024 onwards Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Commenting\ValidTagsSniff
 */
class ValidTagsSniffTest extends MoodleCSBaseTestCase
{
    /**
     * @dataProvider provider
     */
    public function testValidTags(
        string $fixturePath,
        string $fixtureSource,
        array $errors,
        array $warnings
    ): void {
        $fixtureSource = sprintf("%s/fixtures/ValidTags/%s.php", __DIR__, $fixtureSource);

        $this->setStandard('moodle');
        $this->setSniff('moodle.Commenting.ValidTags');
        $this->setFixture($fixtureSource, $fixturePath);
        $this->setWarnings($warnings);
        $this->setErrors($errors);

        $this->verifyCsResults();
    }

    public static function provider(): array {
        return [
            'Unit test file' => [
                'fixturePath' => 'lib/tests/example_test.php',
                'fixtureSource' => 'unit_test',
                'errors' => [
                    12 => 'Incorrect docblock tag "@returns". Should be "@return"',
                    13 => 'Invalid docblock tag "@void"',
                    58 => 'Invalid docblock tag "@small"',
                    59 => 'Invalid docblock tag "@zzzing"',
                    60 => 'Invalid docblock tag "@inheritdoc"',
                ],
                'warnings' => [],
            ],
            'Standard file' => [
                'fixturePath' => 'lib/classes/example.php',
                'fixtureSource' => 'general',
                'errors' => [
                    28 => 'Invalid docblock tag "@covers"',
                    29 => 'Invalid docblock tag "@dataProvider"',
                    30 => 'Invalid docblock tag "@group"',
                    31 => 'Invalid docblock tag "@small"',
                    32 => 'Invalid docblock tag "@zzzing"',
                    33 => 'Invalid docblock tag "@inheritdoc"',
                    42 => 'Invalid docblock tag "@void" is not supported.',
                    51 => 'Incorrect docblock tag "@returns". Should be "@return"',
                ],
                'warnings' => [
                    52 => 'Docblock tag "@version" is not recommended.',
                ],
            ],
        ];
    }
}
