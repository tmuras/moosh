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
use MoodleHQ\MoodleCS\moodle\Util\Docblocks;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Ruleset;

/**
 * Test the Docblocks specific moodle utilities class
 *
 * @copyright  2021 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Util\Docblocks
 */
class DocblocksTest extends MoodleCSBaseTestCase
{
    public static function getNullDocBlockPointerProvider(): array {
        return [
            'global_scope_code' => ['none_global_scope.php'],
            'oop_scope_code' => ['none.php'],
        ];
    }

    /**
     * @dataProvider getNullDocBlockPointerProvider
     */
    public function testGetNullDocBlockPointer(string $fixture): void {
        $phpcsConfig = new Config();
        $phpcsRuleset = new Ruleset($phpcsConfig);
        $phpcsFile = new \PHP_CodeSniffer\Files\LocalFile(
            __DIR__ . '/fixtures/docblocks/' . $fixture,
            $phpcsRuleset,
            $phpcsConfig
        );

        $phpcsFile->process();
        $filePointer = $phpcsFile->findNext(T_OPEN_TAG, 0);

        $docBlock = Docblocks::getDocBlockPointer($phpcsFile, $filePointer);
        $this->assertNull($docBlock);
    }

    public function testGetDocBlockTags(): void {
        $phpcsConfig = new Config();
        $phpcsRuleset = new Ruleset($phpcsConfig);
        $phpcsFile = new \PHP_CodeSniffer\Files\LocalFile(
            __DIR__ . '/fixtures/docblocks/class_docblock.php',
            $phpcsRuleset,
            $phpcsConfig
        );

        $phpcsFile->process();
        $filePointer = $phpcsFile->findNext(T_OPEN_TAG, 0);
        $classPointer = $phpcsFile->findNext(T_CLASS, 0);

        $this->assertCount(0, Docblocks::getMatchingDocTags($phpcsFile, null, '@copyright'));

        $fileDocBlockPtr = Docblocks::getDocBlockPointer($phpcsFile, $filePointer);
        $this->assertNotNull($fileDocBlockPtr);
        $this->assertCount(1, Docblocks::getMatchingDocTags($phpcsFile, $fileDocBlockPtr, '@copyright'));
        $this->assertCount(0, Docblocks::getMatchingDocTags($phpcsFile, $fileDocBlockPtr, '@property'));

        $classDocBlockPtr = Docblocks::getDocBlockPointer($phpcsFile, $classPointer);
        $this->assertNotNull($classDocBlockPtr);
        $this->assertNotEquals($fileDocBlockPtr, $classDocBlockPtr);
        $this->assertCount(1, Docblocks::getMatchingDocTags($phpcsFile, $classDocBlockPtr, '@copyright'));
        $this->assertCount(2, Docblocks::getMatchingDocTags($phpcsFile, $classDocBlockPtr, '@property'));

        $methodPointer = $phpcsFile->findNext(T_FUNCTION, $classPointer);
        $this->assertNull(Docblocks::getDocBlockPointer($phpcsFile, $methodPointer));

        // Get the docblock from pointers at the start, middle, and end, of a docblock.
        $tokens = $phpcsFile->getTokens();
        $startDocPointer = $phpcsFile->findNext(T_DOC_COMMENT_OPEN_TAG, 0);
        $endDocPointer = $phpcsFile->findNext(T_DOC_COMMENT_CLOSE_TAG, $startDocPointer);
        $middleDocPointer = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $startDocPointer, $endDocPointer);

        $docblock = Docblocks::getDocBlockPointer($phpcsFile, $startDocPointer);
        $this->assertIsInt($docblock);
        $this->assertEquals($startDocPointer, $docblock);

        $docblock = Docblocks::getDocBlockPointer($phpcsFile, $middleDocPointer);
        $this->assertIsInt($docblock);
        $this->assertEquals($startDocPointer, $docblock);

        $docblock = Docblocks::getDocBlockPointer($phpcsFile, $endDocPointer);
        $this->assertIsInt($docblock);
        $this->assertEquals($startDocPointer, $docblock);
    }

    public function testGetDocBlockClassOnly(): void {
        $phpcsConfig = new Config();
        $phpcsRuleset = new Ruleset($phpcsConfig);
        $phpcsFile = new \PHP_CodeSniffer\Files\LocalFile(
            __DIR__ . '/fixtures/docblocks/class_docblock_only.php',
            $phpcsRuleset,
            $phpcsConfig
        );

        $phpcsFile->process();
        $filePointer = $phpcsFile->findNext(T_OPEN_TAG, 0);
        $classPointer = $phpcsFile->findNext(T_CLASS, 0);

        $fileDocBlock = Docblocks::getDocBlockPointer($phpcsFile, $filePointer);
        $this->assertNull($fileDocBlock);

        $classDocBlockPtr = Docblocks::getDocBlockPointer($phpcsFile, $classPointer);
        $this->assertNotNull($classDocBlockPtr);
        $this->assertNotEquals($fileDocBlock, $classDocBlockPtr);
        $this->assertCount(1, Docblocks::getMatchingDocTags($phpcsFile, $classDocBlockPtr, '@copyright'));
        $this->assertCount(2, Docblocks::getMatchingDocTags($phpcsFile, $classDocBlockPtr, '@property'));

        $methodPointer = $phpcsFile->findNext(T_FUNCTION, $classPointer);
        $this->assertNull(Docblocks::getDocBlockPointer($phpcsFile, $methodPointer));
    }

    /**
     * Test that a file docblock and a class with no docblock correctly associated the docblock with the file
     * and not the class.
     */
    public function testGetDocBlockClassWithoutDocblock(): void {
        $phpcsConfig = new Config();
        $phpcsRuleset = new Ruleset($phpcsConfig);
        $phpcsFile = new \PHP_CodeSniffer\Files\LocalFile(
            __DIR__ . '/fixtures/docblocks/file_followed_by_class_without_docblock.php',
            $phpcsRuleset,
            $phpcsConfig
        );

        $phpcsFile->process();
        $filePointer = $phpcsFile->findNext(T_OPEN_TAG, 0);
        $classPointer = $phpcsFile->findNext(T_CLASS, 0);

        $fileDocBlock = Docblocks::getDocBlockPointer($phpcsFile, $filePointer);
        $this->assertNotNull($fileDocBlock);

        $classDocBlock = Docblocks::getDocBlockPointer($phpcsFile, $classPointer);
        $this->assertNull($classDocBlock);
    }

    /**
     * Test that a file docblock and a class with no docblock correctly associated the docblock with the file
     * and not the class when the class has an Attribute.
     */
    public function testGetDocBlockClassWithAttribute(): void {
        $phpcsConfig = new Config();
        $phpcsRuleset = new Ruleset($phpcsConfig);
        $phpcsFile = new \PHP_CodeSniffer\Files\LocalFile(
            __DIR__ . '/fixtures/docblocks/file_followed_by_class_with_attribute.php',
            $phpcsRuleset,
            $phpcsConfig
        );

        $phpcsFile->process();
        $filePointer = $phpcsFile->findNext(T_OPEN_TAG, 0);
        $classPointer = $phpcsFile->findNext(T_CLASS, 0);

        $fileDocBlock = Docblocks::getDocBlockPointer($phpcsFile, $filePointer);
        $this->assertNotNull($fileDocBlock);

        $classDocBlock = Docblocks::getDocBlockPointer($phpcsFile, $classPointer);
        $this->assertNull($classDocBlock);
    }

    /**
     * @dataProvider validTagsProvider
     */
    public function testIsValidTag(
        string $filename,
        string $content,
        bool $expected
    ): void {
        $config = new Config([]);
        $ruleset = new Ruleset($config);
        $content = <<<EOF
        phpcs_input_file: {$filename}
        {$content}
        EOF;

        $phpcsFile = new DummyFile($content, $ruleset, $config);
        $phpcsFile->process();

        $tokens = $phpcsFile->getTokens();
        $docPtr = $phpcsFile->findNext(T_DOC_COMMENT_OPEN_TAG, 0);
        $docblock = $tokens[$docPtr];
        $testPtr = reset($docblock['comment_tags']);

        $this->assertEquals($expected, Docblocks::isValidTag($phpcsFile, $testPtr));
    }

    public static function validTagsProvider(): array {
        return [
            'Regular file: Valid' => [
                'lib/classes/example.php',
                '<?php
                /**
                 * @param string $param
                 */
                function exampleFunction(string $param): void {}',
                true,
            ],
            'Regular file: Not valid' => [
                'lib/classes/example.php',
                '<?php
                /**
                 * @invalid
                 */
                function exampleFunction(string $param): void {}',
                false,
            ],
            'Regular file: not recommended' => [
                'lib/classes/example.php',
                '<?php
                /**
                 * @abstract
                 */
                function exampleFunction(string $param): void {}',
                true,
            ],
            'Regular file: Only valid for unit test' => [
                'lib/classes/example.php',
                '<?php
                /**
                 * @dataProvider example
                 */
                function exampleFunction(string $param): void {}',
                false,
            ],
            'Unit test file: Valid' => [
                'lib/tests/example_test.php',
                '<?php
                /**
                 * @dataProvider example
                 */
                function exampleFunction(string $param): void {}',
                true,
            ],
            'Behat test file: Valid' => [
                'lib/tests/behat/behat_example.php',
                '<?php
                class behat_example {
                    /**
                     * @BeforeScenario
                     * @AfterScenario
                     * @BeforeFeature
                     * @AfterFeature
                     * @BeforeStep
                     * @AfterStep
                     * @Given
                     * @When
                     * @Then
                     * @Transform
                     */
                    function exampleFunction(string $param): void {}
                }',
                true,
            ],
            'Unit test file: Contains Behat' => [
                'lib/tests/test_behat.php',
                '<?php
                class test_behat {
                    /**
                     * @BeforeScenario
                     * @AfterScenario
                     * @BeforeFeature
                     * @AfterFeature
                     * @BeforeStep
                     * @AfterStep
                     * @Given
                     * @When
                     * @Then
                     * @Transform
                     */
                    function exampleFunction(string $param): void {}
                }',
                false,
            ],
        ];
    }

    /**
     * @dataProvider isRecommendedTagProvider
     */
    public function testIsRecommendedTag(
        string $tagName,
        bool $expected
    ): void {
        $this->assertEquals($expected, Docblocks::isRecommendedTag($tagName));
    }

    public static function isRecommendedTagProvider(): array {
        return [
            ['uses', true],
            ['abstract', false],
            ['package', true],
            ['category', true],
            ['version', false],
            ['global', false],
        ];
    }

    /**
     * @dataProvider shouldRemoveTagProvider
     */
    public function testShouldRemoveTag(
        string $tagName,
        bool $expected
    ): void {
        $this->assertEquals($expected, Docblocks::shouldRemoveTag($tagName));
    }

    public static function shouldRemoveTagProvider(): array {
        return [
            ['uses', false],
            ['abstract', false],
            ['package', false],
            ['category', false],
            ['version', false],
            ['global', false],
            ['void', true],
        ];
    }

    /**
     * @dataProvider getRenameTagProvider
     */
    public function testGetRenameTag(
        string $tagName,
        ?string $renameTo
    ): void {
        $this->assertEquals($renameTo, Docblocks::getRenameTag($tagName));
    }

    public static function getRenameTagProvider(): array {
        return [
            ['returns', 'return'],
            ['inheritdoc', null],
            ['void', null],
            ['small', null],
            ['zzzing', null],
        ];
    }
}
