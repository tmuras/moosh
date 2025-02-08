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
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Commenting\FileExpectedTagsSniff
 */
class FileExpectedTagsSniffTest extends MoodleCSBaseTestCase
{
    /**
     * @dataProvider fixtureProvider
     */
    public function testSniffWithFixtures(
        string $fixture,
        array $errors,
        array $warnings,
        array $configValues
    ): void {
        $this->setStandard('moodle');
        $this->setSniff('moodle.Commenting.FileExpectedTags');
        $this->setFixture(sprintf("%s/fixtures/FileExpectedTags/%s.php", __DIR__, $fixture));
        $this->setErrors($errors);
        $this->setWarnings($warnings);

        foreach ($configValues as $configKey => $configValue) {
            $this->addCustomConfig($configKey, $configValue);
        }

        $this->verifyCsResults();
    }

    public static function fixtureProvider(): array {
        $cases = [
            'Single artifact, Single docblock' => [
                'fixture' => 'single_artifact_single_docblock',
                'errors' => [],
                'warnings' => [],
                'configValues' => [],
            ],
            'Single artifact, Single docblock (missing)' => [
                'fixture' => 'single_artifact_single_docblock_missing',
                'errors' => [
                    3 => [
                        'Missing @copyright tag',
                        'Missing @license tag',
                    ],
                ],
                'warnings' => [],
                'configValues' => [],
            ],
            'Single artifact, Single docblock (missing tags)' => [
                'fixture' => 'single_artifact_single_docblock_missing_tags',
                'errors' => [
                    3 =>  [
                        'Missing @copyright tag',
                        'Missing @license tag',
                    ],
                ],
                'warnings' => [],
                'configValues' => [],
            ],
            'Single artifact, multiple docblocks' => [
                'fixture' => 'single_artifact_multiple_docblock',
                'errors' => [
                ],
                'warnings' => [],
                'configValues' => [],
            ],
            'Single artifact, multiple docblocks (missing)' => [
                'fixture' => 'single_artifact_multiple_docblock_missing',
                'errors' => [
                    // Note: Covered by MissingDocblockSniff.
                ],
                'warnings' => [],
                'configValues' => [],
            ],
            'Single artifact, multiple docblocks (wrong)' => [
                'fixture' => 'single_artifact_multiple_docblock_wrong',
                'errors' => [],
                'warnings' => [],
                'configValues' => [],
            ],
            'Multiple artifacts, File docblock' => [
                'fixture' => 'multiple_artifact_has_file_docblock',
                'errors' => [],
                'warnings' => [],
                'configValues' => [],
            ],
            'Multiple artifacts, File docblock (missing)' => [
                'fixture' => 'multiple_artifact_has_file_docblock_missing',
                'errors' => [],
                'warnings' => [],
                'configValues' => [],
            ],
            'Multiple artifacts, File docblock (wrong data)' => [
                'fixture' => 'multiple_artifact_has_file_docblock_wrong',
                'errors' => [
                    3 => 'Missing @copyright tag',
                ],
                'warnings' => [
                    6 => 'Invalid @license tag. Value "Me!" does not match expected format',
                ],
                'configValues' => [],
            ],
            'Multiple artifacts, File docblock, Custom license value set' => [
                'fixture' => 'multiple_artifact_has_file_docblock_wrong',
                'errors' => [
                    3 => 'Missing @copyright tag',
                ],
                'warnings' => [],
                'configValues' => [
                    'moodleLicenseRegex' => '@Me!@',
                ],
            ],
            'Multiple artifacts, File docblock, No license value set' => [
                'fixture' => 'multiple_artifact_has_file_docblock_wrong',
                'errors' => [
                    3 => 'Missing @copyright tag',
                ],
                'warnings' => [],
                'configValues' => [
                    'moodleLicenseRegex' => '',
                ],
            ],
            'Multiple artifacts, File docblock (missing tags)' => [
                'fixture' => 'multiple_artifact_has_file_docblock_missing_tags',
                'errors' => [
                    3 => [
                        'Missing @copyright tag',
                        'Missing @license tag',
                    ],
                ],
                'warnings' => [],
                'configValues' => [],
            ],
        ];

        return $cases;
    }
}
