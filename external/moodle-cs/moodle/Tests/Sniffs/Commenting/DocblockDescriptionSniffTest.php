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
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Ruleset;

/**
 * Test the MissingDocblockSniff sniff.
 *
 * @copyright  2024 onwards Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Commenting\DocblockDescriptionSniff
 */
class DocblockDescriptionSniffTest extends MoodleCSBaseTestCase
{
    /**
     * @dataProvider fixtureProvider
     */
    public function testFixtures(
        string $fixture,
        ?string $fixtureFilename,
        array $errors,
        array $warnings
    ): void {
        $this->setStandard('moodle');
        $this->setSniff('moodle.Commenting.DocblockDescription');
        $this->setFixture(sprintf("%s/fixtures/DocblockDescription/%s.php", __DIR__, $fixture), $fixtureFilename);
        $this->setWarnings($warnings);
        $this->setErrors($errors);

        $this->verifyCsResults();
    }

    public static function fixtureProvider(): array {
        $cases = [
            'Standard tests' => [
                'fixture' => 'standard',
                'fixtureFilename' => null,
                'errors' => [
                    22 => 'No one-line description found in phpdocs for docblock of function method_with_param_docblock',
                    47 => 'No one-line description found in phpdocs for docblock of class class_with_docblock_but_no_description',
                    51 => 'No one-line description found in phpdocs for docblock of interface int_with_docblock_but_no_description',
                    55 => 'No one-line description found in phpdocs for docblock of trait trait_with_docblock_but_no_description',
                    60 => 'No one-line description found in phpdocs for docblock of '
                        . 'function function_with_docblock_but_no_description',
                ],
                'warnings' => [
                ],
            ],
            'No file docblock' => [
                'fixture' => 'no_file_docblock',
                'fixtureFilename' => null,
                'errors' => [
                ],
                'warnings' => [
                ],
            ],
            'No description for file docblock' => [
                'fixture' => 'no_description_in_file',
                'fixtureFilename' => null,
                'errors' => [
                    1 => 'No one-line description found in phpdocs for docblock of file no_description_in_file',
                ],
                'warnings' => [
                ],
            ],
        ];

        return $cases;
    }
}
