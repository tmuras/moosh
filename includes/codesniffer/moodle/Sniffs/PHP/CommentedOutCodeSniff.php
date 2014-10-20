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
 * Sniff for detecting commented-out code.
 *
 * @package    local_codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class moodle_Sniffs_PHP_CommentedOutCodeSniff implements PHP_CodeSniffer_Sniff {
    public function __construct() {
        $this->supportedTokenizers = array('PHP', 'CSS');
    }

    /** @var int If a comment is more than this much code, a warning will be shown. */
    protected $maxpercentage = 40;

    public function register() {
        return PHP_CodeSniffer_Tokens::$commentTokens;
    }

    public function process(PHP_CodeSniffer_File $file, $stackptr) {
        $tokens = $file->getTokens();

        // Process whole comment blocks at once, so skip all but the first token.
        if ($stackptr > 0 && $tokens[$stackptr]['code'] === $tokens[($stackptr - 1)]['code']) {
            return;
        }

        // Ignore comments at the end of code blocks.
        if (substr($tokens[$stackptr]['content'], 0, 6) === '//end ') {
            return;
        }

        $content = '';
        if ($file->tokenizerType === 'PHP') {
            $content = '<?php ';
        }

        for ($i = $stackptr; $i < $file->numTokens; $i++) {
            if ($tokens[$stackptr]['code'] !== $tokens[$i]['code']) {
                break;
            }

            /*
                Trim as much off the comment as possible so we don't
                have additional whitespace tokens or comment tokens
            */

            $tokencontent = trim($tokens[$i]['content']);

            if (substr($tokencontent, 0, 2) === '//') {
                $tokencontent = substr($tokencontent, 2);
            }

            if (substr($tokencontent, 0, 1) === '#') {
                $tokencontent = substr($tokencontent, 1);
            }

            if (substr($tokencontent, 0, 3) === '/**') {
                $tokencontent = substr($tokencontent, 3);
            }

            if (substr($tokencontent, 0, 2) === '/*') {
                $tokencontent = substr($tokencontent, 2);
            }

            if (substr($tokencontent, -2) === '*/') {
                $tokencontent = substr($tokencontent, 0, -2);
            }

            if (substr($tokencontent, 0, 1) === '*') {
                $tokencontent = substr($tokencontent, 1);
            }

            $content .= $tokencontent.$file->eolChar;
        }

        $content = trim($content);

        if ($file->tokenizerType === 'PHP') {
            $content .= ' ?>';
        }

        // Quite a few comments use multiple dashes, equals signs etc
        // to frame comments and licence headers.
        $content = preg_replace('/[-=*]+/', '-', $content);

        $stringtokens = PHP_CodeSniffer_File::tokenizeString(
                $content, $file->tokenizer, $file->eolChar);

        $emptytokens = array(
            T_WHITESPACE,
            T_STRING,
            T_STRING_CONCAT,
            T_ENCAPSED_AND_WHITESPACE,
            T_NONE,
        );

        $numtokens = count($stringtokens);

        /*
            We know what the first two and last two tokens should be
            (because we put them there) so ignore this comment if those
            tokens were not parsed correctly. It obvously means this is not
            valid code.
        */

        // First token is always the opening PHP tag.
        if ($stringtokens[0]['code'] !== T_OPEN_TAG) {
            return;
        }

        // Last token is always the closing PHP tag.
        if ($stringtokens[($numtokens - 1)]['code'] !== T_CLOSE_TAG) {
            return;
        }

        // Second last token is always whitespace or a comment, depending
        // on the code inside the comment.
        if (in_array($stringtokens[($numtokens - 2)]['code'],
                PHP_CodeSniffer_Tokens::$emptyTokens) === false) {
            return;
        }

        // If the token before that is not a semicolon, this can't be code.
        if ($stringtokens[($numtokens - 3)]['code'] !== T_SEMICOLON) {
            return;
        }

        $numcomment = 0;
        $numcode    = 0;

        for ($i = 0; $i < $numtokens; $i++) {
            if (in_array($stringtokens[$i]['code'], $emptytokens) === true) {
                // Looks like comment.
                $numcomment++;
            } else {
                // Looks like code.
                $numcode++;
            }
        }

        // We subtract 3 from the token number so we ignore the start/end tokens
        // and their surrounding whitespace. We take 2 off the number of code
        // tokens so we ignore the start/end tokens.
        if ($numtokens > 3) {
            $numtokens -= 3;
        }

        if ($numcode >= 2) {
            $numcode -= 2;
        }

        $percentcode = ceil((($numcode / $numtokens) * 100));
        if ($percentcode > $this->maxpercentage) {
            // Just in case.
            $percentcode = min(100, $percentcode);

            $error = 'This comment is %s%% valid code; is this commented out code?';
            $data  = array($percentcode);
            $file->addWarning($error, $stackptr, 'Found', $data);
        }
    }
}
