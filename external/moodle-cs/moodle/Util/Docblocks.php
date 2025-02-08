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

namespace MoodleHQ\MoodleCS\moodle\Util;

use PHP_CodeSniffer\Files\File;

/**
 * Utilities related to PHP DocBlocks.
 *
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class Docblocks
{
    /**
     * List of valid, well known, phpdoc tags, always accepted.
     *
     * @var array<string, bool>
     * @link http://manual.phpdoc.org/HTMLSmartyConverter/HandS/ */
    private static array $validTags = [
        // Behat tags.
        'Given' => true,
        'Then' => true,
        'When' => true,
        'BeforeFeature' => true,
        'BeforeScenario' => true,
        'BeforeStep' => true,
        'AfterFeature' => true,
        'AfterScenario' => true,
        'AfterStep' => true,
        'Transform' => true,

        // PHPUnit tags.
        'codeCoverageIgnore' => true,
        'codeCoverageIgnoreStart' => true,
        'codeCoverageIgnoreEnd' => true,
        'covers' => true,
        'coversDefaultClass' => true,
        'coversNothing' => true,
        'dataProvider' => true,
        'depends' => true,
        'group' => true,
        'requires' => true,
        'runTestsInSeparateProcesses' => true,
        'runInSeparateProcess' => true,
        'testWith' => true,

        // PHPDoc tags.
        'abstract' => false,
        'access' => false,
        'author' => true,
        'category' => true,
        'copyright' => true,
        'deprecated' => true,
        'example' => false,
        'final' => false,
        'filesource' => false,
        'global' => false,
        'ignore' => false,
        'internal' => false,
        'license' => true,
        'link' => true,
        'method' => false,
        'name' => false,
        'package' => true,
        'param' => true,
        'property' => true,
        'property-read' => true,
        'property-write' => true,
        'return' => true,
        'see' => true,
        'since' => true,
        'static' => false,
        'staticvar' => false,
        'subpackage' => true,
        'throws' => true,
        'todo' => true,
        'tutorial' => false,
        'uses' => true, // Also used by PHPUnit.
        'var' => true,
        'version' => false,
    ];

    /**
     * List of invalid tags that should be removed.
     *
     * @var string[]
     */
    private static array $invalidTagsToRemove = [
        'void',
    ];

    /**
     * List of tags that should be renamed.
     *
     * @var string[string]
     */
    private static array $renameTags = [
        // Rename returns to return.
        'returns' => 'return',
    ];

    /**
     * A list of phpdoc tags allowed to be used under certain directories.
     * keys are tags, values are arrays of allowed paths (regexp patterns).
     *
     * @var array(string => array(string))
     */
    private static array $pathRestrictedTags = [
        'Given' => ['#.*/tests/behat/.*#'],
        'Then' => ['#.*/tests/behat/.*#'],
        'When' => ['#.*/tests/behat/.*#'],
        'BeforeFeature' => ['#.*/tests/behat/.*#'],
        'BeforeScenario' => ['#.*/tests/behat/.*#'],
        'BeforeStep' => ['#.*/tests/behat/.*#'],
        'AfterFeature' => ['#.*/tests/behat/.*#'],
        'AfterScenario' => ['#.*/tests/behat/.*#'],
        'AfterStep' => ['#.*/tests/behat/.*#'],
        'Transform' => ['#.*/tests/behat/.*#'],

        'covers' => ['#.*/tests/.*_test.php#'],
        'coversDefaultClass' => ['#.*/tests/.*_test.php#'],
        'coversNothing' => ['#.*/tests/.*_test.php#'],
        'dataProvider' => ['#.*/tests/.*_test.php#'],
        'depends' => ['#.*/tests/.*_test.php#'],
        'group' => ['#.*/tests/.*_test.php#'],
        'requires' => ['#.*/tests/.*_test.php#'],
        'runTestsInSeparateProcesses' => ['#.*/tests/.*_test.php#'],
        'runInSeparateProcess' => ['#.*/tests/.*_test.php#'],
        'testWith' => ['#.*/tests/.*_test.php#'],
        // Commented out: 'uses' => ['#.*/tests/.*_test.php#'], can also be out from tests (Coding style dixit).
    ];

    /**
     * Get the docblock pointer for a file, class, interface, trait, or method.
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return null|int
     */
    public static function getDocBlockPointer(
        File $phpcsFile,
        int $stackPtr
    ): ?int {
        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$stackPtr];

        // Check if the passed pointer was for a doc.
        $midDocBlockTokens = [
            T_DOC_COMMENT,
            T_DOC_COMMENT_STAR,
            T_DOC_COMMENT_WHITESPACE,
            T_DOC_COMMENT_TAG,
            T_DOC_COMMENT_STRING,
        ];
        if ($token['code'] === T_DOC_COMMENT_OPEN_TAG) {
            return $stackPtr;
        } elseif ($token['code'] === T_DOC_COMMENT_CLOSE_TAG) {
            // The pointer was for a close tag. Fetch the corresponding open tag.
            return $token['comment_opener'];
        } elseif (in_array($token['code'], $midDocBlockTokens)) {
            // The pointer was for a token inside the docblock. Fetch the corresponding open tag.
            $commentStart = $phpcsFile->findPrevious(T_DOC_COMMENT_OPEN_TAG, $stackPtr);
            return $commentStart ?: null;
        }

        // If the pointer was for a file, fetch the doc tag from the open tag.
        if ($tokens[$stackPtr]['code'] === T_OPEN_TAG) {
            return self::getDocTagFromOpenTag($phpcsFile, $stackPtr);
        }

        // Assume that the stackPtr is for a class, interface, trait, or method, or some part of them.
        // Back track over each previous pointer until we find the docblock.
        // It should be on the line immediately before the pointer.
        $pointerLine = $tokens[$stackPtr]['line'];

        $previousContent = null;
        for ($commentEnd = ($stackPtr - 1); $commentEnd >= 0; $commentEnd--) {
            $token = $tokens[$commentEnd];
            if ($previousContent === null) {
                $previousContent = $commentEnd;
            }

            if ($token['code'] === T_ATTRIBUTE_END && isset($token['attribute_opener'])) {
                $commentEnd = $token['attribute_opener'];
                $pointerLine = $tokens[$commentEnd]['line'];
                continue;
            }

            if ($token['line'] < ($pointerLine - 1)) {
                // The comment must be on the line immediately before the pointer, or immediately before the attribute.       z
                return null;
            }

            if ($token['code'] === T_DOC_COMMENT_CLOSE_TAG) {
                // The pointer was for a close tag. Fetch the corresponding open tag.
                return $token['comment_opener'];
            }
        }

        return null; // @codeCoverageIgnore
    }

    /**
     * Get the doc tag from the file open tag.
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return null|int
     */
    protected static function getDocTagFromOpenTag(
        File $phpcsFile,
        int $stackPtr
    ): ?int {
        $tokens = $phpcsFile->getTokens();

        $ignore = [
            T_WHITESPACE,
            T_COMMENT,
        ];

        $stopAtTypes = [
            T_CLASS,
            T_INTERFACE,
            T_TRAIT,
            T_ENUM,
            T_FUNCTION,
            T_CLOSURE,
            T_PUBLIC,
            T_PRIVATE,
            T_PROTECTED,
            T_FINAL,
            T_STATIC,
            T_ABSTRACT,
            T_READONLY,
            T_CONST,
            T_PROPERTY,
            T_INCLUDE,
            T_INCLUDE_ONCE,
            T_REQUIRE,
            T_REQUIRE_ONCE,
            T_ATTRIBUTE,
        ];

        while ($stackPtr = $phpcsFile->findNext($ignore, ($stackPtr + 1), null, true)) {
            // If we have arrived to a stop token, and haven't found the file docblock yet, then there isn't one.
            if (in_array($tokens[$stackPtr]['code'], $stopAtTypes)) {
                return null;
            }

            if ($tokens[$stackPtr]['code'] === T_NAMESPACE || $tokens[$stackPtr]['code'] === T_USE) {
                $stackPtr = $phpcsFile->findNext(T_SEMICOLON, $stackPtr + 1);
                continue;
            }

            if ($tokens[$stackPtr]['code'] === T_DOC_COMMENT_OPEN_TAG) {
                $nextToken = $tokens[$stackPtr]['comment_closer'];
                $closeLine = $tokens[$nextToken]['line'];

                while ($nextToken = $phpcsFile->findNext(T_WHITESPACE, $nextToken + 1, null, true)) {
                    if (in_array($tokens[$nextToken]['code'], $stopAtTypes)) {
                        // If the stop token is on the line immediately following the attribute or close comment
                        // then it belongs to that stop token, and not the file.
                        if ($tokens[$nextToken]['line'] === ($closeLine + 1)) {
                            return null;
                        }
                    }
                    break;
                }
                return $stackPtr;
            }
        }

        return null;
    }

    /**
     * Get the tags that match the given tag name.
     *
     * @param File $phpcsFile
     * @param int|null $stackPtr The pointer of the docblock
     * @param string $tagName
     */
    public static function getMatchingDocTags(
        File $phpcsFile,
        ?int $stackPtr,
        string $tagName
    ): array {
        if ($stackPtr === null) {
            return [];
        }
        $tokens = $phpcsFile->getTokens();
        $docblock = $tokens[$stackPtr];
        $matchingTags = [];
        foreach ($docblock['comment_tags'] as $tag) {
            if ($tokens[$tag]['content'] === $tagName) {
                $matchingTags[] = $tag;
            }
        }

        return $matchingTags;
    }

    /**
     * Whether this a valid tag.
     *
     * @param File $phpcsFile
     * @param int $tagPtr
     * @return bool
     */
    public static function isValidTag(
        File $phpcsFile,
        int $tagPtr
    ): bool {
        $tokens = $phpcsFile->getTokens();
        $tag = ltrim($tokens[$tagPtr]['content'], '@');
        if (array_key_exists($tag, self::$validTags)) {
            if (array_key_exists($tag, self::$pathRestrictedTags)) {
                $file = MoodleUtil::getStandardisedFilename($phpcsFile);
                foreach (self::$pathRestrictedTags[$tag] as $path) {
                    if (preg_match($path, $file)) {
                        return true;
                    }
                }
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Check if a tag is recommended.
     *
     * @param string $tagname
     * @return bool
     */
    public static function isRecommendedTag(
        string $tagname
    ): bool {
        return array_key_exists($tagname, self::$validTags) && self::$validTags[$tagname];
    }

    /**
     * Check if a tag should be removed.
     *
     * @param string $tagname
     * @return bool
     */
    public static function shouldRemoveTag(
        string $tagname
    ): bool {
        return in_array($tagname, self::$invalidTagsToRemove);
    }

    /**
     * Get the tag name to rename to.
     *
     * @param string $tagname
     * @return string|null
     */
    public static function getRenameTag(
        string $tagname
    ): ?string {
        if (array_key_exists($tagname, self::$renameTags)) {
            return self::$renameTags[$tagname];
        }
        return null;
    }
}
