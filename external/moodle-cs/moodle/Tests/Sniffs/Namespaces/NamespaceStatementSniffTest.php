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

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\Namespaces;

use MoodleHQ\MoodleCS\moodle\Tests\MoodleCSBaseTestCase;

/**
 * Test the NoLeadingSlash sniff.
 *
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Namespaces\NamespaceStatementSniff
 */
class NamespaceStatementSniffTest extends MoodleCSBaseTestCase
{
    public static function leadingSlashProvider(): array
    {
        return [
            [
                'fixture' => 'correct_namespace',
                'warnings' => [],
                'errors' => [],
            ],
            [
                'fixture' => 'leading_backslash',
                'warnings' => [],
                'errors' => [
                    3 => 'Namespace should not start with a slash: \MoodleHQ\MoodleCS\moodle\Tests\Sniffs\Namespaces',
                ],
            ],
            [
                'fixture' => 'curly_namespace',
                'warnings' => [],
                'errors' => [
                    3 => 'Namespace should not start with a slash: \MoodleHQ\MoodleCS\moodle\Tests\Sniffs\Namespaces',
                ],
            ],
        ];
    }
    /**
     * @dataProvider leadingSlashProvider
     */
    public function testLeadingSlash(
        string $fixture,
        array $warnings,
        array $errors
    ): void {
        $this->setStandard('moodle');
        $this->setSniff('moodle.Namespaces.NamespaceStatement');
        $this->setFixture(sprintf("%s/fixtures/%s.php", __DIR__, $fixture));
        $this->setWarnings($warnings);
        $this->setErrors($errors);

        $this->verifyCsResults();
    }
}
