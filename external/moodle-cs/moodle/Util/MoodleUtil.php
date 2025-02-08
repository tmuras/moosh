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

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Exceptions\DeepExitException;
use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Ruleset;

/**
 * Various utility methods specific to Moodle stuff.
 *
 * @copyright  2021 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class MoodleUtil
{
    /**
     * @var string Absolute path, cached, containing moodle root detected directory.
     */
    protected static $moodleRoot = false;

    /**
     * @var int Branch, cached, containing moodle detected numeric branch.
     */
    protected static $moodleBranch = false;

    /**
     * @var array Associative array, cached, of components as keys and paths as values.
     */
    protected static $moodleComponents = [];

    /** @var array A list of mocked component mappings for use in unit tests */
    protected static $mockedComponentMappings = [];

    /** @var array A cached list of APIs */
    protected static $apis = [];

    /** @var array A list of mocked API mappings for use in unit tests */
    protected static $mockedApisList = [];

    /**
     * Mock component mappings for unit tests.
     *
     * @param array $mappings List of file path => component mappings
     *
     * @throws \Exception
     */
    public static function setMockedComponentMappings(array $mappings): void {
        if (!defined('PHPUNIT_TEST') || !PHPUNIT_TEST) {
            throw new \Exception('Not running in a unit test'); // @codeCoverageIgnore
        }

        self::$mockedComponentMappings = $mappings;
    }

    /**
     * Mock API mappings for unit tests.
     *
     * @param array $mappings
     * @throws \Exception
     */
    public static function setMockedApiMappings(array $mappings): void {
        if (!defined('PHPUNIT_TEST') || !PHPUNIT_TEST) {
            throw new \Exception('Not running in a unit test'); // @codeCoverageIgnore
        }

        self::$mockedApisList = $mappings;
    }

    /**
     * Load moodle core_component without needing an installed site.
     *
     * @param string $moodleRoot Full path to a valid moodle.root
     * @return bool True if the file has been loaded, false if not.
     */
    protected static function loadCoreComponent(string $moodleRoot): bool {
        global $CFG;

        // Safety check, in case core_component is missing.
        if (!file_exists($moodleRoot . '/lib/classes/component.php')) {
            return false;
        }

        // Some of these (rarely) may be not defined. Ensure they are.
        defined('IGNORE_COMPONENT_CACHE') ?: define('IGNORE_COMPONENT_CACHE', 1);
        defined('MOODLE_INTERNAL') ?: define('MOODLE_INTERNAL', 1);

        // Let's define CFG from scratch (it's not defined ever, because moodle-cs is not a Moodle plugin at all
        $CFG = (object) [
            'dirroot' => $moodleRoot,
            'libdir' => "{$moodleRoot}/lib",
            'admin' => 'admin',
        ];

        require_once($CFG->dirroot . '/lib/classes/component.php'); // Load the class.

        return true;
    }

    /**
     * Calculate all the components installed in a site.
     *
     * @param string $moodleRoot Full path to a valid moodle.root
     * @return array Associative array of components as keys and paths as values or null if not found.
     */
    protected static function calculateAllComponents(string $moodleRoot): ?array {
        // If we have calculated the components already, straight return them.
        if (!empty(self::$moodleComponents)) {
            return self::$moodleComponents;
        }

        // We haven't the components yet, let's calculate all them.

        // First, try to get it from configuration/runtime option.
        // This accepts the full path to a file like the one generated
        // by moodle-local_ci/list_valid_components, which format is:
        // [plugin|subsystem],component_name,component_full_path.
        // Useful to load them when not all the code base is available
        // like it happens with CiBoT runs, for example.
        if ($componentsFile = Config::getConfigData('moodleComponentsListPath')) {
            if (!is_readable($componentsFile)) {
                throw new DeepExitException(
                    "ERROR: Incorrect 'moodleComponentsListPath' config/runtime option. File not found: '$componentsFile'",
                    3
                );
            }
            // Go processing the file.
            $handle = fopen($componentsFile, "r");
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $aline = explode(',', trim($line));
                    // Exclude any line not starting by plugin|sybsystem.
                    if ($aline[0] !== 'plugin' && $aline[0] !== 'subsystem') {
                        continue;
                    }
                    // Exclude any component not being valid one.
                    if (!preg_match('/^[a-z][a-z0-9]*(_[a-z][a-z0-9_]*)?[a-z0-9]+$/', $aline[1])) {
                        continue;
                    }
                    // Exclude any path not being under Mooddle dirroot.
                    if (strpos($aline[2], $moodleRoot) !== 0) {
                        continue;
                    }
                    // Arrived here, it's a valid line, annotate the component.
                    self::$moodleComponents[$aline[1]] = $aline[2];
                }
                fclose($handle);
            }
            // Let's sort the array in ascending order, so more specific matches first.
            arsort(self::$moodleComponents);

            return self::$moodleComponents;
        }

        // Let's try to get the components from core.

        // Verify that core_component class is already available.
        // Make an exception for PHPUnit runs, to be able to test everything
        // because within tests it's always available and never invoked.
        if (!class_exists('\core_component') || (defined('PHPUNIT_TEST') && PHPUNIT_TEST)) {
            if (!self::loadCoreComponent($moodleRoot)) {
                return null;
            }
        }

        // Get all the plugins and subplugin types.
        $types = \core_component::get_plugin_types();
        // Sort types in reverse order, so we get subplugins earlier than plugins.
        $types = array_reverse($types);
        // For each type, get their available implementations.
        foreach ($types as $type => $fullpath) {
            $plugins = \core_component::get_plugin_list($type);
            // For each plugin, let's calculate the proper component name and output it.
            foreach ($plugins as $plugin => $pluginpath) {
                $component = $type . '_' . $plugin;
                self::$moodleComponents[$component] = $pluginpath;
            }
        }

        // Get all the subsystems.
        $subsystems = \core_component::get_core_subsystems();
        $subsystems['core'] = $moodleRoot . '/lib'; // To get core for everything under /lib.
        foreach ($subsystems as $subsystem => $subsystempath) {
            if ($subsystem == 'backup') { // Because I want, yes :-P.
                $subsystempath = $moodleRoot . '/backup';
            }
            // All subsystems are core_ prefixed.
            $component = 'core_' . $subsystem;
            if ($subsystem === 'core') { // But core.
                $component = 'core';
            }
            self::$moodleComponents[$component] = $subsystempath;
        }
        // Let's sort the array in ascending order, so more specific matches first.
        arsort(self::$moodleComponents);

        return self::$moodleComponents;
    }

    /**
     * Try to guess the moodle component for a file
     *
     * This method will return, using moodle core_component, the component
     * corresponding to a file, given the file is within a valid moodle tree.
     *
     * @param File $file File that is being checked.
     * @param bool $selfPath Enables the method to also consider self path to search for a valid moodle root.
     *
     * @return string|null a valid moodle component for the file or null if not found.
     */
    public static function getMoodleComponent(File $file, $selfPath = true): ?string {
        if (defined('PHPUNIT_TEST') && PHPUNIT_TEST && !empty(self::$mockedComponentMappings)) {
            $components = self::$mockedComponentMappings; // @codeCoverageIgnore
        } else {
            // Verify that we are able to find a valid moodle root.
            if (!$moodleRoot = self::getMoodleRoot($file, $selfPath)) {
                return null;
            }

            // Load all components, associative array with keys as component and paths as values.
            $components = self::calculateAllComponents($moodleRoot);
            // Have been unable to load components, done.
            if (empty($components)) {
                return null;
            }
        }

        $filepath = MoodleUtil::getStandardisedFilename($file);
        // Let's find the first component that matches the file path.
        foreach ($components as $component => $componentPath) {
            // Only components with path.
            if (empty($componentPath)) {
                continue;
            }

            // Look for component paths matching the file path.
            $componentPath = str_replace('\\', '/', $componentPath . DIRECTORY_SEPARATOR);
            if (strpos($filepath, $componentPath) === 0) {
                // First match found should be the better one always. We are done.
                return $component;
            }
        }

        // Not found.
        return null;
    }

    /**
     * Get the list of Moodle APIs.
     *
     * @param File $file
     * @param bool $selfPath
     * @return null|array
     */
    public static function getMoodleApis(File $file, bool $selfPath = true): ?array {
        if (defined('PHPUNIT_TEST') && PHPUNIT_TEST && !empty(self::$mockedApisList)) {
            return array_keys(self::$mockedApisList); // @codeCoverageIgnore
        }

        if (empty(self::$apis)) {
            // Verify that we are able to find a valid moodle root.
            if ($moodleRoot = self::getMoodleRoot($file, $selfPath)) {
                // APIs are located in lib/apis.json.
                $apisFile = $moodleRoot . '/lib/apis.json';

                if (is_readable($apisFile)) {
                    $data = json_decode(file_get_contents($apisFile), true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        self::$apis = $data;
                    }
                }
            }

            if (empty(self::$apis)) {
                // If there is no apis.json file, we can't load the current APIs.
                // Load the version from the release of 4.2 when the file was introduced.
                // TODO Remove after min requirement is >= Moodle 4.2 #115.
                $apisFile = __DIR__ . '/apis.json';

                $data = json_decode(file_get_contents($apisFile), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return null; // @codeCoverageIgnore
                }

                self::$apis = $data;
            }
        }

        return array_keys(self::$apis);
    }

    /**
     * Try to guess moodle branch (numeric)
     *
     * This method will parse the moodle root version.php file
     * returning the $branch information from it. It will try to:
     * - detect if the moodleBranch configuration/runtime option has been set.
     * - trying to detect moodle root and parsing the version.php file within it.
     *
     * @param File|null $file File that is being checked.
     * @param bool $selfPath Enables the method to also consider self path to search for a valid moodle root.
     *
     * @return int|null the numeric branch in moodle root version.php or null if not found
     */
    public static function getMoodleBranch(?File $file = null, bool $selfPath = true): ?int {

        // Return already calculated value if available.
        if (self::$moodleBranch !== false) {
            return self::$moodleBranch;
        }

        // First, try to get it from configuration/runtime option.
        if ($branch = Config::getConfigData('moodleBranch')) {
            // Verify it's integer value and <= 9999 (4 digits max).
            if (filter_var($branch, FILTER_VALIDATE_INT) === false) {
                throw new DeepExitException(
                    "ERROR: Incorrect 'moodleBranch' config/runtime option. Value in not an integer: '$branch'",
                    3
                );
            }
            if ($branch > 9999) {
                throw new DeepExitException(
                    "ERROR: Incorrect 'moodleBranch' config/runtime option. Value must be 4 digit max.: '$branch'",
                    3
                );
            }
            self::$moodleBranch = $branch;
            return self::$moodleBranch;
        }

        // Now, let's try to find moodle root and then parse the version.php there.
        if ($moodleRoot = self::getMoodleRoot($file, $selfPath)) {
            // Let's use CodeSniffer own facilities to parse the version.php file.
            // Pass the parallel as CLI, disabled. Note
            // this is to avoid some nasty argv notices.
            $config = new Config(['--parallel=1']);
            $ruleset = new Ruleset($config);
            $versionFile = new DummyFile(file_get_contents($moodleRoot . '/version.php'), $ruleset, $config);
            $versionFile->parse();
            // Find the $branch variable declaration.
            if ($varToken = $versionFile->findNext(T_VARIABLE, 0, null, false, '$branch')) {
                // Find the $branch value.
                if ($valueToken = $versionFile->findNext(T_CONSTANT_ENCAPSED_STRING, $varToken)) {
                    $branch = trim($versionFile->getTokens()[$valueToken]['content'], "\"'");
                    self::$moodleBranch = $branch;
                    return self::$moodleBranch;
                }
            }
        }

        // Still not found, bad luck, cannot calculate moodle branch.
        self::$moodleBranch = null;
        return self::$moodleBranch;
    }


    /**
     * Try to guess moodle root full path (needed for other utils).
     *
     * This method will try to guess the full path to moodle root by:
     * - detect if the moodleRoot configuration/runtime option has been set.
     * - looking recursively up from the file being checked.
     * - looking recursively up from this file.
     *
     * @param File|null $file File that is being checked.
     * @param bool $selfPath Enables the method to also consider self path to search for a valid moodle root.
     *
     * @return string|null the full path to moodle root or null if not found.
     */
    public static function getMoodleRoot(?File $file = null, bool $selfPath = true): ?string {
        // Return already calculated value if available.
        if (self::$moodleRoot !== false) {
            return self::$moodleRoot;
        }

        // First, try to get it from configuration/runtime option.
        if ($path = Config::getConfigData('moodleRoot')) {
            // Verify the path is exists and is readable.
            if (!is_dir($path) || !is_readable($path)) {
                throw new DeepExitException(
                    "ERROR: Incorrect 'moodleRoot' config/runtime option. Directory does not exist or is not readable: '$path'",
                    3
                );
            }
            // Verify the path has version.php and config-dist.php files. Very basic, but effective check.
            if (!is_readable($path . '/version.php') || !is_readable($path . '/config-dist.php')) {
                throw new DeepExitException(
                    "ERROR: Incorrect 'moodleRoot' config/runtime option. Directory is not a valid moodle root: '$path'",
                    3
                );
            }
            self::$moodleRoot = $path;
            return self::$moodleRoot;
        }

        // Still not found, let's look upwards for a main version file and config-dist.php file
        // starting from the file path being checked (given it has been passed).
        if ($file instanceof File) {
            $path = $lastPath = $file->path;
            while (($path = pathinfo($path, PATHINFO_DIRNAME)) !== $lastPath) {
                // If we find both a version.php and config-dist.php file then we have arrived to moodle root.
                if (is_readable($path . '/version.php') && is_readable($path . '/config-dist.php')) {
                    self::$moodleRoot = $path;
                    return self::$moodleRoot;
                }
                // Path processed.
                $lastPath = $path;
            }
        }

        // Still not found, let's look upwards for a main version file and config-dist.php file
        // starting from this file path. Only if explicitly allowed by $selfPath.
        if ($selfPath) {
            $path = $lastPath = __FILE__;
            while (($path = pathinfo($path, PATHINFO_DIRNAME)) !== $lastPath) {
                // If we find both a version.php and config-dist.php file then we have arrived to moodle root.
                if (is_readable($path . '/version.php') && is_readable($path . '/config-dist.php')) {
                    self::$moodleRoot = $path;
                    return self::$moodleRoot;
                }
                // Path processed.
                $lastPath = $path;
            }
        }

        // Still not found, bad luck, cannot calculate moodle root.
        self::$moodleRoot = null;
        return self::$moodleRoot;
    }

    /**
     * Whether this file is a lang file.
     *
     * @param File $phpcsFile
     * @return bool
     */
    public static function isLangFile(File $phpcsFile): bool
    {
        $filename = MoodleUtil::getStandardisedFilename($phpcsFile);
        // If the file is not under a /lang/[a-zA-Z0-9_-]+/ directory, nothing to check.
        // (note that we are using that regex because it's what PARAM_LANG does).
        if (preg_match('~/lang/[a-zA-Z0-9_-]+/~', $filename) === 0) {
            return false;
        }

        // If the file is not a PHP file, nothing to check.
        if (substr($filename, -4) !== '.php') {
            return false;
        }

        return true;
    }

    /**
     * Whether this file is a unit test file.
     *
     * This does not include test fixtures, generators, or behat files.
     *
     * Any file which is not correctly named will be ignored.
     *
     * @param File $phpcsFile
     * @return bool
     */
    public static function isUnitTest(File $phpcsFile): bool
    {
        $filename = MoodleUtil::getStandardisedFilename($phpcsFile);
        // If the file isn't called, _test.php, nothing to check.
        if (stripos(basename($phpcsFile->getFilename()), '_test.php') === false) {
            return false;
        }

        // If the file isn't under tests directory, nothing to check.
        if (stripos($filename, '/tests/') === false) {
            return false;
        }

        // If the file is in a fixture directory, ignore it.
        if (stripos($filename, '/tests/fixtures/') !== false) {
            return false;
        }

        // If the file is in a generator directory, ignore it.
        if (stripos($filename, '/tests/generator/') !== false) {
            return false;
        }

        // If the file is in a behat directory, ignore it.
        if (stripos($filename, '/tests/behat/') !== false) {
            return false;
        }

        return true;
    }

    /**
     * Whether we are running PHPUnit.
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public static function isUnitTestRunning(): bool
    {
        // Detect if we are running PHPUnit.
        return defined('PHPUNIT_TEST') && PHPUNIT_TEST;
    }

    /**
     * Whether the class is a unit test case class.
     *
     * @param File $file
     * @param int $classPtr
     * @return bool
     */
    public static function isUnitTestCaseClass(
        File $file,
        int $classPtr
    ): bool {
        $tokens = $file->getTokens();
        $class = $file->getDeclarationName($classPtr);

        // Only if the class is extending something.
        // TODO: We could add a list of valid classes once we have a class-map available.
        if (!$file->findNext(T_EXTENDS, $classPtr + 1, $tokens[$classPtr]['scope_opener'])) {
            return false;
        }

        // Ignore non ended "_test|_testcase" classes.
        if (substr($class, -5) !== '_test' && substr($class, -9) != '_testcase') {
            return false;
        }

        // This is a class, which extends another class, and whose name ends in _test.
        return true;
    }

    /**
     * Whether the file belongs to a version of Moodle meeting the specifeid minimum version.
     *
     * If a version could not be determined, null is returned.
     *
     * @param File $phpcsFile The file to check
     * @param int The minimum version to check against as a 2, or 3 digit number.
     * @return null|bool
     */
    public static function meetsMinimumMoodleVersion(
        File $phpcsFile,
        int $version
    ): ?bool {
        $moodleBranch = self::getMoodleBranch($phpcsFile);
        if (!isset($moodleBranch)) {
            // We cannot determine the moodle branch, so we cannot determine if the version is met.
            return null;
        }

        return ($moodleBranch >= $version);
    }

    /**
     * Find the pointer to a method in a class.
     *
     * @param File $phpcsFile
     * @param int $classPtr
     * @param string $methodName
     * @return null|int
     */
    public static function findClassMethodPointer(
        File $phpcsFile,
        int $classPtr,
        string $methodName
    ): ?int {
        $mStart = $classPtr;
        $tokens = $phpcsFile->getTokens();
        while ($mStart = $phpcsFile->findNext(T_FUNCTION, $mStart + 1, $tokens[$classPtr]['scope_closer'])) {
            $method = $phpcsFile->getDeclarationName($mStart);
            if ($method === $methodName) {
                return $mStart;
            }
        }

        return null;
    }

    /**
     * Get all tokens relating to a particular line.
     *
     * @param File $phpcsFile
     * @param int $line
     * @return array
     */
    public static function getTokensOnLine(
        File $phpcsFile,
        int $line
    ): array {
        return array_filter(
            $phpcsFile->getTokens(),
            fn($token) => $token['line'] === $line,
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * Get the standardised filename for the file.
     *
     * @param File @phpcsFile
     * @return string
     */
    public static function getStandardisedFilename(File $phpcsFile): string {
        return str_replace('\\', '/', $phpcsFile->getFilename());
    }
}
