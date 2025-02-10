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

namespace MoodleHQ\MoodleCS\moodle\Sniffs\Commenting;

use MoodleHQ\MoodleCS\moodle\Util\Docblocks;
use MoodleHQ\MoodleCS\moodle\Util\TokenUtil;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHPCSUtils\Tokens\Collections;

/**
 * Checks that a file has appropriate tags in either the file, or single artefact block.
 *
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class FileExpectedTagsSniff implements Sniff
{
    /**
     * The regular expression used to match the expected license.
     *
     * Note that the regular expression is applied using preg_quote to escape as required.
     *
     * Note that, if the regular expression is the empty string,
     * then this Sniff will do nothing.
     *
     * Example values:
     * - Empty string or null: No check is done.
     *  ''
     * - The GNU GPL v3 or later license with either http or https license text
     *   '@https?://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later@':
     *
     * @var null|string
     */
    public ?string $preferredLicenseRegex = '@https?://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later@';

    /**
     * Register for open tag (only process once per file).
     */
    public function register() {
        return [
            T_OPEN_TAG,
        ];
    }

    /**
     * Processes php files and perform various checks with file.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position in the stack.
     */
    public function process(File $phpcsFile, $stackPtr) {
        // Get the stack pointer for the file-level docblock.
        $stackPtr = Docblocks::getDocBlockPointer($phpcsFile, $stackPtr);
        if ($stackPtr === null) {
            // There is no file-level docblock.

            if (TokenUtil::countGlobalScopesInFile($phpcsFile) > 1) {
                // There are more than one item in the global scope.
                // Only accept the file docblock.
                return;
            } else {
                // There is only one item in the global scope.
                // We can accept the file docblock or the item docblock.
                $stackPtr = $phpcsFile->findNext(Collections::closedScopes(), 0);
            }
        }

        $this->processFileCopyright($phpcsFile, $stackPtr);
        $this->processFileLicense($phpcsFile, $stackPtr);
    }

    /**
     * Process the file docblock and check for the presence of a @copyright tag.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position in the stack.
     */
    private function processFileCopyright(File $phpcsFile, $stackPtr): void {
        $docPtr = Docblocks::getDocBlockPointer($phpcsFile, $stackPtr);
        $copyrightTokens = Docblocks::getMatchingDocTags($phpcsFile, $docPtr, '@copyright');
        if (empty($copyrightTokens)) {
            if (empty($docPtr)) {
                $docPtr = $stackPtr;
            }

            $phpcsFile->addError(
                'Missing @copyright tag',
                $docPtr,
                'CopyrightTagMissing'
            );
            return;
        }
    }

    /**
     * Process the file docblock and check for the presence of a @license tag.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position in the stack.
     */
    private function processFileLicense(File $phpcsFile, $stackPtr): void {
        $tokens = $phpcsFile->getTokens();
        $docPtr = Docblocks::getDocBlockPointer($phpcsFile, $stackPtr);
        $foundTokens = Docblocks::getMatchingDocTags($phpcsFile, $docPtr, '@license');
        if (empty($foundTokens)) {
            if ($docPtr) {
                $phpcsFile->addError(
                    'Missing @license tag',
                    $docPtr,
                    'LicenseTagMissing'
                );
            } else {
                $phpcsFile->addError(
                    'Missing @license tag',
                    $stackPtr,
                    'LicenseTagMissing'
                );
            }
            return;
        }

        // If specified, get the regular expression from the config.
        if (($regex = Config::getConfigData('moodleLicenseRegex')) !== null) {
            $this->preferredLicenseRegex = $regex;
        }

        if ($this->preferredLicenseRegex === '') {
            return;
        }

        $licensePtr = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $foundTokens[0]);
        $license = $tokens[$licensePtr]['content'];

        if (!preg_match($this->preferredLicenseRegex, $license)) {
            $phpcsFile->addWarning(
                'Invalid @license tag. Value "%s" does not match expected format',
                $licensePtr,
                'LicenseTagInvalid',
                [$license]
            );
        }
    }
}
