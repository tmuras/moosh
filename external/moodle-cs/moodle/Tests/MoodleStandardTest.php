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

namespace MoodleHQ\MoodleCS\moodle\Tests;

/**
 * Test various "moodle" phpcs standard sniffs.
 *
 * Each case covers one sniff. Self-explanatory
 *
 * @copyright  2013 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @todo Complete coverage of all Sniffs.
 */
class MoodleStandardTest extends MoodleCSBaseTestCase
{
    /**
     * Test the PSR12.Functions.ReturnTypeDeclaration sniff.
     *
     * @covers \PHP_CodeSniffer\Standards\PSR12\Sniffs\Functions\ReturnTypeDeclarationSniff
     */
    public function testPSR12FunctionsReturnTypeDeclaration() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('PSR12.Functions.ReturnTypeDeclaration');
        $this->setFixture(__DIR__ . '/fixtures/psr12_functions_returntypedeclaration.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $errors = [
            30 => 'SpaceBeforeColon',
            34 => 'SpaceBeforeReturnType',
            38 => 'SpaceBeforeReturnType',
        ];
        $warnings = [];
        $this->setErrors($errors);
        $this->setWarnings($warnings);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test the PSR12.Functions.NullableTypeDeclaration sniff.
     *
     * @covers \PHP_CodeSniffer\Standards\PSR12\Sniffs\Functions\NullableTypeDeclarationSniff
     */
    public function testPSR12FunctionsNullableTypeDeclaration() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('PSR12.Functions.NullableTypeDeclaration');
        $this->setFixture(__DIR__ . '/fixtures/psr12_functions_nullabletypedeclaration.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $errors = [
            17 => 'WhitespaceFound',
            22 => 3,
        ];
        $warnings = [];
        $this->setErrors($errors);
        $this->setWarnings($warnings);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test the PSR2.Methods.MethodDeclaration sniff.
     *
     * @covers \PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\MethodDeclarationSniff
     */
    public function testPSR2MethodsMethodDeclaration() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('PSR2.Methods.MethodDeclaration');
        $this->setFixture(__DIR__ . '/fixtures/psr2_methods_methoddeclaration.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            33 => 'The static declaration must come after the visibility',
            34 => 1,
            35 => 1,
            37 => 'The final declaration must precede the visibility',
            38 => 1,
            39 => 1,
            41 => ['FinalAfterVisibility', 'StaticBeforeVisibility'],
            42 => 2,
            43 => 2,
            45 => 'The abstract declaration must precede the visibility',
            46 => 1,
            48 => ['AbstractAfterVisibility', 'StaticBeforeVisibility'],
            49 => 2,
        ]);
        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test the moodle.Commenting.InlineComment sniff.
     *
     * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Commenting\InlineCommentSniff
     */
    public function testMoodleCommentingInlineComment() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('moodle.Commenting.InlineComment');
        $this->setFixture(__DIR__ . '/fixtures/moodle_comenting_inlinecomment.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            4 => ['3 slashes comments are not allowed'],
            6 => 1,
            8 => 'No space found before comment text',
           28 => 'Inline doc block comments are not allowed; use "// Comment." instead',
           44 => 1,
           73 => 'Perl-style comments are not allowed; use "// Comment." instead',
           78 => '3 slashes comments are not allowed',
           91 => '\'$variable\' does not match next code line \'lets_execute_it...\'',
           94 => 1,
          102 => '\'$cm\' does not match next list() variables @Source: moodle.Commenting.InlineComment.TypeHintingList',
          112 => '\'$cm\' does not match next foreach() as variable @Source: moodle.Commenting.InlineComment.TypeHintingFor',
          118 => 0,
          122 => 1,
          124 => 1,
          126 => 1,
          128 => 1,
          130 => 1,
          134 => 0,
          135 => 0,
          136 => 0,
          137 => 0,
          143 => '@Message: Tab found before comment',
          144 => '@Source: moodle.Commenting.InlineComment.SpacingBefore',
          146 => '@Message: Blank comments are not allowed',
        ]);
        $this->setWarnings([
            4 => 0,
            6 => [null, 'Commenting.InlineComment.InvalidEndChar'],
           55 => ['19 found'],
           57 => ['121 found'],
           59 => ['Found: (no)'],
           61 => 1,
           63 => 1,
           65 => 1,
           67 => 1,
           69 => ['WrongCommentCodeFoundBefore'],
           71 => 3,
           75 => 2,
           77 => 1,
           79 => 1,
          118 => 0,
          122 => 0,
        ]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test the moodle.Commenting.InlineComment sniff.
     *
     * Note that, while this test continues passing, because
     * we load the .js file manually, now the moodle standard
     * by default enforces --extensions=php, so no .js file
     * will be inspected by default ever.
     *
     * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Commenting\InlineCommentSniff
     */
    public function testMoodleCommentingInlineCommentJS() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('moodle.Commenting.InlineComment');
        $this->setFixture(__DIR__ . '/fixtures/moodle_comenting_inlinecomment.js');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            1 => ['3 slashes comments are not allowed'],
            3 => 1,
            5 => 'No space found before comment text',
        ]);
        $this->setWarnings([
            3 => [null, 'Commenting.InlineComment.InvalidEndChar'],
        ]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test the moodle.ControlStructures.ControlSignature sniff.
     *
     * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\ControlStructures\ControlSignatureSniff
     */
    public function testMoodleControlStructuresControlsignature() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('moodle.ControlStructures.ControlSignature');
        $this->setFixture(__DIR__ . '/fixtures/moodle_controlstructures_controlsignature.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            3 => 0,
            4 => ['found "if(...) {'],
            5 => 0,
            6 => '@Message: Expected "} else {\n"',
        ]);
        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test the moodle.Files.LineLength sniff.
     *
     * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Files\LineLengthSniff
     */
    public function testMoodleFilesLineLength() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('moodle.Files.LineLength');
        $this->setFixture(__DIR__ . '/fixtures/moodle_files_linelength.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            21 => 'maximum limit of 180 characters; contains 181 characters',
            22 => 'maximum limit of 180 characters; contains 181 characters',
        ]);
        $this->setWarnings([
            13 => 'exceeds 132 characters; contains 133 characters',
            14 => 'exceeds 132 characters; contains 133 characters',
            17 => 'exceeds 132 characters; contains 180 characters',
            18 => 'exceeds 132 characters; contains 180 characters',
        ]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test the Generic.Arrays.DisallowLongArraySyntax sniff.
     *
     * @covers \PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowLongArraySyntaxSniff
     */
    public function testGenericArraysDisallowLongArraySyntax(): void {
        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('Generic.Arrays.DisallowLongArraySyntax');
        $this->setFixture(__DIR__ . '/fixtures/generic_array_longarraysyntax.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            3 => 'Short array syntax must be used to define arrays @Source: Generic.Arrays.DisallowLongArraySyntax.Found',
            5 => 'Short array syntax must be used to define arrays @Source: Generic.Arrays.DisallowLongArraySyntax.Found',
            9 => 'Short array syntax must be used to define arrays @Source: Generic.Arrays.DisallowLongArraySyntax.Found',
        ]);

        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test the Generic.Files.LineEndings sniff.
     *
     * @covers \PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineEndingsSniff
     */
    public function testGeneriFilesLineEndings() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('Generic.Files.LineEndings');
        $this->setFixture(__DIR__ . '/fixtures/generic_files_lineendings.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            1 => 'line character is invalid; expected "\n" but found "\r\n" @Source: Generic.Files.LineEndings.InvalidEOLChar',
        ]);

        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test the Generic.Files.EndFileNewline sniff.
     *
     * @covers \PHP_CodeSniffer\Standards\Generic\Sniffs\Files\EndFileNewlineSniff
     */
    public function testGenericFilesEndFileNewLine() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('Generic.Files.EndFileNewline');
        $this->setFixture(__DIR__ . '/fixtures/generic_files_endfilenewline.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            4 => 'File must end with a newline character @Source: Generic.Files.EndFileNewline.NotFound',
        ]);

        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test the Generic.WhiteSpace.DisallowTabIndent sniff.
     *
     * @covers \PHP_CodeSniffer\Standards\Generic\Sniffs\Whitespace\DisallowTabIndentSniff
     */
    public function testGenericWhiteSpaceDisalowTabIndent() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('Generic.WhiteSpace.DisallowTabIndent');
        $this->setFixture(__DIR__ . '/fixtures/generic_whitespace_disallowtabindent.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            9 => 'Spaces must be used to indent lines; tabs are not allowed',
           10 => 1,
           11 => 1,
        ]);

        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test the Generic.Functions.OpeningFunctionBraceKernighanRitchie sniff.
     *
     * @covers \PHP_CodeSniffer\Standards\Generic\Sniffs\Functions\OpeningFunctionBraceKernighanRitchieSniff
     */
    public function testGenericFunctionsOpeningFunctionBraceKernighanRitchie() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('Generic.Functions.OpeningFunctionBraceKernighanRitchie');
        $this->setFixture(__DIR__ . '/fixtures/generic_functions_openingfunctionbracekerninghanritchie.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            6 => 'Expected 1 space before opening brace; found 0',
            9 => 1,
           12 => 'Expected 1 space before opening brace; found 3',
           15 => 1,
           20 => 'Expected 1 space before opening brace; found 0',
           23 => 1,
           26 => 'Expected 1 space before opening brace; found 3',
           29 => 1]);

        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test the Generic.Classes.OpeningBraceSameLine sniff.
     *
     * @covers \PHP_CodeSniffer\Standards\Generic\Sniffs\Classes\OpeningBraceSameLineSniff
     */
    public function testGenericClassesOpeningBraceSameLine() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('Generic.Classes.OpeningBraceSameLine');
        $this->setFixture(__DIR__ . '/fixtures/generic_classes_openingclassbrace.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            5 => 'Expected 1 space before opening brace; found 0',
            8 => 'Expected 1 space before opening brace; found 0',
            11 => 'Expected 1 space before opening brace; found 3',
            14 => 'Expected 1 space before opening brace; found 3',
            19 => 'Opening brace should be on the same line as the declaration for class test05',
        ]);

        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test the Generic.WhiteSpace.ScopeIndent sniff.
     *
     * @covers \PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ScopeIndentSniff
     */
    public function testGenericWhiteSpaceScopeIndent() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('Generic.WhiteSpace.ScopeIndent');
        $this->setFixture(__DIR__ . '/fixtures/generic_whitespace_scopeindent.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            7 => 'indented incorrectly; expected at least 4 spaces, found 2 @Source: Generic.WhiteSpace.ScopeIndent.Incorrect',
            19 => 'indented incorrectly; expected at least 4 spaces, found 2 @Source: Generic.WhiteSpace.ScopeIndent.Incorrect',
            44 => 'expected at least 8 spaces',
        ]);
        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test the moodle.PHP.DeprecatedFunctions sniff.
     *
     * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\PHP\DeprecatedFunctionsSniff
     */
    public function testMoodlePHPDeprecatedFunctions() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('moodle.PHP.DeprecatedFunctions');
        $this->setFixture(__DIR__ . '/fixtures/moodle_php_deprecatedfunctions.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([]);
        $warnings = [7 => 'print_error() has been deprecated; use throw new moodle_exception()'];
        if (PHP_VERSION_ID >= 70300 && PHP_VERSION_ID < 80000) {
            $warnings[10] = 'mbsplit() has been deprecated';
        }
        $this->setWarnings($warnings);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test the moodle.PHP.ForbiddenFunctions sniff.
     *
     * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\PHP\ForbiddenFunctionsSniff
     */
    public function testMoodlePHPForbiddenFunctions() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('moodle.PHP.ForbiddenFunctions');
        $this->setFixture(__DIR__ . '/fixtures/moodle_php_forbiddenfunctions.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            5 => 'function sizeof() is forbidden; use count()',
            6 => 1,
            8 => 1,
            9 => 1,
            10 => 1,
            13 => 'function extract() is forbidden',
            14 => 0, // These are eval, goto and got labels handled by {@see moodle_Sniffs_PHP_ForbiddenTokensSniff}.
            15 => 0,
            16 => 0,
            17 => 0,
            ]);
        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test the moodle.PHP.ForbiddenGlobalUse snifff.
     *
     * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\PHP\ForbiddenGlobalUseSniff
     */
    public function testMoodlePHPForbiddenGlobalUse() {
        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('moodle.PHP.ForbiddenGlobalUse');
        $this->setFixture(__DIR__ . '/fixtures/moodle_php_forbidden_global_use.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
                6 => 0,
                7 => 0,
                8 => 0,
                9 => 0,
                10 => 0,
                11 => 0,
                12 => 0,
                13 => 0,
                14 => 0,
                15 => 0,
                16 => 'global $OUTPUT cannot be used in renderers. Use $this->output.',
                17 => 'global $OUTPUT cannot be used in renderers. Use $this->output.',
                18 => 0,
                19 => 0,
                20 => 0,
                21 => 'global $PAGE cannot be used in renderers. Use $this->page.',
                22 => 'global $PAGE cannot be used in renderers. Use $this->page.',
                23 => 0,
                24 => 0,
                25 => 0,
                26 => ['global $OUTPUT cannot be used in renderers. Use $this->output.',
                        'global $PAGE cannot be used in renderers. Use $this->page.'],
                27 => ['global $OUTPUT cannot be used in renderers. Use $this->output.',
                        'global $PAGE cannot be used in renderers. Use $this->page.'],
                28 => 0,
                29 => 0,
                30 => 0,
                31 => 'global $PAGE cannot be used in renderers. Use $this->page.',
                32 => 'global $PAGE cannot be used in renderers. Use $this->page.',
                33 => 0,
                34 => 0,
                35 => 0,
                36 => 0,
                37 => 0,
                38 => 'global $OUTPUT cannot be used in renderers. Use $this->output.',
                39 => 'global $OUTPUT cannot be used in renderers. Use $this->output.',
                40 => 0,
                41 => 0,
                42 => 0,
                43 => 0,
                44 => 0,
                45 => 0,
                46 => 0,
                47 => 0,
                48 => 0,
                49 => 0,
                50 => 0,
                51 => 0,
                52 => 0,
                53 => 0,
                54 => 0,
                55 => 0,
                56 => 'global $PAGE cannot be used in block classes. Use $this->page.',
                57 => 'global $PAGE cannot be used in block classes. Use $this->page.',
                58 => 0,
                59 => 0,
                ]);
        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test the moodle.PHP.ForbiddenTokens sniff.
     *
     * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\PHP\ForbiddenTokensSniff
     */
    public function testMoodlePHPForbiddenTokens() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('moodle.PHP.ForbiddenTokens');
        $this->setFixture(__DIR__ . '/fixtures/moodle_php_forbiddentokens.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            5 => 'The use of function eval() is forbidden',
            6 => 'The use of operator goto is forbidden',
            8 => 'The use of goto labels is forbidden',
            11 => 1,
            13 => ['backticks', 'backticks']]);
        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test the moodle.Strings.ForbiddenStrings sniff.
     *
     * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Strings\ForbiddenStringsSniff
     */
    public function testMoodleStringsForbiddenStrings() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('moodle.Strings.ForbiddenStrings');
        $this->setFixture(__DIR__ . '/fixtures/moodle_strings_forbiddenstrings.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            8 => 'The use of the AS keyword to alias tables is bad for cross-db',
            10 => 1,
            11 => 'The use of the AS keyword to alias tables is bad for cross-db',
            12 => 0,
            // Only if the engine supported /e. Completely removed in PHP 7.3.
            15 => (version_compare(PHP_VERSION, '7.3.0', '<') ?
                'The use of the /e modifier in regular expressions is forbidden' : 0),
            16 => (version_compare(PHP_VERSION, '7.3.0', '<') ? 1 : 0),
            23 => (version_compare(PHP_VERSION, '7.3.0', '<') ? 2 : 1),
            26 => 0,
            27 => 0]);
        $this->setWarnings([
            19 => ['backticks in strings is not recommended'],
            20 => 1,
            23 => 1,
            36 => 'backticks in strings',
            37 => 1]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test external sniff incorporated to moodle standard.
     *
     * @covers \PHPCompatibility\Sniffs\FunctionUse\RemovedFunctionsSniff
     */
    public function testPHPCompatibilityFunctionUseRemovedFunctions() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('PHPCompatibility.FunctionUse.RemovedFunctions');
        $this->setFixture(__DIR__ . '/fixtures/phpcompatibility_php_deprecatedfunctions.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            5 => ['Function ereg_replace', 'Use call_user_func() instead', '@Source: PHPCompat'],
        ]);
        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test call time pass by reference.
     *
     * @covers \PHPCompatibility\Sniffs\Syntax\ForbiddenCallTimePassByReferenceSniff
     */
    public function testPHPCompatibilitySyntaxForbiddenCallTimePassByReference() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('PHPCompatibility.Syntax.ForbiddenCallTimePassByReference');
        $this->setFixture(__DIR__ . '/fixtures/phpcompatibility_php_forbiddencalltimepassbyreference.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            6 => ['call-time pass-by-reference is deprecated'],
            7 => ['@Source: PHPCompat']]);
        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test variable naming standards
     *
     * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\NamingConventions\ValidVariableNameSniff
     */
    public function testMoodleNamingConventionsValidVariableName() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('moodle.NamingConventions.ValidVariableName');
        $this->setFixture(__DIR__ . '/fixtures/moodle_namingconventions_variablename.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            4 => 'must not contain underscores',
            5 => 'must be all lower-case',
            6 => 'must not contain underscores',
            7 => ['must be all lower-case', 'must not contain underscores'],
            8 => 0,
            9 => 0,
            12 => 'must not contain underscores',
            13 => 'must be all lower-case',
            14 => ['must be all lower-case', 'must not contain underscores'],
            15 => 0,
            16 => 0,
            17 => 'The \'var\' keyword is not permitted',
            20 => 'must be all lower-case',
            21 => 2,
            22 => ['must be all lower-case', 'must not contain underscores'],
        ]);
        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test operator spacing standards
     *
     * @covers \PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\OperatorSpacingSniff
     */
    public function testSquizWhiteSpaceOperatorSpacing() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('Squiz.WhiteSpace.OperatorSpacing');
        $this->setFixture(__DIR__ . '/fixtures/squiz_whitespace_operatorspacing.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
                               6 => 0,
                               7 => 'Expected 1 space before',
                               8 => 'Expected 1 space after',
                               9 => ['Expected 1 space before', 'Expected 1 space after'],
                               10 => 0,
                               11 => 'Expected 1 space after "=>"; 3 found',
                               12 => 0,
                               13 => 0,
                               14 => 'Expected 1 space before',
                               15 => 'Expected 1 space after',
                               16 => ['Expected 1 space before', 'Expected 1 space after'],
                               17 => 0,
                               18 => 'Expected 1 space after "="; 2 found',
                               19 => 0,
                               20 => 0,
                               21 => 0,
                               22 => 'Expected 1 space before',
                               23 => 'Expected 1 space after',
                               24 => ['Expected 1 space before', 'Expected 1 space after'],
                               25 => 0,
                               26 => 'Expected 1 space after "+"; 2 found',
                               27 => 'Expected 1 space before "+"; 2 found',
                               28 => 0,
                               29 => 'Expected 1 space before',
                               30 => 'Expected 1 space after',
                               31 => ['Expected 1 space before', 'Expected 1 space after'],
                               32 => 0,
                               33 => 'Expected 1 space after "-"; 2 found',
                               34 => 'Expected 1 space before "-"; 2 found',
                               35 => 0,
                               36 => 'Expected 1 space before',
                               37 => 'Expected 1 space after',
                               38 => ['Expected 1 space before', 'Expected 1 space after'],
                               39 => 0,
                               40 => 'Expected 1 space after "*"; 2 found',
                               41 => 'Expected 1 space before "*"; 2 found',
                               42 => 0,
                               43 => 'Expected 1 space before',
                               44 => 'Expected 1 space after',
                               45 => ['Expected 1 space before', 'Expected 1 space after'],
                               46 => 0,
                               47 => 'Expected 1 space after "/"; 2 found',
                               48 => 'Expected 1 space before "/"; 2 found',
                               49 => 0,
                               50 => 0,
                               51 => 0,
                               52 => 0,
                               53 => 0,
                               54 => 0,
                               55 => 0,
                               56 => 0,
                               57 => 0,
                               58 => 0,
                               59 => 0,
                               60 => 0,
                               61 => 0,
                               62 => 0,
                               63 => 0,
                          ]);
        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test object operator spacing standards
     *
     * @covers \PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\ObjectOperatorSpacingSniff
     */
    public function testSquizWhiteSpaceObjectOperatorSpacing() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('Squiz.WhiteSpace.ObjectOperatorSpacing');
        $this->setFixture(__DIR__ . '/fixtures/squiz_whitespace_objectoperatorspacing.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([]);
        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test object operator indentation standards
     *
     * @covers \PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ObjectOperatorIndentSniff
     */
    public function testPEARWhiteSpaceObjectOperatorIndent() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('PEAR.WhiteSpace.ObjectOperatorIndent');
        $this->setFixture(__DIR__ . '/fixtures/pear_whitespace_objectoperatorspacing.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([
            40 => 'not indented correctly; expected 4 spaces but found 2',
            41 => '@Source: PEAR.WhiteSpace.ObjectOperatorIndent.Incorrect',
            44 => 1,
            45 => 1,
            49 => 'not indented correctly; expected 4 spaces but found 6',
            50 => '@Source: PEAR.WhiteSpace.ObjectOperatorIndent.Incorrect',
            53 => 1,
            54 => 1,
            61 => 1,
            62 => 1,
            65 => 1,
            66 => 1,
            69 => 1,
            70 => 1,
        ]);
        $this->setWarnings([]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test variable naming standards
     *
     * @covers \PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\CommentedOutCodeSniff
     */
    public function testSquizPHPCommentedOutCode() {

        // Define the standard, sniff and fixture to use.
        $this->setStandard('moodle');
        $this->setSniff('Squiz.PHP.CommentedOutCode');
        $this->setFixture(__DIR__ . '/fixtures/squiz_php_commentedoutcode.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->setErrors([]);
        $this->setWarnings([
            5 => 'This comment is 72% valid code; is this commented out code',
            9 => '@Source: Squiz.PHP.CommentedOutCode.Found',
        ]);

        // Let's do all the hard work!
        $this->verifyCsResults();
    }

    /**
     * Test the moodle.Files.RequireLogin sniff detects problems.
     *
     * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Files\RequireLoginSniff
     */
    public function testMoodleFilesRequireLoginProblem() {
        $this->setStandard('moodle');
        $this->setSniff('moodle.Files.RequireLogin');
        $this->setFixture(__DIR__ . '/fixtures/moodle_files_requirelogin/problem.php');

        $this->setErrors([]);
        $this->setWarnings([
            25 => ', require_course_login, require_admin, admin_externalpage_setup) following config inclusion. None found',
        ]);

        $this->verifyCsResults();
    }

    /**
     * Test the moodle.Files.RequireLogin sniff with login.
     *
     * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Files\RequireLoginSniff
     */
    public function testMoodleFilesRequireLoginOk() {
        $this->setStandard('moodle');
        $this->setSniff('moodle.Files.RequireLogin');
        $this->setFixture(__DIR__ . '/fixtures/moodle_files_requirelogin/require_login_ok.php');

        $this->setErrors([]);
        $this->setWarnings([]);

        $this->verifyCsResults();
    }

    /**
     * Test the moodle.Files.RequireLogin sniff with require_course_login().
     *
     * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Files\RequireLoginSniff
     */
    public function testMoodleFilesRequireLoginCourseOk() {
        $this->setStandard('moodle');
        $this->setSniff('moodle.Files.RequireLogin');
        $this->setFixture(__DIR__ . '/fixtures/moodle_files_requirelogin/require_course_login_ok.php');

        $this->setErrors([]);
        $this->setWarnings([]);

        $this->verifyCsResults();
    }

    /**
     * Test the moodle.Files.RequireLogin sniff with external page setup.
     *
     * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Files\RequireLoginSniff
     */
    public function testMoodleFilesRequireLoginAdminExtenalPageOk() {
        $this->setStandard('moodle');
        $this->setSniff('moodle.Files.RequireLogin');
        $this->setFixture(__DIR__ . '/fixtures/moodle_files_requirelogin/admin_externalpage_setup_ok.php');

        $this->setErrors([]);
        $this->setWarnings([]);

        $this->verifyCsResults();
    }

    /**
     * Test the moodle.Files.RequireLogin in a CLI script.
     *
     * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Files\RequireLoginSniff
     */
    public function testMoodleFilesRequireLoginCLIScriptOk() {
        $this->setStandard('moodle');
        $this->setSniff('moodle.Files.RequireLogin');
        $this->setFixture(__DIR__ . '/fixtures/moodle_files_requirelogin/cliscript_ok.php');

        $this->setErrors([]);
        $this->setWarnings([]);

        $this->verifyCsResults();
    }

    /**
     * Test the moodle.Files.RequireLogin sniff in a no moodle cookies script.
     *
     * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Files\RequireLoginSniff
     */
    public function testMoodleFilesRequireLoginNoMoodlCookiesOk() {
        $this->setStandard('moodle');
        $this->setSniff('moodle.Files.RequireLogin');
        $this->setFixture(__DIR__ . '/fixtures/moodle_files_requirelogin/nomoodlecookies_ok.php');

        $this->setErrors([]);
        $this->setWarnings([]);

        $this->verifyCsResults();
    }
}
