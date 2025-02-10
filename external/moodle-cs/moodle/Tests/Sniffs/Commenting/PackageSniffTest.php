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
 * Test the TestCaseNamesSniff sniff.
 *
 * @copyright  2024 onwards Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Commenting\PackageSniff
 */
class PackageSniffTest extends MoodleCSBaseTestCase
{
    /**
     * Test that various checks are not performed when there isn't any component available.
     */
    public function testPackageOnMissingComponent(): void {
        $this->setStandard('moodle');
        $this->setSniff('moodle.Commenting.Package');
        $this->setFixture(__DIR__ . '/fixtures/package_tags_nocheck.php');
        $this->setComponentMapping([]); // No components available.

        $this->setWarnings([]);
        $this->setErrors([]);

        $this->verifyCsResults();
    }

    /**
     * @dataProvider packageCorrectnessProvider
     */
    public function testPackageCorrectness(
        string $fixture,
        array $errors,
        array $warnings
    ): void {
        $this->setStandard('moodle');
        $this->setSniff('moodle.Commenting.Package');
        $this->setFixture(sprintf("%s/fixtures/%s.php", __DIR__, $fixture));
        $this->setWarnings($warnings);
        $this->setErrors($errors);
        $this->setComponentMapping([
            'local_codechecker' => dirname(__DIR__),
        ]);

        $this->verifyCsResults();
    }

    public static function packageCorrectnessProvider(): array {
        return [
            'Standard fixes' => [
                'fixture' => 'package_tags',
                'errors' => [
                    18 => 'DocBlock missing a @package tag for function package_missing. Expected @package local_codechecker',
                    31 => 'DocBlock missing a @package tag for class package_absent. Expected @package local_codechecker',
                    42 => '@package tag for function package_wrong_in_function. Expected local_codechecker, found wrong_package.',
                    48 => '@package tag for class package_wrong_in_class. Expected local_codechecker, found wrong_package.',
                    57 => 'More than one @package tag found in function package_multiple_in_function',
                    64 => 'More than one @package tag found in class package_multiple_in_class',
                    71 => 'More than one @package tag found in function package_multiple_in_function_all_wrong',
                    78 => 'More than one @package tag found in class package_multiple_in_class_all_wrong',
                    85 => 'More than one @package tag found in interface package_multiple_in_interface_all_wrong',
                    92 => 'More than one @package tag found in trait package_multiple_in_trait_all_wrong',
                    101 => 'missing a @package tag for interface missing_package_interface. Expected @package',
                    106 => '@package tag for interface incorrect_package_interface. Expected local_codechecker, found',
                    124 => 'DocBlock missing a @package tag for trait missing_package_trait. Expected @package',
                    129 => 'Incorrect @package tag for trait incorrect_package_trait. Expected local_codechecker, found',
                ],
                'warnings' => [],
            ],
            'File level tag (wrong)' => [
                'fixture' => 'package_tags_file_wrong',
                'errors' => [
                    20 => 'Incorrect @package tag for file package_tags_file_wrong.php. Expected local_codechecker, found core.',
                ],
                'warnings' => [],
            ],
            'File level tag (right)' => [
                'fixture' => 'package_tags_file_right',
                'errors' => [],
                'warnings' => [],
            ],
        ];
    }
}
