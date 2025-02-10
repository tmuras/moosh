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
 * Test the ConstructorReturnSniff sniff.
 *
 * @copyright  2024 onwards Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Commenting\ConstructorReturnSniff
 */
class ConstructorReturnSniffTest extends MoodleCSBaseTestCase
{
    /**
     * @dataProvider docblockCorrectnessProvider
     */
    public function testMissingDocblockSniff(
        string $fixture,
        array $errors,
        array $warnings
    ): void {
        $this->setStandard('moodle');
        $this->setSniff('moodle.Commenting.ConstructorReturn');
        $this->setFixture(sprintf("%s/fixtures/ConstructorReturn/%s.php", __DIR__, $fixture));
        $this->setWarnings($warnings);
        $this->setErrors($errors);

        $this->verifyCsResults();
    }

    public static function docblockCorrectnessProvider(): array {
        $cases = [
            [
                'fixture' => 'general',
                'errors' => [
                    43 => 'Constructor should not have a return tag in the docblock',
                    52 => 'Constructor should not have a return tag in the docblock',
                    60 => 'Constructor should not have a return tag in the docblock',
                    71 => 'Constructor should not have a return tag in the docblock',
                ],
                'warnings' => [],
            ],
        ];
        return $cases;
    }
}
