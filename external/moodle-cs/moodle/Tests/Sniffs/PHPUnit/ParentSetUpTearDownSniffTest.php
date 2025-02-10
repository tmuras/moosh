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

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\PHPUnit;

use MoodleHQ\MoodleCS\moodle\Tests\MoodleCSBaseTestCase;

/**
 * Test the ParentSetUpTearDownSniff sniff.
 *
 * @copyright  2024 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\PHPUnit\ParentSetUpTearDownSniff
 */
class ParentSetUpTearDownSniffTest extends MoodleCSBaseTestCase
{
    /**
     * Data provider for self::testParentSetUpTearDown
     */
    public static function parentSetUpTearDownProvider(): array {
        return [
            'Correct' => [
                'fixture' => 'ParentSetUpTearDownCorrect',
                'errors' => [],
                'warnings' => [],
            ],
            'Problems' => [
                'fixture' => 'ParentSetUpTearDownProblems',
                'errors' => [
                    5 => 'The setUp() method in unit tests must not be empty',
                    8 => 'The tearDown() method in unit tests must not be empty',
                    11 => 'moodle.PHPUnit.ParentSetUpTearDown.EmptySetUpBeforeClass',
                    14 => 'moodle.PHPUnit.ParentSetUpTearDown.EmptyTearDownAfterClass',
                    21 => 'must call to parent::setUp() only once',
                    22 => 'moodle.PHPUnit.ParentSetUpTearDown.IncorrectSetUp',
                    27 => 'moodle.PHPUnit.ParentSetUpTearDown.MultipleTearDown',
                    29 => 'moodle.PHPUnit.ParentSetUpTearDown.IncorrectTearDown',
                    32 => 'moodle.PHPUnit.ParentSetUpTearDown.IncorrectSetUpBeforeClass',
                    35 => 'call to parent::setUpBeforeClass() only once',
                    40 => 'moodle.PHPUnit.ParentSetUpTearDown.MultipleTearDownAfterClass',
                    41 => 'cannot call to parent::setUpBeforeClass()',
                    46 => 'moodle.PHPUnit.ParentSetUpTearDown.MissingSetUp',
                    62 => 'moodle.PHPUnit.ParentSetUpTearDown.MissingTearDown',
                    69 => 'must always call to parent::setUpBeforeClass()',
                    83 => 'must always call to parent::tearDownAfterClass()',
                    92 => 'moodle.PHPUnit.ParentSetUpTearDown.EmptySetUp',
                    93 => 'moodle.PHPUnit.ParentSetUpTearDown.EmptyTearDown',
                    94 => 'moodle.PHPUnit.ParentSetUpTearDown.EmptySetUpBeforeClass',
                    95 => 'moodle.PHPUnit.ParentSetUpTearDown.EmptyTearDownAfterClass',
                ],
                'warnings' => [],
            ],
        ];
    }

    /**
     * @dataProvider parentSetUpTearDownProvider
     */
    public function testParentSetUpTearDown(
        string $fixture,
        array $errors,
        array $warnings
    ): void {
        $this->setStandard('moodle');
        $this->setSniff('moodle.PHPUnit.ParentSetUpTearDown');
        $this->setFixture(sprintf("%s/fixtures/%s.php", __DIR__, $fixture));
        $this->setWarnings($warnings);
        $this->setErrors($errors);

        $this->verifyCsResults();
    }
}
