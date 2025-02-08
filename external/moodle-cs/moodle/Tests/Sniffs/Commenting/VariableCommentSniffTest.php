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
 * Test the VariableCommentSniff sniff.
 *
 * @copyright  2024 onwards Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Commenting\VariableCommentSniff
 */
class VariableCommentSniffTest extends MoodleCSBaseTestCase
{
    /**
     * @dataProvider fixtureProvider
     */
    public function testSniffWithFixtures(
        string $fixture,
        array $errors,
        array $warnings
    ): void {
        $this->setStandard('moodle');
        $this->setSniff('moodle.Commenting.VariableComment');
        $this->setFixture(sprintf("%s/fixtures/VariableComment/%s.php", __DIR__, $fixture));
        $this->setErrors($errors);
        $this->setWarnings($warnings);

        $this->verifyCsResults();
    }

    public static function fixtureProvider(): array {
        $cases = [
            'Multiline docblocks' => [
                'fixture' => 'multiline',
                'errors' => [
                    23 => 'Expected "int" but found "integer" for @var tag in member variable comment',
                    31 => 'Only one @var tag is allowed in a member variable comment',
                    51 => 'You must use "/**" style comments for a member variable comment',
                    53 => 'Missing member variable doc comment',
                    67 => 'Content missing for @see tag in member variable comment',
                    75 => 'Missing @var tag in member variable comment',
                    90 => 'The @var tag must be the first tag in a member variable comment',
                ],
                'warnings' => [
                    82 => '@deprecated tag is not allowed in member variable comment',
                ],
            ],
            'Single line variable declarations' => [
                'fixture' => 'singleline',
                'errors' => [
                    12 => 'Expected "int" but found "integer" for @var tag in member variable comment',
                    22 => 'Missing @var tag in member variable comment',
                    25 => 'Missing member variable doc comment',
                    36 => 'Expected "int" but found "INT" for @var tag in member variable comment',
                    39 => 'Content missing for @var tag in member variable comment',
                ],
                'warnings' => [],
            ],
            'Type corrections' => [
                'fixture' => 'typechecks',
                'errors' => [
                    6 => 'Expected "int" but found "INT" for @var tag in member variable comment',
                    9 => 'Expected "string" but found "String" for @var tag in member variable comment',
                    12 => 'Expected "float" but found "double" for @var tag in member variable comment',
                    15 => 'Expected "float" but found "real" for @var tag in member variable comment',
                    21 => 'Expected "array" but found "ARRAY()" for @var tag in member variable comment',
                    27 => 'Expected "bool" but found "boolean" for @var tag in member variable comment',
                    30 => 'Expected "bool[]" but found "boolean[]" for @var tag in member variable comment',
                    33 => 'Expected "bool[]" but found "array(int => bool)" for @var tag in member variable comment',
                    36 => 'Expected "array" but found "array(int > bool)" for @var tag in member variable comment',
                    39 => 'Expected "int[]" but found "array(int)" for @var tag in member variable comment',
                ],
                'warnings' => [],
            ],
            'Constructor with mixed CPP' => [
                'fixture' => 'constructor_with_mixed_property_promotion',
                'errors' => [
                    21 => 'Missing member variable doc comment',
                ],
                'warnings' => [
                ],
            ],
        ];

        if (version_compare(PHP_VERSION, '8.0.0') >= 0) {
            // Since PHP 8.0.
            $cases['Constructor Property Promotions'] = [
                'fixture' => 'constructor_property_promotion',
                'errors' => [
                    7 => 'Missing member variable doc comment',
                    13 => 'Expected "bool" but found "BOOLEAN" for @var tag in member variable comment',
                    20 => 'The @var tag must be the first tag in a member variable commen',
                ],
                'warnings' => [
                    19 => '@deprecated tag is not allowed in member variable comment',
                ],
            ];
        }

        if (version_compare(PHP_VERSION, '8.1.0') >= 0) {
            // Since PHP 8.1.
            $cases['Constructor Property Promotions with Readonly params'] = [
                'fixture' => 'constructor_property_promotion_readonly',
                'errors' => [
                    7 => 'Missing member variable doc comment',
                    13 => 'Expected "bool" but found "BOOLEAN" for @var tag in member variable comment',
                    20 => 'The @var tag must be the first tag in a member variable commen',
                ],
                'warnings' => [
                    19 => '@deprecated tag is not allowed in member variable comment',
                ],
            ];
        }

        return $cases;
    }
}
