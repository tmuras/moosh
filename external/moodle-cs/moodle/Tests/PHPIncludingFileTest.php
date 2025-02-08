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

namespace MoodleHQ\MoodleCS\moodle\Tests;

/**
 * Test the IncludingFile sniff.
 *
 * @copyright  2021 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\PHP\IncludingFileSniff
 */
class PHPIncludingFileTest extends MoodleCSBaseTestCase
{
    public function testPHPIncludingFile() {
        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('moodle.PHP.IncludingFile');
        $this->setFixture(__DIR__ . '/fixtures/php/includingfile.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            9 => '@Message: "require" must be immediately followed by an open parenthesis',
           10 => '@Source: moodle.PHP.IncludingFile.BracketsRequired',
           13 => 1,
           14 => 1,
           17 =>  '@Source: moodle.PHP.IncludingFile.UseRequire',
           18 => '@Source: moodle.PHP.IncludingFile.UseRequireOnce',
        ]);
        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }
}
