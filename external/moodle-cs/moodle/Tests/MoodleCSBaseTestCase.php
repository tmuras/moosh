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

use PHP_CodeSniffer\Config;

/**
 * Specialized test case for easy testing of "moodle" standard sniffs.
 *
 * If you want to run the tests for the Moodle sniffs, you need to
 * use the specific command-line:
 *     vendor/bin/phpunit local/codechecker/moodle/tests/moodlestandard_test.php
 * no tests for this plugin are run as part of a full Moodle PHPunit run.
 * (This may be a bug?)
 *
 * This class mimics {@see AbstractSniffUnitTest} way to test Sniffs
 * allowing easy process of examples and assertion of result expectations.
 *
 * Should work for any Sniff part of a given standard (custom or core).
 *
 * Note extension & overriding was impossible because of some "final" stuff.
 *
 * This file contains helper testcase for testing "moodle" CS Sniffs.
 *
 * @copyright  2013 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class MoodleCSBaseTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string|null name of the standard to be tested.
     */
    protected ?string $standard = null;

    /**
     * @var string|null code of the sniff to be tested. Must be part of the standard definition.
     *             See {@see ::set_sniff()} for more information.
     */
    protected ?string $sniff = null;

    /**
     * @var string|null full path to the file used as input (fixture).
     */
    protected ?string $fixture = null;

    /** @var string|null A path name to mock for the fixture */
    protected ?string $fixtureFileName = null;

    /**
     * @var array custom config elements to setup before running phpcs. name => value.
     */
    protected array $customConfigs = [];

    /**
     * @var array|null error expectations to ve verified against execution results.
     */
    protected ?array $errors = null;

    /**
     * @var array|null warning expectations to ve verified against execution results.
     */
    protected ?array $warnings = null;

    /**
     * Code to be executed after each test case (method) is run.
     *
     * In charge of resetting all the internal properties and removing any
     * custom config option or mockup mapping.
     */
    protected function tearDown(): void {
        // Reset all the internal properties.
        $this->standard = null;
        $this->sniff = null;
        $this->errors = null;
        $this->warnings = null;
        $this->fixture = null;
        $this->fixtureFileName = null;
        // Reset any mocked component mappings.
        \MoodleHQ\MoodleCS\moodle\Util\MoodleUtil::setMockedComponentMappings([]);
        // If there is any custom config setup, remove it.
        foreach (array_keys($this->customConfigs) as $key) {
            Config::setConfigData($key, null, true);
        }
        // Call to parent, always.
        parent::tearDown();
    }

    public function setComponentMapping(array $mapping): void {
        \MoodleHQ\MoodleCS\moodle\Util\MoodleUtil::setMockedComponentMappings($mapping);
    }

    public function setApiMappings(array $mapping): void {
        \MoodleHQ\MoodleCS\moodle\Util\MoodleUtil::setMockedApiMappings($mapping);
    }

    /**
     * Set the name of the standard to be tested.
     *
     * @param string $standard name of the standard to be tested.
     */
    protected function setStandard(string $standard) {
        if (\PHP_CodeSniffer\Util\Standards::isInstalledStandard($standard) === false) {
            // They didn't select a valid coding standard, so help them
            // out by letting them know which standards are installed.
            $error = "ERROR: the '{$standard}' coding standard is not installed.\n";
            ob_start();
            \PHP_CodeSniffer\Util\Standards::printInstalledStandards();
            $error .= ob_get_contents();
            ob_end_clean();
            throw new \PHP_CodeSniffer\Exceptions\DeepExitException($error, 3);
        }
        $this->standard = $standard;
    }

    /**
     * Set the name of the sniff to be tested.
     *
     * @param string $sniff code of the sniff to be tested. Must be part of the standard definition.
     *                      Since CodeSniffer 1.5 they are not the Sniff (class) names anymore but
     *                      the called Sniff "code" that is a 3 elements, dot separated, structure
     *                      with format: standard.group.name. Examples:
     *                        - Generic.PHP.LowerCaseConstant
     *                        - moodle.Commenting.InlineComment
     *                        - PEAR.WhiteSpace.ScopeIndent
     */
    protected function setSniff($sniff) {
        $this->sniff = $sniff;
    }

    /**
     * Set the full path to the file used as input.
     *
     * @param string $fixture full path to the file used as input (fixture).
     * @param string|null $fileName A path name to mock for the fixture. If not specified, the fixture filepath is used.
     */
    protected function setFixture(
        string $fixture,
        ?string $fileName = null
    ) {
        if (!is_readable($fixture)) {
            $this->fail('Unreadable fixture passed: ' . $fixture);
        }
        $this->fixture = $fixture;
        $this->fixtureFileName = $fileName;
    }

    /**
     * Set the error expectations to ve verified against execution results.
     *
     * @param array $errors error expectations to ve verified against execution results.
     */
    protected function setErrors(array $errors) {
        $this->errors = $errors;
        // Let's normalize numeric, empty and string errors.
        foreach ($this->errors as $line => $errordef) {
            if (is_int($errordef) && $errordef > 0) {
                $this->errors[$line] = array_fill(0, $errordef, $errordef);
            } elseif (empty($errordef)) {
                $this->errors[$line] = [];
            } elseif (is_string($errordef)) {
                $this->errors[$line] = [$errordef];
            }
        }
    }

    /**
     * Set the warning expectations to ve verified against execution results.
     *
     * @param array $warnings warning expectations to ve verified against execution results.
     */
    protected function setWarnings(array $warnings) {
        $this->warnings = $warnings;
        // Let's normalize numeric, empty and string warnings.
        foreach ($this->warnings as $line => $warningdef) {
            if (is_int($warningdef) && $warningdef > 0) {
                $this->warnings[$line] = array_fill(0, $warningdef, $warningdef);
            } elseif (empty($warningdef)) {
                $this->warnings[$line] = [];
            } elseif (is_string($warningdef)) {
                $this->warnings[$line] = [$warningdef];
            }
        }
    }

    /**
     * Run the CS and verify all the expected errors and warnings.
     *
     * This method must be called after defining everything (the standard,
     * the sniff, the fixture and the error and warning expectations). Then,
     * the CS is called and finally its results are tested against the
     * defined expectations.
     */
    protected function verifyCsResults() {
        $config = new \PHP_CodeSniffer\Config();
        $config->cache     = false;
        $config->standards = [$this->standard];
        $config->sniffs    = [$this->sniff];
        $config->ignored   = [];
        $ruleset = new \PHP_CodeSniffer\Ruleset($config);

        // We don't accept undefined errors and warnings.
        if (is_null($this->errors) && is_null($this->warnings)) {
            $this->fail('Error and warning expectations undefined. You must define at least one.');
        }

        // Let's process the fixture.
        try {
            if ($this->fixtureFileName !== null) {
                $fixtureFilename = $this->fixtureFileName;
                if (DIRECTORY_SEPARATOR !== '/') {
                    $fixtureFilename = str_replace('/', DIRECTORY_SEPARATOR, $fixtureFilename);
                }
                $fixtureSource = file_get_contents($this->fixture);
                $fixtureContent = <<<EOF
                phpcs_input_file: {$fixtureFilename}
                {$fixtureSource}
                EOF;
                $phpcsfile = new \PHP_CodeSniffer\Files\DummyFile($fixtureContent, $ruleset, $config);
            } else {
                $phpcsfile = new \PHP_CodeSniffer\Files\LocalFile($this->fixture, $ruleset, $config);
            }
            $phpcsfile->process();
        } catch (\Exception $e) {
            $this->fail('An unexpected exception has been caught: ' . $e->getMessage());
        }

        // Capture results.
        if (empty($phpcsfile) === true) {
            $this->markTestSkipped();
        }

        // Let's compare expected errors with returned ones.
        $this->verifyErrors($phpcsfile->getErrors());
        $this->verifyWarnings($phpcsfile->getWarnings());

        $fixerrors = [];
        // Let's see if the file has fixable problems and if they become really fixed.
        if ($phpcsfile->getFixableCount() > 0) {
            $phpcsfile->fixer->fixFile();
            // If there are remaining fixable cases, this is a fix problem.
            $tofix = $phpcsfile->getFixableCount();
            if ($tofix > 0) {
                $fixerrors[] = "Failed to fix $tofix fixable problems in $this->fixture";
            }
        }

        // Now, if there is a file, with the same name than the
        // fixture + .fix, use it to verify that the fixed does its job too.)
        if ($this->fixture !== null && is_readable($this->fixture . '.fixed')) {
            $diff = $phpcsfile->fixer->generateDiff($this->fixture . '.fixed');
            if (trim($diff) !== '') {
                $filename = basename($this->fixture);
                $fixedfilename = basename($this->fixture . '.fixed');
                $fixerrors[] = "Fixed version of $filename does not match expected version in $fixedfilename; the diff is\n$diff";
            }
        }

        // Any fix problem detected, report it.
        if (empty($fixerrors) === false) {
            $this->fail(implode(PHP_EOL, $fixerrors));
        }
    }

    /**
     * Helper to skip tests where a real Moodle Root is required.
     */
    protected function requireRealMoodleRoot(): void {
        $moodleRoot = \MoodleHQ\MoodleCS\moodle\Util\MoodleUtil::getMoodleRoot();
        if (!empty($moodleRoot)) {
            return;
        }

        $this->markTestSkipped("Unable to complete test. No Moodle Root specified.");
    }

    /**
     * Adds a custom config element to be setup before running phpcs.
     *
     * Note that those config elements will be automatically removed
     * after each test case (by tearDown())
     *
     * @param string $key config key or name.
     * @param string $value config value.
     */
    protected function addCustomConfig(string $key, string $value): void {
        $this->customConfigs[$key] = $value;
        Config::setConfigData($key, $value, true);
    }

    /**
     * Normalize result errors and verify them against error expectations.
     *
     * @param array $errors error results produced by the CS execution.
     */
    private function verifyErrors($errors) {
        if (!is_array($errors)) {
            $this->fail('Unexpected errors structure received from CS execution.');
        }
        $errors = $this->normalizeCsResults($errors);
        $this->assertResults($this->errors, $errors, 'errors');
    }

    /**
     * Normalize result warnings and verify them against warning expectations.
     *
     * @param array $warnings warning results produced by the CS execution
     */
    private function verifyWarnings($warnings) {
        if (!is_array($warnings)) {
            $this->fail('Unexpected warnings structure received from CS execution.');
        }
        $warnings = $this->normalizeCsResults($warnings);
        $this->assertResults($this->warnings, $warnings, 'warnings');
    }

    /**
     * Perform all the assertions needed to verify results math expectations.
     *
     * @param array $expectations error|warning defined expectations
     * @param array $results error|warning generated results.
     * @param string $type results being asserted (errors, warnings). Used for output only.
     */
    private function assertResults($expectations, $results, $type) {
        foreach ($expectations as $line => $expectation) {
            // Build some information to be shown in case of problems.
            $info = '';
            if (count($expectation)) {
                $info .= PHP_EOL . 'Expected: ' . json_encode($expectation);
            }
            $countresults = isset($results[$line]) ? count($results[$line]) : 0;
            if ($countresults) {
                $info .= PHP_EOL . 'Actual: ' . json_encode($results[$line]);
            }
            // Verify counts for a line are the same.
            $this->assertSame(
                count($expectation),
                $countresults,
                'Failed number of ' . $type . ' for line ' . $line . '.' . $info
            );
            // Now verify every expectation requiring matching.
            foreach ($expectation as $key => $expectedcontent) {
                if (is_string($expectedcontent)) {
                    $this->assertStringContainsString(
                        $expectedcontent,
                        $results[$line][$key],
                        'Failed contents matching of ' . $type . ' for element ' . ($key + 1) . ' of line ' . $line . '.'
                    );
                }
            }
            // Delete this line from results.
            unset($results[$line]);
        }
        // Ended looping, verify there aren't remaining results (errors, warnings).
        $this->assertSame(
            [],
            $results,
            'Failed to verify that all the ' . $type . ' have been defined by expectations.'
        );
    }

    /**
     * Transforms the raw results from CS into a simpler array structure.
     *
     * The raw results are a more complex structure of nested arrays, with
     * information that we don't need. This method transforms that structure
     * into a simpler alternative, for easier asserts against the expectations.
     *
     * @param array $results raw CS results (errors or warnings),
     * @return array normalized array.
     */
    private function normalizeCsResults($results) {
        $normalized = [];
        foreach ($results as $line => $lineerrors) {
            foreach ($lineerrors as $errors) {
                foreach ($errors as $error) {
                    if (isset($normalized[$line])) {
                        $normalized[$line][] = '@Message: ' . $error['message'] . ' @Source: ' . $error['source'];
                    } else {
                        $normalized[$line] = ['@Message: ' . $error['message'] . ' @Source: ' . $error['source']];
                    }
                }
            }
        }
        return $normalized;
    }
}
