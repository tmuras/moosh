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
 * Sniff to prevent some reserved words to be used as function calls.
 *
 * This Sniff is a subclass of {@link PHPCompatibility_Sniffs_PHP_ForbiddenNamesAsInvokedFunctionsSniff}
 * that does the job pretty well, but whitelisting some uses that
 * are historically allowed in Moodle, no matter they are not very PHP-elegant.
 *
 * @package    local_codechecker
 * @copyright  2014 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (class_exists('PHPCompatibility_Sniffs_PHP_ForbiddenNamesAsInvokedFunctionsSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception(
            'PHPCompatibility_Sniffs_PHP_ForbiddenNamesAsInvokedFunctionsSniff not found');
}

class moodle_Sniffs_PHP_ForbiddenNamesAsInvokedFunctionsSniff
        extends PHPCompatibility_Sniffs_PHP_ForbiddenNamesAsInvokedFunctionsSniff {
    /** Constructor. */
    public function __construct() {
        // Moodle allows clone to be used as function for now.
        if (isset($this->targetedTokens[T_CLONE])) {
            unset($this->targetedTokens[T_CLONE]);
        }
    }
}
