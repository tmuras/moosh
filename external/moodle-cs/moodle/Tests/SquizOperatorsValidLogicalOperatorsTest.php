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
 * Test the PHP_CodeSniffer\Standards\Squiz\Sniffs\Operators\ValidLogicalOperatorsSniff sniff.
 *
 * @copyright  2022 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \PHP_CodeSniffer\Standards\Squiz\Sniffs\Operators\ValidLogicalOperatorsSniff
 */
class SquizOperatorsValidLogicalOperatorsTest extends MoodleCSBaseTestCase
{
    /**
     * Test the Squid.Operators.ValidLogicalOperators sniff
     */
    public function testSquizOperatorsValidLogicalOperators() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('Squiz.Operators.ValidLogicalOperators');
        $this->setFixture(__DIR__ . '/fixtures/squiz_operators_validlogicaloperators.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            21 => 'Logical operator "or" is prohibited; use "||" instead',
            25 => 'Squiz.Operators.ValidLogicalOperators.NotAllowed',
            29 => 2,
            33 => 4,
        ]);
        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }
}
