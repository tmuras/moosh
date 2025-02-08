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
 * Test the MissingDocblockSniff sniff.
 *
 * @copyright  2024 onwards Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Commenting\MissingDocblockSniff
 */
class MissingDocblockSniffTest extends MoodleCSBaseTestCase
{
    /**
     * @dataProvider docblockCorrectnessProvider
     */
    public function testMissingDocblockSniff(
        string $fixture,
        ?string $fixtureFilename,
        array $errors,
        array $warnings
    ): void {
        $this->setStandard('moodle');
        $this->setSniff('moodle.Commenting.MissingDocblock');
        $this->setFixture(sprintf("%s/fixtures/MissingDocblock/%s.php", __DIR__, $fixture), $fixtureFilename);
        $this->setWarnings($warnings);
        $this->setErrors($errors);
        $this->setComponentMapping([
            'local_codechecker' => dirname(__DIR__),
        ]);

        $this->verifyCsResults();
    }

    public static function docblockCorrectnessProvider(): array {
        $cases = [
            'Multiple artifacts in a file, check messages' => [
                'fixture' => 'multiple_artifacts',
                'fixtureFilename' => null,
                'errors' => [
                    1 => 'Missing docblock for file multiple_artifacts.php',
                    34 => 'Missing docblock for function missing_docblock_in_function',
                    38 => 'Missing docblock for class missing_docblock_in_class',
                    95 => 'Missing docblock for interface missing_docblock_interface',
                    118 => 'Missing docblock for trait missing_docblock_trait',
                    151 => 'Missing docblock for function test_method2',
                    159 => 'Missing docblock for function test_method',
                    166 => 'Missing docblock for function test_method',
                    170 => 'Missing docblock for class example_extends',
                    171 => 'Missing docblock for function test_method',
                    175 => 'Missing docblock for class example_implements',
                    176 => 'Missing docblock for function test_method',
                ],
                'warnings' => [
                ],
            ],
            'Multiple artifacts in a file, check codes' => [
                'fixture' => 'multiple_artifacts',
                'fixtureFilename' => null,
                'errors' => [
                    1 => 'moodle.Commenting.MissingDocblock.File',
                    34 => 'moodle.Commenting.MissingDocblock.Function',
                    38 => 'moodle.Commenting.MissingDocblock.Class',
                    95 => 'moodle.Commenting.MissingDocblock.Interface',
                    118 => 'moodle.Commenting.MissingDocblock.Trait',
                    151 => 'moodle.Commenting.MissingDocblock.Function',
                    159 => 'moodle.Commenting.MissingDocblock.Function',
                    166 => 'moodle.Commenting.MissingDocblock.Function',
                    170 => 'moodle.Commenting.MissingDocblock.Class',
                    171 => 'moodle.Commenting.MissingDocblock.Function',
                    175 => 'moodle.Commenting.MissingDocblock.Class',
                    176 => 'moodle.Commenting.MissingDocblock.Function',
                ],
                'warnings' => [
                ],
            ],
            'File level tag, no class' => [
                'fixture' => 'class_without_docblock',
                'fixtureFilename' => null,
                'errors' => [
                    11 => 'Missing docblock for class class_without_docblock',
                ],
                'warnings' => [],
            ],
            'Class only (incorrect whitespace)' => [
                'fixture' => 'class_only_with_incorrect_whitespace',
                'fixtureFilename' => null,
                'errors' => [
                    11 => 'Missing docblock for class class_only_with_incorrect_whitespace',
                ],
                'warnings' => [],
            ],
            'Class only (correct)' => [
                'fixture' => 'class_only',
                'fixtureFilename' => null,
                'errors' => [],
                'warnings' => [],
            ],
            'Class only with attributes (correct)' => [
                'fixture' => 'class_only_with_attributes',
                'fixtureFilename' => null,
                'errors' => [],
                'warnings' => [],
            ],
            'Class only with attributes and incorrect whitespace' => [
                'fixture' => 'class_only_with_attributes_incorrect_whitespace',
                'fixtureFilename' => null,
                'errors' => [
                    13 => 'Missing docblock for class class_only_with_attributes_incorrect_whitespace',
                    20 => 'Missing docblock for function method_only_with_attributes_incorrect_whitespace',
                ],
                'warnings' => [],
            ],
            'Interface only with attributes and incorrect whitespace' => [
                'fixture' => 'interface_only_with_attributes_incorrect_whitespace',
                'fixtureFilename' => null,
                'errors' => [
                    13 => 'Missing docblock for interface interface_only_with_attributes_incorrect_whitespace',
                ],
                'warnings' => [],
            ],
            'Trait only with attributes and incorrect whitespace' => [
                'fixture' => 'trait_only_with_attributes_incorrect_whitespace',
                'fixtureFilename' => null,
                'errors' => [
                    13 => 'Missing docblock for trait trait_only_with_attributes_incorrect_whitespace',
                ],
                'warnings' => [],
            ],
            'Class and file (correct)' => [
                'fixture' => 'class_and_file',
                'fixtureFilename' => null,
                'errors' => [],
                'warnings' => [],
            ],
            'Interface only (correct)' => [
                'fixture' => 'interface_only',
                'fixtureFilename' => null,
                'errors' => [],
                'warnings' => [],
            ],
            'Trait only (correct)' => [
                'fixture' => 'trait_only',
                'fixtureFilename' => null,
                'errors' => [],
                'warnings' => [],
            ],
            'Constants' => [
                'fixture' => 'imported_constants',
                'fixtureFilename' => null,
                'errors' => [
                    16 => 'Missing docblock for constant UNDOCUMENTED_CONST',
                    32 => 'Missing docblock for constant example_class::UNDOCUMENTED_CONST',
                ],
                'warnings' => [],
            ],
            'Testcase' => [
                'fixture' => 'testcase_class',
                'fixtureFilename' => '/lib/tests/example_test.php',
                'errors' => [
                    3 => 'Missing docblock for class example_test',
                ],
                'warnings' => [
                    15 => 'Missing docblock for function this_is_not_a_test in testcase',
                    18 => 'Missing docblock for function this_is_a_dataprovider in testcase',
                ],
            ],
            'With and without Overrides attributes' => [
                'fixture' => 'with_and_without_overrides',
                'fixtureFilename' => null,
                'errors' => [
                    1 => 'Missing docblock for file with_and_without_overrides.php',
                    11 => 'Missing docblock for function has_override',
                    13 => 'Missing docblock for function no_override',
                    21 => 'Missing docblock for function has_override',
                    23 => 'Missing docblock for function no_override',
                    33 => 'Missing docblock for function no_override',
                    43 => 'Missing docblock for function no_override',
                    53 => 'Missing docblock for function no_override',
                ],
                'warnings' => [
                ],
            ],
            'Anonymous class as only class in file (documented)' => [
                'fixture' => 'entire_anonymous_class_documented',
                'fixtureFilename' => null,
                'errors' => [
                ],
                'warnings' => [
                ],
            ],
            'Anonymous class as only class in file (undocumented)' => [
                'fixture' => 'entire_anonymous_class',
                'fixtureFilename' => null,
                'errors' => [
                    5 => 'Missing docblock for class anonymous class',
                ],
                'warnings' => [
                ],
            ],
            'Anonymous class as member of method' => [
                'fixture' => 'nested_anonymous_class',
                'fixtureFilename' => null,
                'errors' => [
                ],
                'warnings' => [
                ],
            ],
        ];

        if (version_compare(PHP_VERSION, '8.0.0') >= 0) {
            $cases['Multiline attributes'] = [
                'fixture' => 'docblock_with_multiline_attributes',
                'fixtureFilename' => null,
                'errors' => [
                    59 => 'Missing docblock for class class_multiline_attribute_space_between',
                    69 => 'Missing docblock for function method_multiline_attribute_space_between',
                    81 => 'Missing docblock for interface interface_multiline_attribute_space_between',
                    92 => 'Missing docblock for trait trait_multiline_attribute_space_between',
                ],
                'warnings' => [],
            ];
        }

        if (version_compare(PHP_VERSION, '8.1.0') >= 0) {
            $cases['Enum only (correct)'] = [
                'fixture' => 'enum_only',
                'fixtureFilename' => null,
                'errors' => [],
                'warnings' => [],
            ];
        }

        if (version_compare(PHP_VERSION, '8.3.0') >= 0) {
            $cases['Typed constants'] = [
                'fixture' => 'typed_constants',
                'fixtureFilename' => null,
                'errors' => [
                    16 => 'Missing docblock for constant UNDOCUMENTED_CONST',
                    32 => 'Missing docblock for constant example_class::UNDOCUMENTED_CONST',
                ],
                'warnings' => [],
            ];
        }

        return $cases;
    }
}
