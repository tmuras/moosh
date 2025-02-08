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
use MoodleHQ\MoodleCS\moodle\Util\TypeUtil;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Ruleset;

/**
 * Test the Tokens specific utilities class
 *
 *
 * @copyright Andrew Lyons <andrew@nicols.co.uk>
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \MoodleHQ\MoodleCS\moodle\Util\TypeUtil
 */
final class TypeUtilTest extends MoodleCSBaseTestCase
{
    /**
     * @dataProvider getValidTypesProvider
     */
    public function testGetValidTypes(string $type, string $expected): void {
        $config = new Config();
        $fileContent = <<<EOF
        <?php

        /** @var {$type} Type description */
        EOF;
        $file = new DummyFile($fileContent, new Ruleset($config), $config);
        $file->process();
        $ptr = $file->findNext(T_DOC_COMMENT_STRING, 0);

        $this->assertEquals(
            $expected,
            TypeUtil::getValidatedType($file, $ptr, $type),
        );
    }

    public static function getValidTypesProvider(): array {
        return [
            ['string', 'string'],
            ['int', 'int'],
            ['integer', 'int'],
            ['float', 'float'],
            ['real', 'float'],
            ['double', 'float'],
            ['array', 'array'],
            ['array()', 'array'],
            ['ARRAY()', 'array'],
            ['INT', 'int'],
            ['Boolean', 'bool'],
            ['NULL', 'null'],
            ['FALSE', 'false'],
            ['true', 'true'],

            // Various array syntaxes.
            ['string[]', 'string[]'],
            ['array(int => string)', 'string[]'],
            ['array(int)', 'int[]'],
            ['array(int > string)', 'array'],

            // Union types.
            ['string|int', 'string|int'],
            ['string|integer', 'string|int'],
            ['real|integer', 'float|int'],

            // Some example Moodle classes.
            [\core\formatting::class, \core\formatting::class],
            [\core\output\notification::class, \core\output\notification::class],
            [\core_renderer::class, \core_renderer::class],

            // Standard types.
            ['Traversable', 'Traversable'],
            [\ArrayAccess::class, \ArrayAccess::class],
            ['DateTimeImmutable', 'DateTimeImmutable'],
        ];
    }
}
