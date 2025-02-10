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
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleHQ\MoodleCS\moodle\Sniffs\Namespaces;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class NamespaceStatementSniff implements Sniff
{
    public function register()
    {
        return [
            T_NAMESPACE,
        ];
    }

    public function process(File $file, $stackPtr)
    {
        $tokens = $file->getTokens();
        // Format should be:
        // - T_NAMESPACE
        // - T_WHITESPACE
        // - T_STRING

        $checkPtr = $stackPtr + 2;
        $token = $tokens[$checkPtr];
        if ($token['code'] === T_NS_SEPARATOR) {
            $fqdn = '';
            $stop = $file->findNext(Tokens::$emptyTokens, ($stackPtr + 2));
            for ($i = $stackPtr + 2; $i < $stop; $i++) {
                $fqdn .= $tokens[$i]['content'];
            }
            $fix = $file->addFixableError(
                'Namespace should not start with a slash: %s',
                $checkPtr,
                'LeadingSlash',
                [$fqdn]
            );

            if ($fix) {
                $file->fixer->beginChangeset();
                $file->fixer->replaceToken($checkPtr, '');
                $file->fixer->endChangeset();
            }
        }
    }
}
