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

namespace MoodleHQ\MoodleCS\moodle\Util;

use PHP_CodeSniffer\Files\File;

/**
 * Utility class for handling types.
 *
 * @copyright Andrew Lyons <andrew@nicols.co.uk>
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class TypeUtil
{
    /**
     * An array of variable types for param/var we will check.
     *
     * @var string[]
     */
    protected static array $allowedTypes = [
        'array',
        'bool',
        'false',
        'float',
        'int',
        'mixed',
        'null',
        'object',
        'string',
        'true',
        'resource',
        'callable',
    ];

    /**
     * Standardise a type to a known type.
     *
     * @param string $type The type to standardise.
     * @return string|null
     */
    public static function standardiseType(string $type): ?string {
        $type = strtolower($type);
        if (in_array($type, self::$allowedTypes, true)) {
            return $type;
        }

        switch ($type) {
            case 'array()':
                return 'array';
            case 'boolean':
                return 'bool';
            case 'double':
            case 'real':
                return 'float';
            case 'integer':
                return 'int';
            default:
                return null;
        }
    }


    /**
     * Returns a valid variable type for param/var tags.
     *
     * If type is not one of the standard types, it must be a custom type.
     * Returns the correct type name suggestion if type name is invalid.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in the stack passed in $tokens.
     * @param string $varType The variable type to process.
     * @return string
     */
    public static function suggestType(
        File $phpcsFile,
        int $stackPtr,
        string $varType
    ): string {
        $lowerVarType = strtolower($varType);
        if ($normalisedType = self::standardiseType($lowerVarType)) {
            return $normalisedType;
        }
        if (substr($varType, -2) === '[]') {
            return sprintf(
                '%s[]',
                self::suggestType($phpcsFile, $stackPtr, substr($varType, 0, -2))
            );
        }

        if (strpos($lowerVarType, 'array(') !== false) {
            // Valid array declaration:
            // array, array(type), array(type1 => type2).
            $matches = [];
            $pattern = '/^array\(\s*([^\s^=^>]*)(\s*=>\s*(.*))?\s*\)/i';
            if (preg_match($pattern, $varType, $matches) !== 0) {
                $type1 = '';
                if (isset($matches[1]) === true) {
                    $type1 = $matches[1];
                }

                $type2 = '';
                if (isset($matches[3]) === true) {
                    $type2 = $matches[3];
                }

                $type1 = self::suggestType($phpcsFile, $stackPtr, $type1);
                $type2 = self::suggestType($phpcsFile, $stackPtr, $type2);

                // Note: The phpdoc array syntax only allows you to describe the array value type.
                // https://docs.phpdoc.org/latest/guide/guides/types.html#arrays
                if ($type1 && !$type2) {
                    // This is an array of [type2, type2, type2].
                    return "{$type1}[]";
                }
                // This is an array of [type1 => type2, type1 => type2, type1 => type2].
                return "{$type2}[]";
            } else {
                return 'array';
            }
        }

        // Must be a custom type name.
        return $varType;
    }

    /**
     * Validate a type in its entirety.
     *
     * The method currently supports built-in types, and Union types.
     * It does not currently support DNF, or other complex types.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in the stack passed in $tokens.
     * @param string $type The type to validate.
     * @return string The validated type.
     */
    public static function getValidatedType(
        File $phpcsFile,
        int $stackPtr,
        string $type
    ): string {
        $types = explode('|', $type);
        $validatedTypes = [];
        foreach ($types as $type) {
            $validatedTypes[] = self::suggestType($phpcsFile, $stackPtr, $type);
        }
        return implode('|', $validatedTypes);
    }
}
