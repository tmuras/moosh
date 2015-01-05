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
 * This file contains the test cases covering the "moodle" standard.
 *
 * @package    local_codechecker
 * @subpackage phpunit
 * @category   phpunit
 * @copyright  2013 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../tests/local_codechecker_testcase.php');

/**
 * PHP CS moodle standard test cases.
 *
 * Each case covers one sniff. Self-explanatory
 *
 * @todo Complete coverage of all Sniffs.
 */
class moodlestandard_testcase extends local_codechecker_testcase {

    public function test_moodle_comenting_inlinecomment() {

        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Commenting.InlineComment');
        $this->set_fixture(__DIR__ . '/fixtures/moodle_comenting_inlinecomment.php');

        // Define expected results (errors and warnings). Format, array of:
        //   - line => number of problems,  or
        //   - line => array of contents for message / source problem matching.
        //   - line => string of contents for message / source problem matching (only 1).
        $this->set_errors(array(
            4 => array('3 slashes comments are not allowed'),
            6 => 1,
            8 => 'No space before comment text',
           28 => 1,
           44 => 1));
        $this->set_warnings(array(
            4 => 0,
            6 => array(null, 'Commenting.InlineComment.InvalidEndChar'),
           55 => array('19 found'),
           57 => array('121 found'),
           59 => array('Found: (no)'),
           61 => 1,
           63 => 1,
           65 => 1,
           67 => 1,
           69 => array('WrongCommentCodeFoundBefore'),
           71 => 3));

        // Let's do all the hard work!
        $this->verify_cs_results();
    }

    public function test_moodle_controlstructures_controlsignature() {

        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('moodle.ControlStructures.ControlSignature');
        $this->set_fixture(__DIR__ . '/fixtures/moodle_controlstructures_controlsignature.php');

        // Define expected results (errors and warnings). Format, array of:
        //   - line => number of problems,  or
        //   - line => array of contents for message / source problem matching.
        //   - line => string of contents for message / source problem matching (only 1).
        $this->set_errors(array(
            3 => 0,
            4 => array('found "if(...) {'),
            5 => 0,
            6 => '@Message: Expected "} else {\n"'));
        $this->set_warnings(array());

        // Let's do all the hard work!
        $this->verify_cs_results();
    }

    public function test_moodle_whitespace_scopeindent() {

        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('moodle.WhiteSpace.ScopeIndent');
        $this->set_fixture(__DIR__ . '/fixtures/moodle_whitespace_scopeindent.php');

        // Define expected results (errors and warnings). Format, array of:
        //   - line => number of problems,  or
        //   - line => array of contents for message / source problem matching.
        //   - line => string of contents for message / source problem matching (only 1).
        $this->set_errors(array(
            6 => 'indented incorrectly; expected at least 4 spaces, found 2 @Source: moodle.WhiteSpace.ScopeIndent.Incorrect',
            18 => 'indented incorrectly; expected at least 4 spaces, found 2 @Source: moodle.WhiteSpace.ScopeIndent.Incorrect'));
        $this->set_warnings(array());

        // Let's do all the hard work!
        $this->verify_cs_results();
    }

    public function test_moodle_php_forbiddenfunctions() {

        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('moodle.PHP.ForbiddenFunctions');
        $this->set_fixture(__DIR__ . '/fixtures/moodle_php_forbiddenfunctions.php');

        // Define expected results (errors and warnings). Format, array of:
        //   - line => number of problems,  or
        //   - line => array of contents for message / source problem matching.
        //   - line => string of contents for message / source problem matching (only 1).
        $this->set_errors(array(
            5 => 'function sizeof() is forbidden; use count()',
            6 => 1,
            8 => 1,
            9 => 1,
            10 => 1,
            13 => 'function extract() is forbidden',
            14 => 0, // These are eval, goto and got labels handled by {@link moodle_Sniffs_PHP_ForbiddenTokensSniff}.
            15 => 0,
            16 => 0,
            17 => 0,
            ));
        $this->set_warnings(array());

        // Let's do all the hard work!
        $this->verify_cs_results();
    }

    public function test_moodle_php_forbiddennamesasinvokedfunctions() {

        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('moodle.PHP.ForbiddenNamesAsInvokedFunctions');
        $this->set_fixture(__DIR__ . '/fixtures/moodle_php_forbiddennamesasinvokedfunctions.php');

        // Define expected results (errors and warnings). Format, array of:
        //   - line => number of problems,  or
        //   - line => array of contents for message / source problem matching.
        //   - line => string of contents for message / source problem matching (only 1).
        $this->set_errors(array(
            5 => 0, // These are allowed as invoked functions in Moodle for now.
            6 => 0,
            7 => 0,
            8 => 0,
            9 => 0,
            12 => 0,
            13 => 0,
            14 => 0,
            15 => 0,
            16 => 0,
            19 => 1, // These are not allowed as invoked functions.
            20 => 'T_CALLABLE',
            21 => 'is a reserved keyword introduced in PHP version 5.0',
            22 => 'T_FINAL',
            23 => 1,
            24 => 1,
            25 => 1,
            26 => 1));
        $this->set_warnings(array());

        // Let's do all the hard work!
        $this->verify_cs_results();
    }

    public function test_moodle_php_forbiddentokens() {

        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('moodle.PHP.ForbiddenTokens');
        $this->set_fixture(__DIR__ . '/fixtures/moodle_php_forbiddentokens.php');

        // Define expected results (errors and warnings). Format, array of:
        //   - line => number of problems,  or
        //   - line => array of contents for message / source problem matching.
        //   - line => string of contents for message / source problem matching (only 1).
        $this->set_errors(array(
            5 => 'The use of function eval() is forbidden',
            6 => 'The use of operator goto is forbidden',
            8 => 'The use of goto labels is forbidden',
            11 => 1,
            13 => array('backticks', 'backticks')));
        $this->set_warnings(array());

        // Let's do all the hard work!
        $this->verify_cs_results();
    }

    public function test_moodle_strings_forbiddenstrings() {

        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Strings.ForbiddenStrings');
        $this->set_fixture(__DIR__ . '/fixtures/moodle_strings_forbiddenstrings.php');

        // Define expected results (errors and warnings). Format, array of:
        //   - line => number of problems,  or
        //   - line => array of contents for message / source problem matching.
        //   - line => string of contents for message / source problem matching (only 1).
        $this->set_errors(array(
            8 => 'The use of the AS keyword to alias tables is bad for cross-db',
            10 => 1,
            11 => 'The use of the AS keyword to alias tables is bad for cross-db',
            12 => 0,
            15 => 'The use of the /e modifier in regular expressions is forbidden',
            16 => 1,
            23 => 2,
            26 => 0,
            27 => 0));
        $this->set_warnings(array(
            19 => array('backticks in strings is not recommended'),
            20 => 1,
            23 => 1));

        // Let's do all the hard work!
        $this->verify_cs_results();
    }

    /**
     * Test external sniff incorporated to moodle standard.
     */
    public function test_PHPCompatibility_php_deprecatedfunctions() {

        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('PHPCompatibility.PHP.DeprecatedFunctions');
        $this->set_fixture(__DIR__ . '/fixtures/PHPCompatibility_php_deprecatedfunctions.php');

        // Define expected results (errors and warnings). Format, array of:
        //   - line => number of problems,  or
        //   - line => array of contents for message / source problem matching.
        //   - line => string of contents for message / source problem matching (only 1).
        $this->set_errors(array());
        $this->set_warnings(array(
            5 => array('function ereg_replace', 'use call_user_func instead', '@Source: PHPCompat')));

        // Let's do all the hard work!
        $this->verify_cs_results();
    }

    /**
     * Test call time pass by reference.
     */
    public function test_PHPCompatibility_php_forbiddencalltimepassbyreference() {

        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('PHPCompatibility.PHP.ForbiddenCallTimePassByReference');
        $this->set_fixture(__DIR__ . '/fixtures/PHPCompatibility_php_forbiddencalltimepassbyreference.php');

        // Define expected results (errors and warnings). Format, array of:
        //   - line => number of problems,  or
        //   - line => array of contents for message / source problem matching.
        //   - line => string of contents for message / source problem matching (only 1).
        $this->set_errors(array(
            6 => array('call-time pass-by-reference is prohibited'),
            7 => array('@Source: PHPCompat')));
        $this->set_warnings(array());

        // Let's do all the hard work!
        $this->verify_cs_results();
    }

    /**
     * Test variable naming standards
     */
    public function test_moodle_namingconventions_variablename() {

        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('moodle.NamingConventions.ValidVariableName');
        $this->set_fixture(__DIR__ . '/fixtures/moodle_namingconventions_variablename.php');

        // Define expected results (errors and warnings). Format, array of:
        //   - line => number of problems,  or
        //   - line => array of contents for message / source problem matching.
        //   - line => string of contents for message / source problem matching (only 1).
        $this->set_errors(array(
            4 => 'must not contain underscores',
            5 => 'must be all lower-case',
            6 => 'must not contain underscores',
            7 => array('must be all lower-case', 'must not contain underscores'),
            8 => 0,
            9 => 0,
            12 => 'must not contain underscores',
            13 => 'must be all lower-case',
            14 => array('must be all lower-case', 'must not contain underscores'),
            15 => 0,
            16 => 0,
            17 => 'The \'var\' keyword is not permitted',
            20 => 'must be all lower-case',
            21 => 'must not contain underscores',
            22 => array('must be all lower-case', 'must not contain underscores'),
        ));
        $this->set_warnings(array());

        // Let's do all the hard work!
        $this->verify_cs_results();
    }

    /**
     * Test operator spacing standards
     */
    public function test_moodle_operator_spacing() {

        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('Squiz.WhiteSpace.OperatorSpacing');
        $this->set_fixture(__DIR__ . '/fixtures/squiz_whitespace_operatorspacing.php');

        // Define expected results (errors and warnings). Format, array of:
        //   - line => number of problems,  or
        //   - line => array of contents for message / source problem matching.
        //   - line => string of contents for message / source problem matching (only 1).
        $this->set_errors(array(
                               5 => 0,
                               6 => 'Expected 1 space before',
                               7 => 'Expected 1 space after',
                               8 => array('Expected 1 space before', 'Expected 1 space after'),
                               9 => 0,
                               10 => 'Expected 1 space after "=>"; 3 found',
                               11 => 0,
                               12 => 0,
                               13 => 'Expected 1 space before',
                               14 => 'Expected 1 space after',
                               15 => array('Expected 1 space before', 'Expected 1 space after'),
                               16 => 0,
                               17 => 'Expected 1 space after "="; 2 found',
                               18 => 0,
                               19 => 0,
                               20 => 0,
                               21 => 'Expected 1 space before',
                               22 => 'Expected 1 space after',
                               23 => array('Expected 1 space before', 'Expected 1 space after'),
                               24 => 0,
                               25 => 'Expected 1 space after "+"; 2 found',
                               26 => 'Expected 1 space before "+"; 2 found',
                               27 => 0,
                               28 => 'Expected 1 space before',
                               29 => 'Expected 1 space after',
                               30 => array('Expected 1 space before', 'Expected 1 space after'),
                               31 => 0,
                               32 => 'Expected 1 space after "-"; 2 found',
                               33 => 'Expected 1 space before "-"; 2 found',
                               34 => 0,
                               35 => 'Expected 1 space before',
                               36 => 'Expected 1 space after',
                               37 => array('Expected 1 space before', 'Expected 1 space after'),
                               38 => 0,
                               39 => 'Expected 1 space after "*"; 2 found',
                               40 => 'Expected 1 space before "*"; 2 found',
                               41 => 0,
                               42 => 'Expected 1 space before',
                               43 => 'Expected 1 space after',
                               44 => array('Expected 1 space before', 'Expected 1 space after'),
                               45 => 0,
                               46 => 'Expected 1 space after "/"; 2 found',
                               47 => 'Expected 1 space before "/"; 2 found',

                          ));
        $this->set_warnings(array());

        // Let's do all the hard work!
        $this->verify_cs_results();
    }
}
