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

namespace MoodleHQ\MoodleCS\moodle\Tests\Util;

use MoodleHQ\MoodleCS\moodle\Tests\MoodleCSBaseTestCase;
use MoodleHQ\MoodleCS\moodle\Util\Attributes;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Ruleset;

/**
 * Test the Attributes specific moodle utilities class
 *
 * @copyright  2024 onwards Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Util\Attributes
 */
class AttributesTest extends MoodleCSBaseTestCase
{
    /**
     * @dataProvider validTagsProvider
     */
    public function testGetAttributePointers(
        string $content,
        $stackPtrSearch,
        ?array $expectations
    ): void {
        $config = new Config([]);
        $ruleset = new Ruleset($config);

        $phpcsFile = new DummyFile($content, $ruleset, $config);
        $phpcsFile->process();

        $searchPtr = $phpcsFile->findNext($stackPtrSearch, 0);

        $pointers = Attributes::getAttributePointers($phpcsFile, $searchPtr);
        if (count($expectations)) {
            foreach ($expectations as $expectation) {
                $this->assertCount(
                    $expectation['count'],
                    array_filter($pointers, function ($pointer) use ($expectation, $phpcsFile) {
                        $properties = Attributes::getAttributeProperties($phpcsFile, $pointer);

                        return $properties['attribute_name'] === $expectation['name'];
                    })
                );
            }
        } else {
            $this->assertEmpty($pointers);
        }
    }

    public static function validTagsProvider(): array {
        return [
            'No attributes' => [
                '<?php
                /**
                 * @param string $param
                 */
                function exampleFunction(string $param): void {}',
                T_FUNCTION,
                [],
            ],
            'Attribute matches' => [
                '<?php
                /**
                 * @param string $param
                 */
                #[\Override]
                function exampleFunction(string $param): void {}',
                T_FUNCTION,
                [
                    ['name' => '\\Override', 'count' => 1],
                ],
            ],
            'Multiple Attribute matches (same)' => [
                '<?php
                /**
                 * @param string $param
                 */
                #[\Override]
                #[\Override]
                function exampleFunction(string $param): void {}',
                T_FUNCTION,
                [
                    ['name' => '\\Override', 'count' => 2],
                ],
            ],
            'Multiple Attribute matches (different)' => [
                '<?php
                /**
                 * @param string $param
                 */
                #[\Override]
                #[\Other]
                #[\Override]
                function exampleFunction(string $param): void {}',
                T_FUNCTION,
                [
                    ['name' => '\\Override', 'count' => 2],
                    ['name' => '\\Other', 'count' => 1],
                ],
            ],
            'Attribute on other funciton' => [
                '<?php
                function otherFunction(): void {}
                #[\Override]
                #[\Override]
                function exampleFunction(string $param): void {}',
                T_FUNCTION,
                [],
            ],
            'Attributes with arguments' => [
                '<?php
                #[\Route("Example")]
                #[\Override]
                #[\Route]
                #[\core\attribute\deprecated("thing")]
                function exampleFunction(string $param): void {}',
                T_FUNCTION,
                [
                    ['name' => '\\Override', 'count' => 1],
                    ['name' => '\\Route', 'count' => 2],
                    ['name' => '\\core\\attribute\\deprecated', 'count' => 1],
                ],
            ],
            'Attribute start will only get that attribute' => [
                '<?php
                #[\Route("Example")]
                #[\Override]
                #[\Route]
                #[\core\attribute\deprecated("thing")]
                function exampleFunction(string $param): void {}',
                T_ATTRIBUTE,
                [
                    ['name' => '\\Route', 'count' => 1],
                ],
            ],
            'Attribute End will only get that attribute' => [
                '<?php
                #[\Route("Example")]
                #[\Override]
                #[\Route]
                #[\core\attribute\deprecated("thing")]
                function exampleFunction(string $param): void {}',
                T_ATTRIBUTE_END,
                [
                    ['name' => '\\Route', 'count' => 1],
                ],
            ],
        ];
    }

    public function testGetAttributePropertiesNotAnAttribute(): void {
        $config = new Config([]);
        $ruleset = new Ruleset($config);

        $content = <<<EOF
        <?php
        #[\Example()]
        function foo() {}
        EOF;

        $phpcsFile = new DummyFile($content, $ruleset, $config);
        $phpcsFile->process();

        $searchPtr = $phpcsFile->findNext(T_FUNCTION, 0);

        $this->assertNull(Attributes::getAttributeProperties($phpcsFile, $searchPtr));
    }

/**
     * @dataProvider hasOverrideAttributeProvider
     */
    public function testHasOverrideAttribute(
        string $content,
        $stackPtrSearch,
        bool $expected
    ): void {
        $config = new Config([]);
        $ruleset = new Ruleset($config);

        $phpcsFile = new DummyFile($content, $ruleset, $config);
        $phpcsFile->process();

        $searchPtr = $phpcsFile->findNext($stackPtrSearch, 0);

        $this->assertEquals($expected, Attributes::hasOverrideAttribute($phpcsFile, $searchPtr));
    }

    public static function hasOverrideAttributeProvider(): array {
        return [
            'Not in a method' => [
                '<?php
                protected $example;
                function exampleFunction(string $param): void {}',
                T_PROPERTY,
                false,
            ],
            'Not in a class' => [
                '<?php
                function exampleFunction(string $param): void {}',
                T_FUNCTION,
                false,
            ],
            'Not in a class, has Override' => [
                '<?php
                #[\Override]
                function exampleFunction(string $param): void {}',
                T_FUNCTION,
                false,
            ],
            'In a class, no Override' => [
                '<?php
                class Example {
                    function exampleFunction(string $param): void {}
                }',
                T_FUNCTION,
                false,
            ],
            'In a class, does not extend/implement, has Override' => [
                '<?php
                class Example {
                    #[\Override]
                    function exampleFunction(string $param): void {}
                }',
                T_FUNCTION,
                false,
            ],
            'In a class, extends, no Override' => [
                '<?php
                class Example extends OtherExample {
                    function exampleFunction(string $param): void {}
                }',
                T_FUNCTION,
                false,
            ],
            'In a class, implements, no Override' => [
                '<?php
                class Example implements OtherExample {
                    function exampleFunction(string $param): void {}
                }',
                T_FUNCTION,
                false,
            ],
            'In a class, extends, has Override' => [
                '<?php
                class Example extends OtherExample {
                    #[\Override]
                    function exampleFunction(string $param): void {}
                }',
                T_FUNCTION,
                true,
            ],
            'In a class, implements, has Override' => [
                '<?php
                class Example implements OtherExample {
                    #[\Override]
                    function exampleFunction(string $param): void {}
                }',
                T_FUNCTION,
                true,
            ],
        ];
    }
}
