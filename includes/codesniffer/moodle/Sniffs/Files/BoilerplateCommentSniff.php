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
 * Checks that each file contains the standard GPL comment.
 *
 * @package    local_codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class moodle_Sniffs_Files_BoilerplateCommentSniff implements PHP_CodeSniffer_Sniff {
    protected static $comment = array(
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
        "// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.",
    );
    public function register() {
        return array(T_OPEN_TAG);
    }

    public function process(PHP_CodeSniffer_File $file, $stackptr) {
        // We only want to do this once per file.
        $prevopentag = $file->findPrevious(T_OPEN_TAG, $stackptr - 1);
        if ($prevopentag !== false) {
            return;
        }

        if ($stackptr > 0) {
            $file->addError('The first thing in a PHP file must be the <?php tag.', 0, 'NoPHP');
            return;
        }

        $tokens = $file->getTokens();

        // Find count the number of newlines after the opening <?PHP. We only
        // count enough to see if the number is right.
        // Note that the opening PHP tag includes one newline.
        $numnewlines = 0;
        for ($i = 1; $i <= 5; ++$i) {
            if ($tokens[$i]['code'] == T_WHITESPACE && $tokens[$i]['content'] == "\n") {
                $numnewlines = $i;
            } else {
                break;
            }
        }

        if ($numnewlines > 0) {
            $file->addError('The opening <?php tag must be followed by exactly one newline.',
                    1, 'WrongWhitespace');
            return;
        }
        $offset = $numnewlines + 1;

        // Now check the text of the comment.
        foreach (self::$comment as $lineindex => $line) {
            $tokenptr = $offset + $lineindex;

            if (!array_key_exists($tokenptr, $tokens)) {
                $file->addError('Reached the end of the file before finding ' .
                        'all of the opening comment.', $tokenptr - 1, 'FileTooShort');
                return;
            }

            $regex = str_replace('Moodle', '.*', '/^' . preg_quote($line, '/') . '/');
            if ($tokens[$tokenptr]['code'] != T_COMMENT ||
                    !preg_match($regex, $tokens[$tokenptr]['content'])) {

                $file->addError('Line %s of the opening comment must start "%s".',
                        $tokenptr, 'WrongLine', array($lineindex + 1, $line));
            }
        }
    }
}
