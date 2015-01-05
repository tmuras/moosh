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
 * Sniff for debugging and other functions that we don't want used in finished code.
 *
 * @package    local_codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (class_exists('Generic_Sniffs_PHP_ForbiddenFunctionsSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception(
            'Class Generic_Sniffs_PHP_ForbiddenFunctionsSniff not found');
}

class moodle_Sniffs_PHP_ForbiddenFunctionsSniff
        extends Generic_Sniffs_PHP_ForbiddenFunctionsSniff {
    /** Constructor. */
    public function __construct() {
        $this->forbiddenFunctions = array(
            // Usual development debugging functions.
            'sizeof'       => 'count',
            'delete'       => 'unset',
            'error_log'    => null,
            'print_r'      => null,
            'print_object' => null,
            // Dangerous functions. From coding style.
            'extract'      => null,
            // Note that some of these are handled as specific tokens by the Tokenizer
            // and detected by {@link moodle_Sniffs_PHP_ForbiddenTokensSniff} instead.
            // We just keep them here to ensure behavior does not change in the future.
            'eval'         => null, // T_EVAL token.
            'goto'         => null, // T_GOTO token.
        );
    }
}
