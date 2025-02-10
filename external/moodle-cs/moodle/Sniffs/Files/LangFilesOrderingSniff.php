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

/**
 * This sniff verifies that lang files are sorted alphabetically by string key.
 *
 * @copyright 2024 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleHQ\MoodleCS\moodle\Sniffs\Files;

use MoodleHQ\MoodleCS\moodle\Util\MoodleUtil;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class LangFilesOrderingSniff implements Sniff
{
    /**
     * @var string|null The previous string that has been processed.
     *
     * We use this variable to compare the current string with the previous one. And decide
     * if the current string is a duplicate or if it's out of order.
     */
    protected ?string $previousString = null;

    /**
     * @var int pointer to the token where we should stop fixing the file (defaults to last token).
     *
     * When we find a comment that is not a "Deprecated since Moodle" one, we will stop fixing the file.
     */
    protected int $stopFixingPtr = 999999999;

    /**
     * @var array An array which keys are all the known strings, grouped, and the values are the start and end pointers to them.
     *
     * We use this array to, accurately, know where to move every string on each fixing iteration.
     */
    protected array $strings = [];

    public function register(): array {
        return [T_OPEN_TAG]; // We are going to process the whole file, finding all the strings and comments within it.
    }

    public function process(File $phpcsFile, $stackPtr): void {
        // If the file is not a lang file, return.
        if (!MoodleUtil::isLangFile($phpcsFile)) {
            return;
        }

        // Only for Moodle 4.4dev (404) and up.
        // Make and exception for unit tests, so they are run always.
        if (!MoodleUtil::meetsMinimumMoodleVersion($phpcsFile, 404) && !MoodleUtil::isUnitTestRunning()) {
            return; // @codeCoverageIgnore
        }

        // Get the file tokens, for ease of use.
        $tokens = $phpcsFile->getTokens();

        // Ensure that we start from scratch on each file and pass.
        $this->previousString = null;
        $this->stopFixingPtr = 999999999;
        $this->strings = [];

        // Let's find the first variable and start the process.
        $currentPtr = $phpcsFile->findNext(T_VARIABLE, $stackPtr + 1);
        if ($currentPtr === false) {
            return; // No strings found, nothing to do.
        }

        // It's time to iterate over all the strings and comments till the end of the file.
        // We'll go accumulating all the strings by group, with their start and end pointers as values.
        $currentGroup = 'main'; // The default group to start with, we'll change it each time we find a new section.
        do {
            // Let's manage comments first (so we know if we are changing the current group).

            // Correct comments are in new line and begin with "// Deprecated since ".
            if (
                $tokens[$currentPtr]['code'] === T_COMMENT &&
                strpos($tokens[$currentPtr]['content'], '// Deprecated since ') === 0 &&
                $tokens[$currentPtr - 1]['content'] === "\n"
            ) {
                $currentGroup = trim($tokens[$currentPtr]['content']);
            }

            // If we find a comment that is not the standard one, we will stop fixing the file here. And error.
            if (
                $tokens[$currentPtr]['code'] === T_COMMENT &&
                strpos($tokens[$currentPtr]['content'], '// Deprecated since ') === false
            ) {
                $phpcsFile->addWarning(
                    'Unexpected comment found. Auto-fixing will not work after this comment',
                    $currentPtr,
                    'UnexpectedComment'
                );
                if ($this->stopFixingPtr > $currentPtr) {
                    $this->stopFixingPtr = $currentPtr; // Update the stop fixing pointer.
                }
            }

            if ($tokens[$currentPtr]['code'] === T_COMMENT) {
                continue; // We are done for comment tokens.
            }

            // Arrived here, all the tokens are variables, so we don't need to check for that.

            // If the name of the variable is not "$string", error.
            if ($tokens[$currentPtr]['content'] !== '$string') {
                $phpcsFile->addError(
                    'Variable "%s" is not expected in a lang file',
                    $currentPtr,
                    'UnexpectedVariable',
                    [$tokens[$currentPtr]['content']]
                );
                continue; // We are done for this token.
            }

            // Get the string key, if any.
            if (!$stringKey = $this->getStringKey($phpcsFile, $currentPtr)) {
                continue; // Problems with this string key, skip it (has been already reported).
            }

            // Have found a valid $string[KEY], let's calculate the end and store it.
            if ($currentEnd = $this->getStringEnd($phpcsFile, $currentPtr)) {
                if (!isset($this->strings[$currentGroup])) {
                    $this->strings[$currentGroup] = [];
                    $this->previousString = null; // Reset the previous string on new group.
                }
                // Check if the string already has been found earlier.
                if (isset($this->strings[$currentGroup][$stringKey])) {
                    $phpcsFile->addError('The string key "%s" is duplicated', $currentPtr, 'DuplicatedKey', [$stringKey]);
                    continue; // We are done for this string, won't report anything about it till fixed.
                } else {
                    // We can safely add the string to the group, if we are before the last pointer to fix.
                    if ($currentPtr < $this->stopFixingPtr) {
                        $this->strings[$currentGroup][$stringKey] = [$currentPtr, $currentEnd];
                    }
                }
            }

            if (null === $currentEnd) {
                // The string end is not as expected, report as error unless the next token
                // after the semicolon is a comment. In that case, we won't report it because
                // UnexpectedComment will take on it.
                $delegateToUnexpectedComment = false;
                $semicolonPtr = $phpcsFile->findNext(T_SEMICOLON, $currentPtr + 1);
                if (
                    (
                        isset($tokens[$semicolonPtr + 1]) && // There is a next token (not the end of the file)
                        $tokens[$semicolonPtr + 1]['code'] === T_COMMENT // And it's a comment.
                    ) ||
                    (
                        isset($tokens[$semicolonPtr + 2]) && // Or there are 2 more tokens (not the end of the file).
                        $tokens[$semicolonPtr + 1]['code'] === T_WHITESPACE && // And they are whitespace + comment.
                        $tokens[$semicolonPtr + 2]['code'] === T_COMMENT
                    )
                ) {
                    $delegateToUnexpectedComment = true;
                }
                if (!$delegateToUnexpectedComment) {
                    $phpcsFile->addError(
                        'Unexpected string end, it should be a line feed after a semicolon',
                        $currentPtr,
                        'UnexpectedEnd'
                    );
                    continue; // We are done for this string, won't report anything about it till fixed.
                }
            }

            // Note: We only issue these warnings if there are previous strings to compare with,
            // and, obviously, if the string is out of order.
            if ($this->previousString && strcmp($this->previousString, $stringKey) > 0) {
                // We are going to add this as fixable warning only if we are
                // before the last pointer to fix. This is an unordered string.
                $phpcsFile->addWarning(
                    'The string key "%s" is not in the correct order, it should be before "%s"',
                    $currentPtr,
                    'IncorrectOrder',
                    [$stringKey, $this->previousString],
                    0,
                    $currentPtr < $this->stopFixingPtr
                );
            }

            // Feed $previousString with the current string key.
            $this->previousString = $stringKey;
        } while ($currentPtr = $phpcsFile->findNext([T_VARIABLE, T_COMMENT], $currentPtr + 1)); // Move to next.

        // If we are fixing the file, we need to sort the strings and move them to the correct position.
        if ($phpcsFile->fixer->enabled) {
            $this->sortStringsAndFix($phpcsFile);
        }
    }

    /**
     * Given a lang file, fix all the sorting issues found.
     *
     * This is really similar to the insertion-sort algorithm, but with
     * a few optimisations to avoid unnecessary iterations. Should be
     * efficient enough against lists that are expected to be not
     * too long and already mostly sorted.
     *
     * @param File $phpcsFile The lang file being processed.
     */
    protected function sortStringsAndFix(File $phpcsFile): void {
        // Because of hard restrictions in CodeSniffer fixer (we cannot apply more than one change
        // to the same token in the same pass), we need to accumulate all the changes and apply them
        // at the end of the process. So we are going to build a big changeset to be applied all together.
        // Keys will be the token index and values an array, with operation (index, DELETE, INSERT) and content.

        // Get the file tokens, for ease of use.
        $tokens = $phpcsFile->getTokens();

        // We are going to perform the sorting within each detected group/section.
        foreach ($this->strings as $group => $strings) {
            $changeSet = []; // The changeset to be applied at the end of the iteration.

            $strings = $this->strings[$group];
            // Let's compare the keys in the array of strings with the sorted version of it.
            $sorted = $unSorted = array_keys($strings);
            sort($sorted, SORT_STRING);
            $count = count($sorted);
            for ($i = 0; $i < $count; $i++) {
                $sortedKey = $sorted[$i];
                $stringsKey = $unSorted[$i];

                // The string being checked is not in order (comparing with the sorted array).
                if ($sortedKey !== $stringsKey) {
                    // Apply the changes to the strings array by moving the key to the correct position.
                    $keyValue = $strings[$sortedKey];
                    // Remove the element to move.
                    unset($strings[$sortedKey]);
                    // Rebuild the array, with the element in new position.
                    $strings = array_slice($strings, 0, $i, true) +
                        [$sortedKey => $keyValue] +
                        array_slice($strings, $i, null, true);
                    $this->strings[$group] = $strings; // Update the group array with the rebuilt version.
                    $unSorted = array_keys($strings); // Update the unsorted keys array.

                    // Now add the required changes to the changeset that we'll be using when fixing the file.
                    // For every token in the string being moved, delete it and add it in the correct position.
                    foreach (range($keyValue[0], $keyValue[1]) as $tokenIndex) {
                        $tempToken = $tokens[$tokenIndex]; // Store the token.
                        $changeSet[$tokenIndex]['DELETE'] = ''; // Delete the current string token.
                        // Insert the token before the previous string.
                        if (!isset($changeSet[$strings[$stringsKey][0] - 1]['INSERT'])) {
                            $changeSet[$strings[$stringsKey][0] - 1]['INSERT'] = '';
                        }
                        $changeSet[$strings[$stringsKey][0] - 1]['INSERT'] .= $tempToken['content'];
                    }
                }
            }

            // Let's apply the accumulated changes to the file.
            if (!empty($changeSet)) {
                $phpcsFile->fixer->beginChangeset();
                foreach ($changeSet as $tokenIndex => $operations) {
                    if (isset($operations['DELETE'])) {
                        $phpcsFile->fixer->replaceToken($tokenIndex, '');
                    }
                    if (isset($operations['INSERT'])) {
                        $phpcsFile->fixer->addContent($tokenIndex, $operations['INSERT']);
                    }
                }
                $phpcsFile->fixer->endChangeset();
            }
        }
    }

    /**
     * Return the string key corresponding to the string at the pointer.
     * Note that the key has got any quote (single or double) trimmed.
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return string|null
     */
    protected function getStringKey(File $phpcsFile, int $stackPtr): ?string {
        $tokens = $phpcsFile->getTokens();

        // If the structure is not exactly: $string[KEY], add error and return null.
        if (
            $tokens[$stackPtr + 1]['code'] !== T_OPEN_SQUARE_BRACKET ||
            $tokens[$stackPtr + 2]['code'] !== T_CONSTANT_ENCAPSED_STRING ||
            $tokens[$stackPtr + 3]['code'] !== T_CLOSE_SQUARE_BRACKET
        ) {
            $phpcsFile->addError(
                "Unexpected string syntax, it should be `\$string['key']`",
                $stackPtr,
                'UnexpectedSyntax'
            );
            return null;
        }

        // Now we can safely extract the string key and return it.
        return trim($tokens[$stackPtr + 2]['content'], "'\"");
    }

    /**
     * Return the string final pointer, it should be always a \n after a T_SEMICOLON.
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return int|null The pointer to the end of the string, or null if it's not an expected string end.
     */
    protected function getStringEnd(File $phpcsFile, int $stackPtr): ?int {
        $tokens = $phpcsFile->getTokens();
        $currentEndToken = $phpcsFile->findNext(T_SEMICOLON, $stackPtr + 1) + 1;

        // Verify that the current end token is a line feed, if not, we won't be able to fix (swap).
        if (
            !isset($tokens[$currentEndToken]) ||
            $tokens[$currentEndToken]['code'] !== T_WHITESPACE ||
            $tokens[$currentEndToken]['content'] !== "\n"
        ) {
            return null; // This is not an expected string end.
        }

        return $currentEndToken;
    }
}
