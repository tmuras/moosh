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
 * @copyright  2024 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Commenting\TodoCommentSniff
 */
class TodoCommentSniffTest extends MoodleCSBaseTestCase
{
    public function testComentingTodoComment(): void {
        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('moodle.Commenting.TodoComment');
        $this->setFixture(__DIR__ . '/../../fixtures/Commenting/TodoComment.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $errors = [];
        $warnings = [
            8  => 'Missing required "MDL-[0-9]+"',
            10 => 'Missing required "MDL-[0-9]+"',
            12 => 'TodoComment.MissingInfoInline',
            16 => 'TodoComment.MissingInfoPhpdoc',
            23 => 'comment: In the middle',
            25 => 'take 2',
            27 => 'take 3',
            33 => 'information in inline comment',
            34 => 'information in phpdoc comment',
        ];
        $this->setErrors($errors);
        $this->setWarnings($warnings);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    public function testEmptyConfigValue(): void {
        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('moodle.Commenting.TodoComment');
        $this->setFixture(__DIR__ . '/../../fixtures/Commenting/TodoCommentEmptyConfig.php');

        // Try with an empty config value.
        $this->addCustomConfig('moodleTodoCommentRegex', '');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $errors = [];
        $warnings = [];
        $this->setErrors($errors);
        $this->setWarnings($warnings);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    public function testCustomConfigValue(): void {
            // Define the standard, sniff and fixture to use.
            $this->setStandard('moodle');
            $this->setSniff('moodle.Commenting.TodoComment');
            $this->setFixture(__DIR__ . '/../../fixtures/Commenting/TodoCommentCustomConfig.php');

            // Try with an empty config value.
            $this->addCustomConfig('moodleTodoCommentRegex', 'CUSTOM-[0-9]+');

            // Define expected results (errors and warnings). Format, array of:
            // - line => number of problems,  or
            // - line => array of contents for message / source problem matching.
            // - line => string of contents for message / source problem matching (only 1).
            $errors = [];
            $warnings = [
                8 => 'Missing required "CUSTOM-[0-9]+"',
                9 => 'Missing required "CUSTOM-[0-9]+"',
            ];
            $this->setErrors($errors);
            $this->setWarnings($warnings);

            // Let's do all the hard work!
            $this->verifyCsResults();
    }
}
