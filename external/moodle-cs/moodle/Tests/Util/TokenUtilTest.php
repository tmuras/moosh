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
use MoodleHQ\MoodleCS\moodle\Util\TokenUtil;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Files\DummyFile;

/**
 * Test the Tokens specific utilities class
 *
 * @copyright  2021 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Util\TokenUtil
 */
class TokenUtilTest extends MoodleCSBaseTestCase
{
    /**
     * @dataProvider objectPropertiesProvider
     */
    public function testGetObjectProperties(
        string $content,
        $type,
        ?string $expectedType,
        ?string $expectedName
    ): void {
        $config = new Config([]);
        $ruleset = new Ruleset($config);

        $phpcsFile = new DummyFile($content, $ruleset, $config);
        $phpcsFile->process();

        $stackPtr = $phpcsFile->findNext($type, 0);

        $this->assertEquals($expectedType, TokenUtil::getObjectType($phpcsFile, $stackPtr));
        $this->assertEquals($expectedName, TokenUtil::getObjectName($phpcsFile, $stackPtr));
    }

    public static function objectPropertiesProvider(): array {
        $cases = [
            'Class name' => [
                '<?php class Example {}',
                T_CLASS,
                'class',
                'Example',
            ],
            'File name' => [
                // Setting the first line of the file to phpcs_input_file: pathname will set the file name of the dummy file.
                <<<EOF
                phpcs_input_file: /path/to/file/example.php
                <?php class Example {}
                EOF,
                T_OPEN_TAG,
                'file',
                'example.php',
            ],
            'Trait name' => [
                '<?php trait ExampleTrait {}',
                T_TRAIT,
                'trait',
                'ExampleTrait',
            ],
            'Interface name' => [
                '<?php interface ExampleInterface {}',
                T_INTERFACE,
                'interface',
                'ExampleInterface',
            ],
            'Function name' => [
                '<?php function exampleFunction(): void {}',
                T_FUNCTION,
                'function',
                'exampleFunction',
            ],
            'Unnamed anonymous class' => [
                <<<EOF
                <?php

                return new class extends phpunit_coverage_info {
                    /** @var array The list of folders relative to the plugin root to include in coverage generation. */
                    protected \$includelistfolders = ['classes'];
                };
                EOF,
                T_ANON_CLASS,
                'class',
                'anonymous class',
            ],
            'Unnamed closure' => [
                '<?php $fn = function() {};',
                T_CLOSURE,
                'function',
                'closure',
            ],
        ];

        if (version_compare(PHP_VERSION, '8.1.0') >= 0) {
            $cases['Enum name'] = [
                '<?php enum ExampleEnum {}',
                T_ENUM,
                'enum',
                'ExampleEnum',
            ];
        }

        return $cases;
    }

    /**
     * @dataProvider countGlobalScopesInFileProvider
     */
    public function testCountGlobalScopesInFile(
        string $content,
        int $expectedCount
    ): void {
        $config = new Config([]);
        $ruleset = new Ruleset($config);

        $phpcsFile = new DummyFile($content, $ruleset, $config);
        $phpcsFile->process();

        $this->assertEquals($expectedCount, TokenUtil::countGlobalScopesInFile($phpcsFile));
    }

    public static function countGlobalScopesInFileProvider(): array {
        $cases = [
            'No global scopes' => [
                '<?php $a = 1;',
                0,
            ],
            'One global scope' => [
                '<?php class Example {}',
                1,
            ],
            'Two global scopes' => [
                '<?php class Example {} class AnotherExample {}',
                2,
            ],
            'Class method is not global' => [
                '<?php class Example { public function exampleMethod() {} }',
                1,
            ],
            'Global method counts' => [
                '<?php function exampleFunction() {}',
                1,
            ],
            'Interfaces count' => [
                '<?php interface ExampleInterface {}',
                1,
            ],
            'Traits count' => [
                '<?php trait ExampleTrait {}',
                1,
            ],
        ];

        if (version_compare(PHP_VERSION, '8.1.0') >= 0) {
            $cases['Enums count'] = [
                '<?php enum ExampleEnum {}',
                1,
            ];
        }

        return $cases;
    }

    public function testObjectPropertiesInvalidPointer(): void {
        $config = new Config([]);
        $ruleset = new Ruleset($config);

        $phpcsFile = new DummyFile('<?php', $ruleset, $config);
        $phpcsFile->process();

        $stackPtr = 1000;

        $this->assertEquals('', TokenUtil::getObjectType($phpcsFile, $stackPtr));
        $this->assertEquals('', TokenUtil::getObjectName($phpcsFile, $stackPtr));
    }
}
