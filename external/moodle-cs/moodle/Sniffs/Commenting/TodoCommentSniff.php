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
 * Checks that some comments have a corresponding issue in the tracker.
 *
 * This sniff checks that both inline TODO comments and phpdoc @todo tags
 * have a corresponding issue in the tracker.
 *
 * @copyright 2024 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleHQ\MoodleCS\moodle\Sniffs\Commenting;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class TodoCommentSniff implements Sniff
{
    /**
     * The regular expression to match comments against.
     *
     * Note that the regular expression is applied as is,
     * so it must be escaped if needed to. Partial matching
     * is enough to consider the comment as correct TODO.
     *
     * Note that, if the regular expression is the empty string,
     * then this Sniff will do nothing.
     *
     * Example values:
     * - 'MDL-[0-9]+': The default value, a Moodle core issue is required.
     * - 'CONTRIB-[0-9]+': A Moodle plugin issue is required.
     * - '(MDL|CONTRIB)-[0-9]+': A Moodle core or plugin issue is required.
     * - 'https://': Any URL is required.
     * - '' (empty string or null): No check is done.
     */
    public ?string $commentRequiredRegex = 'MDL-[0-9]+';

    /**
     * Returns an array of tokens this Sniff wants to listen for.
     *
     * @return int[]|string[]
     */
    public function register(): array {
        return [T_COMMENT, T_DOC_COMMENT_TAG];
    }

    /**
     * Processes this Sniff, when one of its tokens is encountered.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr): void {
        // If specified, get the regular expression from the config.
        if (($regex = Config::getConfigData('moodleTodoCommentRegex')) !== null) {
            $this->commentRequiredRegex = $regex;
        }

        // If the regular expression is empty, then we don't want to do anything.
        if (empty($this->commentRequiredRegex)) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_COMMENT) {
            // Found an inline comment, let's process it.
            $this->processInlineComment($phpcsFile, $stackPtr);
        } elseif ($tokens[$stackPtr]['code'] === T_DOC_COMMENT_TAG) {
            // Found a phpdoc tag, let's process it.
            $this->processDocCommentTag($phpcsFile, $stackPtr);
        }
    }

    protected function processInlineComment(File $phpcsFile, int $stackPtr): void {
        $tokens = $phpcsFile->getTokens();

        // If the previous token is also an inline comment, then
        // we already have processed this comment before.
        $previousToken = $phpcsFile->findPrevious(T_COMMENT, ($stackPtr - 1), ($stackPtr - 1), false);
        if ($tokens[$stackPtr]['line'] === ($tokens[$previousToken]['line'] + 1)) {
            return;
        }

        // Only want inline comments.
        if (substr($tokens[$stackPtr]['content'], 0, 2) !== '//') {
            return;
        }

        // Get the content of the whole inline comment, excluding whitespace.
        $commentContent = trim($tokens[$stackPtr]['content']);
        $nextComment = $stackPtr;
        $lastComment = $stackPtr;
        while (($nextComment = $phpcsFile->findNext(T_COMMENT, ($nextComment + 1), null, false)) !== false) {
            // Until we get a token in non-consecutive line (inline comments are consecutive).
            if ($tokens[$nextComment]['line'] !== ($tokens[$lastComment]['line'] + 1)) {
                break;
            }
            $commentContent .= ' ' . trim(substr($tokens[$nextComment]['content'], 2));
            $lastComment = $nextComment;
        }

        // Time to analise the comment contents.

        // Only want inline comments starting with TODO (ignoring the colon existence /
        // absence on purpose, it's not important for this Sniff).
        if (strpos($commentContent, '// TODO') === false) {
            return;
        }

        // Check if the inline comment has the required information.
        $this->evaluateComment($phpcsFile, $stackPtr, 'inline', $commentContent);
    }

    protected function processDocCommentTag(File $phpcsFile, int $stackPtr): void {
        $tokens = $phpcsFile->getTokens();

        // We are only interested in @todo tags.
        if ($tokens[$stackPtr]['content'] !== '@todo') {
            return;
        }

        // Get the content of the whole @todo tag, until another tag or phpdoc block ends.
        $commentContent = '';
        $nextComment = $stackPtr;
        $tags = [T_DOC_COMMENT_STRING, T_DOC_COMMENT_TAG, T_DOC_COMMENT_CLOSE_TAG];
        while (($nextComment = $phpcsFile->findNext($tags, ($nextComment + 1), null, false)) !== false) {
            // Until we get another tag or the end of the phpdoc block.
            if (
                $tokens[$nextComment]['code'] === T_DOC_COMMENT_TAG ||
                $tokens[$nextComment]['code'] === T_DOC_COMMENT_CLOSE_TAG
            ) {
                break;
            }
            $commentContent .= ' ' . trim($tokens[$nextComment]['content']);
        }

        // Time to analise the comment contents.

        // Check if the inline comment has the required information.
        $this->evaluateComment($phpcsFile, $stackPtr, 'phpdoc', $commentContent);
    }

    protected function evaluateComment(
        File $phpcsFile,
        int $stackPtr,
        string $type,
        string $commentContent
    ): void {
        // Just verify that the comment matches the required regular expression.
        if (preg_match('~' . $this->commentRequiredRegex . '~', $commentContent)) {
            return;
        }

        // Arrived here, no match, create a warning with all the info.
        $error = 'Missing required "%s" information in %s comment: %s';
        $errorParams = [
            $this->commentRequiredRegex,
            $type,
            trim($commentContent),
        ];
        $code = $type === 'phpdoc' ? 'MissingInfoPhpdoc' : 'MissingInfoInline';
        $phpcsFile->addWarning($error, $stackPtr, $code, $errorParams);
    }
}
