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

use MoodleHQ\MoodleCS\moodle\Tests\MoodleCSBaseTestCase;

/**
 * Test the LangFilesOrderingSniff sniff.
 *
 * @copyright  2024 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Files\LangFilesOrderingSniff
 */
class LangFilesOrderingTest extends MoodleCSBaseTestCase
{
    /**
     * @dataProvider filesOrderingProvider
     */
    public function testLangFilesOrdering(
        string $fixture,
        array $warnings,
        array $errors
    ) {
        $this->setStandard('moodle');
        $this->setSniff('moodle.Files.LangFilesOrdering');
        $this->setFixture(__DIR__ . '/fixtures/langFilesOrdering/' . $fixture);
        $this->setWarnings($warnings);
        $this->setErrors($errors);

        $this->verifyCsResults();
    }

    /**
     * Data provider for testLangFilesOrdering tests.
     *
     * @return array
     */
    public static function filesOrderingProvider(): array {
        return [
            'processed correct' => [
                'lang/en/correct.php',
                [],
                [],
            ],
            'processed with problems' => [
                'lang/en/withWarningsAndErrors.php',
                [
                    27 => '"modvisiblewithstealth_help" is not in the correct order',
                    30 => 1,
                    34 => 1,
                    37 => 1,
                    38 => 1,
                    45 => 1,
                    46 => 1,
                    47 => 1,
                    51 => 1,
                    60 => 'Unexpected comment found. Auto-fixing will not work after this',
                    61 => 1,
                    63 => 1,
                    64 => 1,
                ],
                [
                    31 => 'Variable "$anothervar" is not expected',
                    33 => 'Unexpected string syntax, it should be',
                    40 => 'The string key "yourself" is duplicated',
                    42 => 'Unexpected string end',
                ],
            ],
            'without strings' => [
                'lang/en/withoutLangStrings.php',
                [],
                [],
            ],
            'not processed' => [
                'lang/en@wrong/incorrectLangDir.php',
                [],
                [],
            ],
        ];
    }
}
