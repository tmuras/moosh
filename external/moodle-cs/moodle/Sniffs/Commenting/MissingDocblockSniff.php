<?php

// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANdTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace MoodleHQ\MoodleCS\moodle\Sniffs\Commenting;

use MoodleHQ\MoodleCS\moodle\Util\Attributes;
use MoodleHQ\MoodleCS\moodle\Util\Docblocks;
use MoodleHQ\MoodleCS\moodle\Util\MoodleUtil;
use MoodleHQ\MoodleCS\moodle\Util\TokenUtil;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\ObjectDeclarations;

/**
 * Checks that all files an classes have appropriate docs.
 *
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MissingDocblockSniff implements Sniff
{
    /** @var array A list of standard method names used in unit test files */
    protected array $phpunitStandardMethodNames = [
        'setUp',
        'tearDown',
        'setUpBeforeClass',
        'tearDownAfterClass',
    ];

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
        $this->processScopes($phpcsFile, $stackPtr);
        $this->processFunctions($phpcsFile, $stackPtr);
        $this->processConstants($phpcsFile, $stackPtr);
    }

    protected function processScopes(File $phpcsFile, int $stackPtr): void {
        $tokens = $phpcsFile->getTokens();

        // Each class, interface, trait, and enum must have a docblock.
        // If a file has one class, interface, trait, or enum, the file docblock is optional.
        // Otherwise, the file docblock is required.

        $artifactCount = 0;
        $missingDocblocks = [];
        $find = Tokens::$ooScopeTokens;
        $find[] = T_FUNCTION;

        $typePtr = $stackPtr + 1;
        while ($typePtr = $phpcsFile->findNext($find, $typePtr + 1)) {
            $token = $tokens[$typePtr];
            if ($token['code'] === T_FUNCTION && !empty($token['conditions'])) {
                // Skip methods of classes, traits and interfaces.
                continue;
            }
            if ($token['code'] === T_ANON_CLASS && !empty($token['conditions'])) {
                // Skip anonymous classes.
                continue;
            }

            $artifactCount++;

            if ($token['code'] === T_FUNCTION) {
                // Skip functions. They are handled separately.
                continue;
            }

            if (!Docblocks::getDocBlockPointer($phpcsFile, $typePtr)) {
                $missingDocblocks[] = $typePtr;
            }
        }

        if ($artifactCount !== 1) {
            // See if there is a file docblock.
            $fileblock = Docblocks::getDocBlockPointer($phpcsFile, $stackPtr);

            if ($fileblock === null) {
                $objectName = TokenUtil::getObjectName($phpcsFile, $stackPtr);
                $phpcsFile->addError('Missing docblock for file %s', $stackPtr, 'File', [$objectName]);
            }
        }

        foreach ($missingDocblocks as $typePtr) {
            $token = $tokens[$typePtr];
            $objectName = TokenUtil::getObjectName($phpcsFile, $typePtr);
            $objectType = TokenUtil::getObjectType($phpcsFile, $typePtr);

            $phpcsFile->addError('Missing docblock for %s %s', $typePtr, ucfirst($objectType), [$objectType, $objectName]);
        }

        if ($artifactCount === 1) {
            // Only one artifact.
            // No need for file docblock.
            return;
        }
    }

    /**
     * Process functions.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position in the stack.
     */
    protected function processFunctions(File $phpcsFile, int $stackPtr): void {
        // Missing docblocks for unit tests are treated as warnings.
        $isUnitTestFile = MoodleUtil::isUnitTest($phpcsFile);

        $tokens = $phpcsFile->getTokens();

        $missingDocblocks = [];
        $knownClasses = [];

        $typePtr = $stackPtr + 1;
        while ($typePtr = $phpcsFile->findNext(T_FUNCTION, $typePtr + 1)) {
            $token = $tokens[$typePtr];
            $extendsOrImplements = false;

            if ($isUnitTestFile) {
                if (in_array(TokenUtil::getObjectName($phpcsFile, $typePtr), $this->phpunitStandardMethodNames)) {
                    // Skip standard PHPUnit methods.
                    continue;
                }
            }

            if (count($token['conditions']) > 0) {
                // This method has conditions (a Class, Interface, Trait, etc.).
                // Check if that container extends or implements anything.
                foreach ($token['conditions'] as $condition => $conditionCode) {
                    if ($conditionCode === T_USE) {
                        // Skip any method inside a USE.
                        continue 2;
                    }
                    if (!array_key_exists($condition, $knownClasses)) {
                        $extendsOrImplements = $extendsOrImplements || ObjectDeclarations::findExtendedClassName(
                            $phpcsFile,
                            $condition
                        );
                        $extendsOrImplements = $extendsOrImplements || ObjectDeclarations::findImplementedInterfaceNames(
                            $phpcsFile,
                            $condition
                        );
                        $extendsOrImplements = $extendsOrImplements || ObjectDeclarations::findExtendedInterfaceNames(
                            $phpcsFile,
                            $condition
                        );
                        $knownClasses[$condition] = $extendsOrImplements;
                    }
                    $extendsOrImplements = $extendsOrImplements || $knownClasses[$condition];
                    if ($extendsOrImplements) {
                        break;
                    }
                }
            }

            if (!Docblocks::getDocBlockPointer($phpcsFile, $typePtr)) {
                $missingDocblocks[$typePtr] = $extendsOrImplements;
            }
        }

        foreach ($missingDocblocks as $typePtr => $extendsOrImplements) {
            $token = $tokens[$typePtr];
            if ($extendsOrImplements) {
                $attributes = Attributes::getAttributePointers($phpcsFile, $typePtr);
                foreach ($attributes as $attributePtr) {
                    $attribute = Attributes::getAttributeProperties($phpcsFile, $attributePtr);
                    if ($attribute['attribute_name'] === '\Override') {
                        // Skip methods that are marked as overrides.
                        continue 2;
                    }
                }
            }

            $objectName = TokenUtil::getObjectName($phpcsFile, $typePtr);
            $objectType = TokenUtil::getObjectType($phpcsFile, $typePtr);

            if ($isUnitTestFile) {
                if (substr($objectName, 0, 5) !== 'test_') {
                    $phpcsFile->addWarning(
                        'Missing docblock for %s %s in testcase',
                        $typePtr,
                        'MissingTestcaseMethodDescription',
                        [$objectType, $objectName]
                    );
                }
            } else {
                $phpcsFile->addError('Missing docblock for %s %s', $typePtr, ucfirst($objectType), [$objectType, $objectName]);
            }
        }
    }

    /**
     * Process constants.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position in the stack.
     */
    protected function processConstants(File $phpcsFile, int $stackPtr): void {
        $tokens = $phpcsFile->getTokens();

        $typePtr = $stackPtr + 1;
        while ($typePtr = $phpcsFile->findNext(T_CONST, $typePtr + 1)) {
            $token = $tokens[$typePtr];
            $containerName = null;

            if (count($token['conditions']) > 0) {
                foreach ($token['conditions'] as $conditionPtr => $conditionCode) {
                    // Skip any constant inside a USE.
                    if ($conditionCode === T_USE) {
                        continue 2;
                    }
                    if (in_array($conditionCode, Collections::closedScopes())) {
                        $containerName = TokenUtil::getObjectName($phpcsFile, $conditionPtr);
                    }
                }
            }

            if (Docblocks::getDocBlockPointer($phpcsFile, $typePtr)) {
                // This is documented.
                continue;
            }

            // Get the constant name
            // We have to find the equals and step back from there.
            // PHP 8.3 introduces the concept of typed constants but both the type and name are presented as T_STRING
            $equalPtr = $phpcsFile->findNext(T_EQUAL, $typePtr + 1);
            $namePtr = $phpcsFile->findPrevious(T_STRING, $equalPtr - 1, $typePtr);
            $objectName = $tokens[$namePtr]['content'];

            if ($containerName) {
                $phpcsFile->addError(
                    'Missing docblock for constant %s::%s',
                    $typePtr,
                    'Constant',
                    [$containerName, $objectName]
                );
            } else {
                $phpcsFile->addError(
                    'Missing docblock for constant %s',
                    $typePtr,
                    'Constant',
                    [$objectName]
                );
            }
        }
    }
}
