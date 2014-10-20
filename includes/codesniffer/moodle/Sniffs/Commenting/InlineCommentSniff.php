<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Verifies that inline comments conform to their coding standards.
 *
 * Based on {@link Squiz_Sniffs_Commenting_InlineCommentSniff}.
 *
 * @package    local_codechecker
 * @copyright  2012 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class moodle_Sniffs_Commenting_InlineCommentSniff implements PHP_CodeSniffer_Sniff {

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return array(
                T_COMMENT,
                T_DOC_COMMENT,
               );
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
        $tokens = $phpcsFile->getTokens();

        // If this is a function/class/interface doc block comment, skip it.
        // We are only interested in inline doc block comments, which are
        // not allowed.
        if ($tokens[$stackPtr]['code'] === T_DOC_COMMENT) {
            $nextToken = $phpcsFile->findNext(
                PHP_CodeSniffer_Tokens::$emptyTokens,
                ($stackPtr + 1),
                null,
                true
            );

            $ignore = array(
                       T_CLASS,
                       T_INTERFACE,
                       T_FUNCTION,
                       T_PUBLIC,
                       T_PRIVATE,
                       T_PROTECTED,
                       T_FINAL,
                       T_STATIC,
                       T_ABSTRACT,
                       T_CONST,
                       T_OBJECT,
                       T_PROPERTY,
                      );

            // We allow phpdoc before all those tokens.
            if (in_array($tokens[$nextToken]['code'], $ignore) === true) {
                return;

            // Allow phpdoc before define() token (see CONTRIB-4150).
            } else if ($tokens[$nextToken]['code'] == T_STRING and $tokens[$nextToken]['content'] == 'define') {
                return;

            } else {
                $prevToken = $phpcsFile->findPrevious(
                    PHP_CodeSniffer_Tokens::$emptyTokens,
                    ($stackPtr - 1),
                    null,
                    true
                );

                // Allow phpdocs after php open tag (file phpdoc).
                if ($tokens[$prevToken]['code'] === T_OPEN_TAG) {
                    return;
                }

                // Only error once per comment.
                if (substr($tokens[$stackPtr]['content'], 0, 3) === '/**') {
                    $error  = 'Inline doc block comments are not allowed; use "/* Comment */" or "// Comment" instead';
                    $phpcsFile->addError($error, $stackPtr, 'DocBlock');
                }
            }
        }

        if ($tokens[$stackPtr]['content']{0} === '#') {
            $error  = 'Perl-style comments are not allowed; use "// Comment" instead';
            $phpcsFile->addError($error, $stackPtr, 'WrongStyle');
        }

        $comment = rtrim($tokens[$stackPtr]['content']);

        // Only want inline comments.
        if (substr($comment, 0, 2) !== '//') {
            return;
        }

        // Allow pure comment separators only (look for comments having at least 10 hyphens).
        if (preg_match('!^// (.*?)(-{10})(.*)$!', $comment, $matches)) {
            // It's a comment separator.
            // Verify it's pure.
            $wrongcharsfound = trim(str_replace('-', '', $matches[1] . $matches[3]));
            if ($wrongcharsfound !== '') {
                $error = 'Comment separators are not allowed to contain other chars buy hyphens (-). Found: (%s)';
                // Basic clean dupes for notification
                $wrongcharsfound = implode(array_keys(array_flip(preg_split('//', $wrongcharsfound, -1, PREG_SPLIT_NO_EMPTY))));
                $data = array($wrongcharsfound);
                $phpcsFile->addWarning($error, $stackPtr, 'IncorrectCommentSeparator', $data);
            }
            // Verify length between 20 and 120
            $hyphencount = strlen($matches[1] . $matches[2] . $matches[3]);
            if ($hyphencount < 20 or $hyphencount > 120) {
                $error = 'Comment separators length must contain 20-120 chars, %s found';
                $phpcsFile->addWarning($error, $stackPtr, 'WrongCommentSeparatorLength', array($hyphencount));
            }
            // Verify it's the first token in the line.
            $prevToken = $phpcsFile->findPrevious(
                PHP_CodeSniffer_Tokens::$emptyTokens,
                ($stackPtr - 1),
                null,
                true
            );
            if (!empty($prevToken) and $tokens[$prevToken]['line'] == $tokens[$stackPtr]['line']) {
                $error = 'Comment separators must be the unique text in the line, code found before.';
                $phpcsFile->addWarning($error, $stackPtr, 'WrongCommentCodeFoundBefore', array());
            }
            // Don't want to continue processing the comment separator.
            return;
        }

        // Count slashes
        $slashCount = strlen(preg_replace('!^([/#]*).*!', '\\1', $comment));

        // Three or more slashes not allowed
        if ($slashCount > 2) {
            $error = '%s slashes comments are not allowed; use "// Comment" instead';
            $data = array($slashCount);
            $phpcsFile->addError($error, $stackPtr, 'WrongStyle', $data);
        }

        $spaceCount = 0;
        for ($i = $slashCount; $i < strlen($comment); $i++) {
            if ($comment[$i] !== ' ') {
                break;
            }
            $spaceCount++;
        }

        if (strlen($comment) > $slashCount and $spaceCount === 0) {
            $error = 'No space before comment text; expected "// %s" but found "%s"';
            $data  = array(
                      substr($comment, $slashCount),
                      $comment,
                     );
            $phpcsFile->addError($error, $stackPtr, 'NoSpaceBefore', $data);
        }

        // The below section determines if a comment block is correctly capitalised,
        // and ends in a full-stop. It will find the last comment in a block, and
        // work its way up.
        $nextComment = $phpcsFile->findNext(array(T_COMMENT), ($stackPtr + 1), null, false);

        if (($nextComment !== false) && (($tokens[$nextComment]['line']) === ($tokens[$stackPtr]['line'] + 1))) {
            return;
        }

        $topComment  = $stackPtr;
        $lastComment = $stackPtr;
        while (($topComment = $phpcsFile->findPrevious(array(T_COMMENT), ($lastComment - 1), null, false)) !== false) {
            if ($tokens[$topComment]['line'] !== ($tokens[$lastComment]['line'] - 1)) {
                break;
            }
            $lastComment = $topComment;
        }

        $topComment  = $lastComment;
        $commentText = '';

        for ($i = $topComment; $i <= $stackPtr; $i++) {
            if ($tokens[$i]['code'] === T_COMMENT) {
                $commentText .= trim(preg_replace('!^[/#]*(.*)!', '\\1', $tokens[$i]['content']));
            }
        }

        // rtrim parenthesis and quotes, English can have the full-stop
        // within them if they are full sentences. Epic wrong rule, IMO :-)
        $commentText = rtrim($commentText, "'\")");

        if ($commentText === '') {
            $error = 'Blank comments are not allowed';
            $phpcsFile->addError($error, $stackPtr, 'Empty');
            return;
        }

        if (preg_match('!^([A-Z0-9]|\.{3})!', $commentText) === 0) {
            $error = 'Inline comments must start with a capital letter, digit or 3-dots sequence';
            $phpcsFile->addWarning($error, $topComment, 'NotCapital');
        }

        $commentCloser   = $commentText[(strlen($commentText) - 1)];
        $acceptedClosers = array(
                            'full-stops'        => '.',
                            'exclamation marks' => '!',
                            'or question marks' => '?',
                           );

        if (in_array($commentCloser, $acceptedClosers) === false) {
            $error = 'Inline comments must end in %s';
            $ender = '';
            foreach ($acceptedClosers as $closerName => $symbol) {
                $ender .= ' '.$closerName.',';
            }

            $ender = rtrim($ender, ',');
            $data  = array($ender);
            $phpcsFile->addWarning($error, $stackPtr, 'InvalidEndChar', $data);
        }
    }
}
