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

namespace MoodleHQ\MoodleCS\moodle\Tests\Files;

/**
 * Test the MoodleInternalSniff sniff.
 *
 * @copyright  2013 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Files\MoodleInternalSniff
 */
class MoodleInternalTest extends \MoodleHQ\MoodleCS\moodle\Tests\MoodleCSBaseTestCase
{
    /**
     * @dataProvider moodleFilesMoodleInternalProvider
     */
    public function testMoodleFilesMoodleInternal(
        string $fixture,
        array $warnings,
        array $errors
    ) {
        // Contains class_alias, which is not a side-effect.
        $this->setStandard('moodle');
        $this->setSniff('moodle.Files.MoodleInternal');
        $this->setFixture(__DIR__ . '/fixtures/moodleinternal/' . $fixture . '.php');
        $this->setWarnings($warnings);
        $this->setErrors($errors);

        $this->verifyCsResults();
    }

    /**
     * Data provider for testMoodleFilesMoodleInternal tests.
     * @return array
     */
    public static function moodleFilesMoodleInternalProvider(): array {
        return [
            [
                'problem',
                [],
                [
                    19 => 'Expected MOODLE_INTERNAL check or config.php inclusion',
                ],
            ],
            [
                'warning',
                [
                    32 => 'Expected MOODLE_INTERNAL check or config.php inclusion. Multiple artifacts',
                ],
                [],
            ],
            [
                'nowarning',
                [],
                [],
            ],
            [
                'declare_ok',
                [],
                [],
            ],
            [
                'enum_ok',
                [],
                [],
            ],
            [
                'namespace_ok',
                [],
                [],
            ],
            [
                'no_moodle_cookie_ok',
                [],
                [],
            ],
            [
                'tests/behat/behat_mod_workshop',
                [],
                [],
            ],
            [
                'lib/behat/behat_mod_workshop',
                [],
                [],
            ],
            [
                'lang/en/repository_dropbox',
                [],
                [],
            ],
            [
                'namespace_with_use_ok',
                [],
                [],
            ],
            [
                'old_style_if_die_ok',
                [
                    24 => 'Old MOODLE_INTERNAL check detected. Replace it by',
                ],
                [],
            ],
            [
                'no_relevant_ok',
                [],
                [],
            ],
            [
                'unexpected',
                [
                    17 => 'MoodleInternalNotNeeded',
                ],
                [],
            ],
            [
                'class_alias',
                [],
                [],
            ],
            [
                'class_alias_extra',
                [],
                [
                    25 => 'Expected MOODLE_INTERNAL check or config.php inclusion',
                ],
            ],
            [
                'class_alias_defined',
                [
                    17 => 'MoodleInternalNotNeeded',
                ],
                [],
            ],
            [
                'attribute_ok',
                [],
                [],
            ],
        ];
    }
}
