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
 * Checks that each file contains the standard GPL comment.
 *
 * @copyright  2011 The Open University
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleHQ\MoodleCS\moodle\Sniffs\Files;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class BoilerplateCommentSniff implements Sniff
{
    protected static array $comment = [
        "// This file is part of",
        "//",
        "// Moodle is free software: you can redistribute it and/or modify",
        "// it under the terms of the GNU General Public License as published by",
        "// the Free Software Foundation, either version 3 of the License, or",
        "// (at your option) any later version.",
        "//",
        "// Moodle is distributed in the hope that it will be useful,",
        "// but WITHOUT ANY WARRANTY; without even the implied warranty of",
        "// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the",
        "// GNU General Public License for more details.",
        "//",
        "// You should have received a copy of the GNU General Public License",
        "// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.",
    ];

    public string $productName = 'Moodle';

    public string $firstLinePostfix = ' - https://moodle.org/';

    public function register(): array
    {
        return [T_OPEN_TAG];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        // We only want to do this once per file.
        $prevopentag = $phpcsFile->findPrevious(T_OPEN_TAG, $stackPtr - 1);
        if ($prevopentag !== false) {
            return; // @codeCoverageIgnore
        }

        if ($stackPtr > 0) {
            $phpcsFile->addError('The first thing in a PHP file must be the <?php tag.', 0, 'NoPHP');
            return;
        }

        $tokens = $phpcsFile->getTokens();

        // Allow T_PHPCS_XXX comment annotations in the first line (skip them).
        if ($commentptr = $phpcsFile->findNext(Tokens::$phpcsCommentTokens, $stackPtr + 1, $stackPtr + 3)) {
            $stackPtr = $commentptr;
        }

        $expectedafter = $stackPtr;

        $firstcommentptr = $phpcsFile->findNext(T_COMMENT, $expectedafter + 1);

        // Check that it appears to be a Moodle boilerplate comment.
        $regex = $this->regexForLine(self::$comment[0]);
        $boilerplatefound = ($firstcommentptr !== false) && preg_match($regex, $tokens[$firstcommentptr]['content']);

        if (!$boilerplatefound) {
            $fix = $phpcsFile->addFixableError(
                'Moodle boilerplate not found',
                $stackPtr,
                'NoBoilerplateComment'
            );

            if ($fix) {
                $this->insertBoilerplate($phpcsFile, $expectedafter);
            }
            return;
        }

        // Now check the text of the comment.
        $textfixed = false;
        $tokenptr = $firstcommentptr;
        foreach (self::$comment as $lineindex => $line) {
            // We already checked the first line.
            if ($lineindex === 0) {
                continue;
            }

            $tokenptr = $firstcommentptr + $lineindex;
            $iseof = $tokenptr >= $phpcsFile->numTokens;

            if ($iseof || $tokens[$tokenptr]['code'] != T_COMMENT || strpos($tokens[$tokenptr]['content'], '//') !== 0) {
                $errorline = $iseof ? $tokenptr - 1 : $tokenptr;

                $fix = $phpcsFile->addFixableError(
                    'Comment does not contain full Moodle boilerplate',
                    $errorline,
                    'CommentEndedTooSoon'
                );

                if ($fix) {
                    $this->completeBoilerplate($phpcsFile, $tokenptr - 1, $lineindex);
                    return;
                }

                // No point checking whitespace after comment if it is incomplete.
                return;
            }

            $regex = $this->regexForLine($line);

            if (!preg_match($regex, $tokens[$tokenptr]['content'])) {
                $fix = $phpcsFile->addFixableError(
                    'Line %s of the opening comment must start "%s".',
                    $tokenptr,
                    'WrongLine',
                    [$lineindex + 1, $line]
                );

                if ($fix) {
                    $phpcsFile->fixer->replaceToken($tokenptr, $line . "\n");
                    $textfixed = true;
                }
            }
        }

        if ($firstcommentptr !== $expectedafter + 1) {
            $fix = $phpcsFile->addFixableError(
                'Moodle boilerplate not found at first line',
                $expectedafter + 1,
                'NotAtFirstLine'
            );

            // If the boilerplate comment has been changed we need to commit the fixes before
            // moving it.
            if ($fix && !$textfixed) {
                $this->moveBoilerplate($phpcsFile, $firstcommentptr, $expectedafter);
            }

            // There's no point in checking the whitespace after the boilerplate
            // if it's not in the right place.
            return;
        }

        if ($tokenptr === $phpcsFile->numTokens - 1) {
            return;
        }

        // Let's jump over all the extra (allowed) consecutive comments to find the first non-comment token.
        $lastComment = $tokenptr;
        $nextComment = $tokenptr;
        while (($nextComment = $phpcsFile->findNext(T_COMMENT, ($nextComment + 1), null, false)) !== false) {
            // Only \n is allowed as spacing since the previous comment line.
            if (strpos($tokens[$nextComment - 1]['content'], "\n") === false) {
                // Stop looking for consecutive comments, some spacing broke the sequence.
                break;
            }
            if ($tokens[$nextComment]['line'] !== ($tokens[$lastComment]['line'] + 1)) {
                // Stop looking for comments, the lines are not consecutive.
                break;
            }
            $lastComment = $nextComment;
        }
        $tokenptr = $lastComment + 1; // Move to the last found comment + 1.

        $nextnonwhitespace = $phpcsFile->findNext(T_WHITESPACE, $tokenptr, null, true);

        // Allow indentation.
        if ($nextnonwhitespace !== false && strpos($tokens[$nextnonwhitespace - 1]['content'], "\n") === false) {
            $nextnonwhitespace--;
        }

        if (
            ($nextnonwhitespace === false) && array_key_exists($tokenptr + 1, $tokens) ||
            ($nextnonwhitespace !== false && $nextnonwhitespace !== $tokenptr + 1)
        ) {
            $fix = $phpcsFile->addFixableError(
                'Boilerplate comment must be followed by a single blank line or end of file',
                $tokenptr,
                'SingleTrailingNewLine'
            );

            if ($fix) {
                if ($nextnonwhitespace === false) {
                    while (array_key_exists(++$tokenptr, $tokens)) {
                        $phpcsFile->fixer->replaceToken($tokenptr, '');
                    }
                } elseif ($nextnonwhitespace === $tokenptr) {
                    $phpcsFile->fixer->addContentBefore($tokenptr, "\n");
                } else {
                    while (++$tokenptr < $nextnonwhitespace) {
                        if ($tokens[$tokenptr]['content'][-1] === "\n") {
                            $phpcsFile->fixer->replaceToken($tokenptr, '');
                        }
                    }
                }
            }
        }
    }

    private function fullComment(): array
    {
        $result = [];
        foreach (self::$comment as $lineindex => $line) {
            if ($lineindex === 0) {
                $result[] = $line . ' ' . $this->productName . $this->firstLinePostfix;
            } else {
                $result[] = str_replace('Moodle', $this->productName, $line);
            }
        }
        return $result;
    }

    private function insertBoilerplate(File $file, int $stackptr): void
    {
        $token = $file->getTokens()[$stackptr];
        $paddedComment = implode("\n", $this->fullComment()) . "\n";

        if ($token['code'] === T_OPEN_TAG) {
            $replacement = trim($token['content']) . "\n" . $paddedComment;
            $file->fixer->replaceToken($stackptr, $replacement);
        } else {
            $prefix = substr($token['content'], -1) === "\n" ? '' : "\n";
            $file->fixer->addContent($stackptr, $prefix . $paddedComment);
        }
    }

    private function moveBoilerplate(File $file, int $start, int $target): void
    {
        $tokens = $file->getTokens();

        $file->fixer->beginChangeset();

        // If we have only whitespace between expected location and first comment, just remove it.
        $nextnonwhitespace = $file->findPrevious(T_WHITESPACE, $start - 1, $target, true);

        if ($nextnonwhitespace === false || $nextnonwhitespace === $target) {
            foreach (range($target + 1, $start - 1) as $whitespaceptr) {
                $file->fixer->replaceToken($whitespaceptr, '');
            }
            $file->fixer->endChangeset();
            return;
        }

        // Otherwise shift existing comment to correct place.
        $existingboilerplate = [];
        foreach (range(0, count(self::$comment)) as $lineindex) {
            $tokenptr = $start + $lineindex;

            $existingboilerplate[] = $tokens[$tokenptr]['content'];

            $file->fixer->replaceToken($tokenptr, '');
        }

        $file->fixer->addContent($target, implode("", $existingboilerplate) . "\n");

        $file->fixer->endChangeset();
    }

    private function completeBoilerplate(File $file, $stackptr, int $lineindex): void
    {
        $file->fixer->addContent($stackptr, implode("\n", array_slice($this->fullComment(), $lineindex)) . "\n");
    }

    /**
     * @param string $line
     * @return string
     */
    private function regexForLine(string $line): string
    {
        // We need to match the blank lines in their entirety.
        if ($line === '//') {
            return '/^\/\/$/';
        }

        return str_replace(
            ['Moodle', 'https\\:'],
            ['.*', 'https?\\:'],
            '/^' . preg_quote($line, '/') . '/'
        );
    }
}
