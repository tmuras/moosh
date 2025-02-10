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

namespace MoodleHQ\MoodleCS\moodle\Tests\Util;

use MoodleHQ\MoodleCS\moodle\Tests\MoodleCSBaseTestCase;
use MoodleHQ\MoodleCS\moodle\Util\MoodleUtil;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Exceptions\DeepExitException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Ruleset;
use org\bovigo\vfs\vfsStream;
use PHPCSUtils\Utils\ObjectDeclarations;

/**
 * Test the MoodleUtil specific moodle utilities class
 *
 * @copyright  2021 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Util\MoodleUtil
 */
class MoodleUtilTest extends MoodleCSBaseTestCase
{
    /**
     * Unit test for calculateAllComponents.
     *
     * Not 100% orthodox because {@see calculateAllComponents()} is protected,
     * and it's already indirectly tested by {@see test_getMoodleComponent()}
     * but it has some feature that we need to test individually here.
     */
    public function testCalculateAllComponents() {
        // Let's calculate moodleRoot.
        $vfs = vfsStream::setup('root', null, []);
        $moodleRoot = $vfs->url();

        // Let's prepare a components file, with some correct and incorrect entries.
        $components =
            "nonono,mod_forum,{$moodleRoot}/mod_forum\n" .                 // Wrong type.
            "plugin,mod__nono,{$moodleRoot}/mod_forum\n" .                 // Wrong component.
            "plugin,mod_forum,/no/no/no/no//mod_forum\n" .                 // Wrong path.
            "plugin,local_codechecker,{$moodleRoot}/local/codechecker\n" . // All ok.
            "plugin,mod_forum,{$moodleRoot}/mod/forum\n";                  // All ok.

        vfsStream::create(
            ['components.txt' => $components],
            $vfs
        );

        // Set codechecker config to point to it.
        Config::setConfigData('moodleComponentsListPath', $vfs->url() . '/components.txt', true);

        // Let's run calculateAllComponents() and evaluate results.
        $method = new \ReflectionMethod(MoodleUtil::class, 'calculateAllComponents');
        $method->setAccessible(true);
        $method->invokeArgs(null, [$moodleRoot]);

        // Let's inspect which components have been loaded.
        $property = new \ReflectionProperty(MoodleUtil::class, 'moodleComponents');
        $property->setAccessible(true);
        $loadedComponents = $property->getValue();

        $this->assertCount(2, $loadedComponents);
        // Verify they are ordered in ascending order.
        $this->assertSame(
            ['mod_forum', 'local_codechecker'],
            array_keys($loadedComponents)
        );
        // Verify component paths are also the expected ones.
        $this->assertSame(
            ["{$moodleRoot}/mod/forum", "{$moodleRoot}/local/codechecker"],
            array_values($loadedComponents)
        );

        // Now be evil and try with an unreadable file, it must throw an exception.

        $this->cleanMoodleUtilCaches(); // Need to clean previous cached values.
        Config::setConfigData('moodleComponentsListPath', '/path/to/non/readable/file', true);

        // We cannot use expectException() here, because we need to clean caches at the end.
        try {
            $method->invokeArgs(null, [$moodleRoot]);
            $this->fail('\PHP_CodeSniffer\Exceptions\DeepExitException was expected, got none');
        } catch (\Exception $e) {
            $this->assertInstanceOf(\PHP_CodeSniffer\Exceptions\DeepExitException::class, $e);
        }

        // Ensure cached information doesn't affect other tests.
        $this->cleanMoodleUtilCaches();
        Config::setConfigData('moodleComponentsListPath', null, true);
    }

    /**
     * Provider for test_getMoodleComponent.
     */
    public function getMoodleComponentProvider() {
        return [
            'moodleComponent_file_without_moodleroot' => [
                'config' => ['file' => sys_get_temp_dir() . '/notexists.php'],
                'return' => ['value' => null],
                'reset' => true,
                'selfPath' => false,
                'requireMockMoodle' => false,
            ],
            'moodleComponent_file_without_component_class' => [
                'config' => ['file' => dirname(__FILE__) . '/fixtures/moodleutil/good/lib/lib.php'],
                'return' => ['value' => null],
                'reset' => true,
                'selfPath' => false,
                'requireMockMoodle' => false,
            ],
            'moodleComponent_file_valid' => [
                'config' => ['file' => 'local/invented/lib.php'],
                'return' => ['value' => 'local_invented'],
                'reset' => false, // Prevent resetting cached information to verify next works.
                'selfPath' => false,
                'requireMockMoodle' => true,
            ],
            'moodleComponent_file_already_cached' => [
                'config' => ['file' => 'lib/lib.php'],
                'return' => ['value' => 'core'],
                'reset' => true,
                'selfPath' => false,
                'requireMockMoodle' => true,
            ],
            'moodleComponent_file_cache_cleaned' => [
                'config' => ['file' => dirname(__FILE__) . '/fixtures/moodleutil/good/lib/lib.php'],
                'return' => ['value' => null],
                'reset' => true,
                'selfPath' => false,
                'requireMockMoodle' => false,
            ],
            'moodleComponent_file_without_component' => [
                'config' => ['file' => dirname(__FILE__, 5) . '/userpix/index.php'],
                'return' => ['value' => null],
                'reset' => true,
                'selfPath' => false,
                'requireMockMoodle' => false,
            ],
        ];
    }

    /**
     * Unit test for getMoodleComponent.
     *
     * @param array $config get the Config from provider.
     * @param array $return expected result of the test.
     * @param bool $reset to decide if static caches should be reset before the test.
     * @param bool $selfPath to decide if codechecker own path is good to find a valid moodle root.
     * @param bool $requireMockMoodle Whether a mock Moodle root is required for this test.
     *
     * @dataProvider getMoodleComponentProvider
     */
    public function testGetMoodleComponent(
        array $config,
        array $return,
        bool $reset = true,
        bool $selfPath = true,
        bool $requireMockMoodle = false
    ) {
        if ($requireMockMoodle) {
            // We have to mock the passed moodleRoot.
            $vfs = vfsStream::setup('mocksite', null, []);
            vfsStream::copyFromFileSystem(__DIR__ . '/fixtures/moodleutil/complete', $vfs);
            $config['moodleRoot'] = $vfs->url(); // Let's add it to the standard config and immediately use it.
            Config::setConfigData('moodleRoot', $config['moodleRoot'], true);
            $this->requireRealMoodleRoot();

            // Also, we need to set the config['file'] to point to the vfs one.
            $config['file'] = $vfs->url() . '/' . $config['file'];
        }

        $file = null;
        // Set config options when passed.
        if ($config) {
            foreach ($config as $key => $value) {
                if ($key === 'file') {
                    // We are passing a real File, prepare it.
                    $phpcsConfig = new Config();
                    $phpcsRuleset = new Ruleset($phpcsConfig);
                    $file = new File($value, $phpcsRuleset, $phpcsConfig);
                } else {
                    // Normal config.
                    Config::setConfigData($key, $value, true);
                }
            }
        }

        // Exception is coming, let's verify it happens.
        if (isset($return['exception'])) {
            try {
                MoodleUtil::getMoodleComponent($file, $selfPath);
            } catch (\Exception $e) {
                $this->assertInstanceOf($return['exception'], $e);
                $this->assertStringContainsString($return['message'], $e->getMessage());
            }
        } elseif (array_key_exists('value', $return)) {
            // Normal asserting result.
            $this->assertSame($return['value'], MoodleUtil::getMoodleComponent($file, $selfPath));
        }

        // Do we want to reset any information cached (by default we do).
        if ($reset) {
            $this->cleanMoodleUtilCaches();
        }

        // We need to unset all config options when passed.
        if ($config) {
            foreach ($config as $key => $value) {
                Config::setConfigData($key, null, true);
            }
        }
    }

    /**
     * Provider for test_getMoodleBranch.
     */
    public function getMoodleBranchProvider() {
        return [
            // Setting up moodleBranch config/runtime option.
            'moodleBranch_not_integer' => [
                'config' => ['moodleBranch' => 'noint'],
                'return' => ['exception' => DeepExitException::class, 'message' => 'Value in not an integer'],
            ],
            'moodleBranch_big' => [
                'config' => ['moodleBranch' => '10000'],
                'return' => ['exception' => DeepExitException::class, 'message' => 'Value must be 4 digit max'],
            ],
            'moodleBranch_valid' => [
                'config' => ['moodleBranch' => 999],
                'return' => ['value' => 999],
                'reset'  => false, // Prevent resetting cached information to verify next works.
            ],
            'moodleBranch_already_cached' => [
                'config' => ['moodleBranch' => 'noint'],
                'return' => ['value' => 999],
            ],
            'moodleBranch_cache_cleaned' => [ // Verify that previous has cleaned cached information.
                'config' => ['moodleBranch' => 'noint'],
                'return' => ['exception' => DeepExitException::class, 'message' => 'Value in not an integer'],
            ],

            // Passing a file to check with correct $branch information at moodle root.
            'moodleBranch_pass_file_good' => [
                'config' => ['file' => dirname(__FILE__) . '/fixtures/moodleutil/good/lib/lib.php'],
                'return' => ['value' => 9876],
            ],

            // Passing a file to check with incorrect $branch information at moodle root.
            'moodleBranch_pass_file_bad' => [
                'config' => ['file' => dirname(__FILE__) . '/fixtures/moodleutil/bad/lib/lib.php'],
                'return' => ['value' => null],
            ],
        ];
    }

    /**
     * Unit test for getMoodleBranch.
     *
     * @param array $config get the Config from provider.
     * @param array $return expected result of the test.
     * @param bool $reset to decide if static caches should be reset before the test.
     * @param bool $selfPath to decide if codechecker own path is good to find a valid moodle root.
     *
     * @dataProvider getMoodleBranchProvider
     */
    public function testGetMoodleBranch(array $config, array $return, bool $reset = true, bool $selfPath = true) {
        $file = null;
        // Set config options when passed.
        if ($config) {
            foreach ($config as $key => $value) {
                if ($key === 'file') {
                    // We are passing a real File, prepare it.
                    $phpcsConfig = new Config();
                    $phpcsRuleset = new Ruleset($phpcsConfig);
                    $file = new File($value, $phpcsRuleset, $phpcsConfig);
                } else {
                    // Normal config.
                    Config::setConfigData($key, $value, true);
                }
            }
        }

        // Exception is coming, let's verify it happens.
        if (isset($return['exception'])) {
            try {
                MoodleUtil::getMoodleBranch($file, $selfPath);
            } catch (\Exception $e) {
                $this->assertInstanceOf($return['exception'], $e);
                $this->assertStringContainsString($return['message'], $e->getMessage());
            }
        } elseif (array_key_exists('value', $return)) {
            // Normal asserting result.
            $this->assertSame($return['value'], MoodleUtil::getMoodleBranch($file, $selfPath));
        }

        // Do we want to reset any information cached (by default we do).
        if ($reset) {
            $this->cleanMoodleUtilCaches();
        }

        // We need to unset all config options when passed.
        if ($config) {
            foreach ($config as $key => $value) {
                Config::setConfigData($key, null, true);
            }
        }
    }

    /**
     * Provider for test_getMoodleRoot.
     */
    public function getMoodleRootProvider() {
        return [
            // Setting up moodleRoot config/runtime option.
            'moodleRoot_not_exists' => [
                'config' => ['moodleRoot' => '/does/not/exist'],
                'return' => ['exception' => DeepExitException::class, 'message' => 'does not exist or is not readable'],
                'requireRealMoodle' => false,
            ],
            'moodleRoot_not_moodle' => [
                'config' => ['moodleRoot' => sys_get_temp_dir()],
                'return' => ['exception' => DeepExitException::class, 'message' => 'not a valid moodle root'],
                'requireRealMoodle' => false,
            ],
            'moodleRoot_valid' => [
                'config' => ['moodleRoot' => 'some_valid_moodle_root'],
                'return' => ['value' => 'some_valid_moodle_root'],
                'requireRealMoodle' => true,
                'reset'  => false, // Prevent resetting cached information to verify next works.
            ],
            'moodleRoot_already_cached' => [
                'config' => ['moodleRoot' => '/does/not/exist'],
                'return' => ['value' => 'some_valid_moodle_root'],
                'requireRealMoodle' => true,
            ],
            'moodleRoot_cache_cleaned' => [ // Verify that previous has cleaned cached information.
                'config' => ['moodleRoot' => '/does/not/exist'],
                'return' => ['exception' => DeepExitException::class, 'message' => 'does not exist or is not readable'],
                'requireRealMoodle' => false,
            ],
            'moodleRoot_from_fixtures' => [
                'config' => ['moodleRoot' => dirname(__FILE__) . '/fixtures/moodleutil/good'],
                'return' => ['value' => dirname(__FILE__) . '/fixtures/moodleutil/good'],
                'requireRealMoodle' => false,
            ],

            // Passing a file to check.
            'moodleRoot_pass_file' => [
                'config' => ['file' => dirname(__FILE__) . '/fixtures/moodleutil/good/lib/lib.php'],
                'return' => ['value' => dirname(__FILE__) . '/fixtures/moodleutil/good'],
                'requireRealMoodle' => false,
            ],

            // Passing nothing, defaults to this file, that leads to not valid moodle root.
            'moodleRoot_pass_nothing' => [
                'config' => [],
                'return' => ['value' => null],
                'requireRealMoodle' => false,
            ],
        ];
    }

    /**
     * Unit test for getMoodleRoot.
     *
     * @param array $config get the Config from provider.
     * @param array $return expected result of the test.
     * @param bool $reset to decide if static caches should be reset before the test.
     * @param bool $selfPath to decide if moodle-cs own path is good to find a valid moodle root.
     * @param bool $requireMockMoodle Whether a mock Moodle root is required for this test.
     *
     * @dataProvider getMoodleRootProvider
     */
    public function testGetMoodleRoot(
        array $config,
        array $return,
        bool $requireMockMoodle = false,
        bool $reset = true,
        bool $selfPath = true
    ) {
        if ($requireMockMoodle) {
            if (isset($config['moodleRoot']) && isset($return['value'])) {
                // We have to mock the passed moodleRoot.
                $vfs = vfsStream::setup($config['moodleRoot'], null, [
                    'version.php' => 'some version contents, not important for this test',
                    'config-dist.php' => 'come config contents, not important for this test',
                ]);
                $config['moodleRoot'] = $vfs->url(); // Let's add it to the standard config and immediately use it.
                Config::setConfigData('moodleRoot', $config['moodleRoot'], true);
                $this->requireRealMoodleRoot();

                // We also have to mock the passed expectation for the test.
                $returnVfs = vfsStream::setup($return['value'], null, []);
                $return['value'] = $returnVfs->url();
            }
        }

        $file = null;
        // Set config options when passed.
        if ($config) {
            foreach ($config as $key => $value) {
                if ($key === 'file') {
                    // We are passing a real File, prepare it.
                    $phpcsConfig = new Config();
                    $phpcsRuleset = new Ruleset($phpcsConfig);
                    $file = new File($value, $phpcsRuleset, $phpcsConfig);
                } else {
                    // Normal config.
                    Config::setConfigData($key, $value, true);
                }
            }
        }

        // Exception is coming, let's verify it happens.
        if (isset($return['exception'])) {
            try {
                MoodleUtil::getMoodleRoot($file, $selfPath);
            } catch (\Exception $e) {
                $this->assertInstanceOf($return['exception'], $e);
                $this->assertStringContainsString($return['message'], $e->getMessage());
            }
        } elseif (array_key_exists('value', $return)) {
            // Normal asserting result.
            $this->assertSame($return['value'], MoodleUtil::getMoodleRoot($file), $selfPath);
        }

        // Do we want to reset any information cached (by default we do).
        if ($reset) {
            $this->cleanMoodleUtilCaches();
        }

        // We need to unset all config options when passed.
        if ($config) {
            foreach ($config as $key => $value) {
                Config::setConfigData($key, null, true);
            }
        }
    }

    /**
     * Utility method to clean MoodleUtil own "caches" (class properties).
     */
    protected function cleanMoodleUtilCaches() {
        $moodleUtil = new \ReflectionClass(MoodleUtil::class);
        $moodleRoot = $moodleUtil->getProperty('moodleRoot');
        $moodleRoot->setAccessible(true);
        $moodleUtil->setStaticPropertyValue('moodleRoot', false);

        $moodleUtil = new \ReflectionClass(MoodleUtil::class);
        $moodleBranch = $moodleUtil->getProperty('moodleBranch');
        $moodleBranch->setAccessible(true);
        $moodleUtil->setStaticPropertyValue('moodleBranch', false);

        $moodleUtil = new \ReflectionClass(MoodleUtil::class);
        $moodleComponents = $moodleUtil->getProperty('moodleComponents');
        $moodleComponents->setAccessible(true);
        $moodleUtil->setStaticPropertyValue('moodleComponents', []);

        $apiCache = $moodleUtil->getProperty('apis');
        $apiCache->setAccessible(true);
        $apiCache->setValue(null, []);

        $apiCache = $moodleUtil->getProperty('mockedApisList');
        $apiCache->setAccessible(true);
        $apiCache->setValue(null, []);
    }

    /**
     * Data provider for testIsLangFile.
     *
     * @return array
     */
    public static function isLangFileProvider(): array
    {
        return [
            'Not in lang directory' => [
                'value' => '/path/to/standard/file.php',
                'return' => false,
            ],
            'In lang/en directory' => [
                'value' => '/path/to/standard/lang/en/file.php',
                'return' => true,
            ],
            'In lang directory but missing en' => [
                'value' => '/path/to/standard/lang/file.php',
                'return' => false,
            ],
            'In lang/en directory but missing .php' => [
                'value' => '/path/to/standard/lang/en/file',
                'return' => false,
            ],
            'In lang/en directory but another extension' => [
                'value' => '/path/to/standard/lang/en/file.md',
                'return' => false,
            ],
            'In lang sub-directory with not allowed chars' => [
                'value' => '/path/to/standard/lang/@@@/file.php',
                'return' => false,
            ],
        ];
    }

    /**
     * @dataProvider isLangFileProvider
     */
    public function testIsLangFile(
        string $filepath,
        bool $expected
    ): void {
        $phpcsConfig = new Config();
        $phpcsRuleset = new Ruleset($phpcsConfig);
        $file = new File($filepath, $phpcsRuleset, $phpcsConfig);

        $this->assertEquals($expected, MoodleUtil::isLangFile($file));
    }

    /**
     * Data provider for testIsUnitTest.
     *
     * @return array
     */
    public static function isUnitTestProvider(): array
    {
        return [
            'Not in tests directory' => [
                'value' => '/path/to/standard/file_test.php',
                'return' => false,
            ],
            'In tests directory' => [
                'value' => '/path/to/standard/tests/file_test.php',
                'return' => true,
            ],
            'In tests directory but missing _test suffix' => [
                'value' => '/path/to/standard/tests/file.php',
                'return' => false,
            ],
            'In tests directory but some idiot put a _test.php suffix on a directory' => [
                'value' => '/path/to/standard/tests/some_test.php/file.php',
                'return' => false,
            ],
            'In test sub-directory' => [
                'value' => '/path/to/standard/tests/sub/file_test.php',
                'return' => true,
            ],
            'In test sub-directory but missing _test suffix' => [
                'value' => '/path/to/standard/tests/sub/file.php',
                'return' => false,
            ],
            'Generator' => [
                'value' => '/path/to/standard/tests/generator/file_test.php',
                'return' => false,
            ],
            'Fixture' => [
                'value' => '/path/to/standard/tests/fixtures/file_test.php',
                'return' => false,
            ],
            'Behat' => [
                'value' => '/path/to/standard/tests/behat/behat_test_file_test.php',
                'return' => false,
            ],
        ];
    }

    /**
     * @dataProvider isUnitTestProvider
     */
    public function testIsUnitTest(
        string $filepath,
        bool $expected
    ): void {
        $phpcsConfig = new Config();
        $phpcsRuleset = new Ruleset($phpcsConfig);
        $file = new File($filepath, $phpcsRuleset, $phpcsConfig);

        $this->assertEquals($expected, MoodleUtil::isUnitTest($file));
    }

    /**
     * Data provider for testIsUnitTest.
     *
     * @return array
     */
    public static function isUnitTestCaseClassProvider(): array {
        return [
            'Not in tests directory' => [
                'value' => '/path/to/standard/file_test.php',
                'testClassName' => 'irrelevant',
                'return' => false,
            ],
            'testcase in a test file' => [
                'value' => __DIR__ . '/fixtures/moodleutil/istestcaseclass/multiple_classes_test.php',
                'testClassName' => 'is_testcase',
                'return' => true,
            ],
            'not a testcase in a test file' => [
                'value' => __DIR__ . '/fixtures/moodleutil/istestcaseclass/multiple_classes_test.php',
                'testClassName' => 'not_a_test_class',
                'return' => false,
            ],
            'not a testcase with test in the name in a test file' => [
                'value' => __DIR__ . '/fixtures/moodleutil/istestcaseclass/multiple_classes_test.php',
                'testClassName' => 'some_other_class_with_test_in_name',
                'return' => false,
            ],
            'not a testcase but extends a test class in a test file' => [
                'value' => __DIR__ . '/fixtures/moodleutil/istestcaseclass/multiple_classes_test.php',
                'testClassName' => 'some_other_class_with_test_in_name',
                'return' => false,
            ],
            'looks like a test but does not extend a test class' => [
                'value' => __DIR__ . '/fixtures/moodleutil/istestcaseclass/multiple_classes_test.php',
                'testClassName' => 'some_other_class_with_test_in_name_not_extending_test',
                'return' => false,
            ],
        ];
    }

    /**
     * @dataProvider isUnitTestCaseClassProvider
     */
    public function testIsUnitTestCaseClass(
        string $filepath,
        string $testClassName,
        bool $expected
    ): void {
        $phpcsConfig = new Config();
        $phpcsRuleset = new Ruleset($phpcsConfig);

        if (file_exists($filepath)) {
            $file = new \PHP_CodeSniffer\Files\LocalFile($filepath, $phpcsRuleset, $phpcsConfig);
            $file->process();
        } else {
            $file = new File($filepath, $phpcsRuleset, $phpcsConfig);
        }

        $cStart = 0;
        while ($cStart = $file->findNext(T_CLASS, $cStart + 1)) {
            if (ObjectDeclarations::getName($file, $cStart) === $testClassName) {
                $this->assertEquals($expected, MoodleUtil::isUnitTestCaseClass($file, $cStart));
                break;
            }
        }
    }

    /**
     * Data provider for testMeetsMinimumMoodleVersion.
     *
     * @return array
     */
    public static function meetsMinimumMoodleVersionProvider(): array
    {
        return [
            // Setting up moodleBranch config/runtime option.
            'moodleBranch_not_integer' => [
                'moodleVersion' => 'noint',
                'minVersion' => 311,
                'return' => ['exception' => DeepExitException::class, 'message' => 'Value in not an integer'],
            ],
            'moodleBranch_big' => [
                'moodleVersion' => '10000',
                'minVersion' => 311,
                'return' => ['exception' => DeepExitException::class, 'message' => 'Value must be 4 digit max'],
            ],
            'moodleBranch_valid_meets_minimum' => [
                'moodleVersion' => 999,
                'minVersion' => 311,
                'return' => ['value' => true],
            ],
            'moodleBranch_valid_equals_minimum' => [
                'moodleVersion' => 311,
                'minVersion' => 311,
                'return' => ['value' => true],
            ],
            'moodleBranch_valid_does_not_meet_minimum' => [
                'moodleVersion' => 311,
                'minVersion' => 402,
                'return' => ['value' => false],
            ],
            'moodleBranch_valid_but_empty' => [
                'moodleVersion' => 0,
                'minVersion' => 311,
                'return' => ['value' => null],
            ],
        ];
    }

    /**
     * @dataProvider meetsMinimumMoodleVersionProvider
     * @param string|int $moodleVersion
     * @param int $minVersion
     * @param array $return
     */
    public function testMeetsMinimumMoodleVersion(
        $moodleVersion,
        int $minVersion,
        array $return
    ): void {
        Config::setConfigData('moodleBranch', $moodleVersion, true);

        $phpcsConfig = new Config();
        $phpcsRuleset = new Ruleset($phpcsConfig);
        $file = new File('/path/to/tests/file.php', $phpcsRuleset, $phpcsConfig);

        // Exception is coming, let's verify it happens.
        if (isset($return['exception'])) {
            try {
                MoodleUtil::getMoodleBranch($file);
            } catch (\Exception $e) {
                $this->assertInstanceOf($return['exception'], $e);
                $this->assertStringContainsString($return['message'], $e->getMessage());
            }
        } elseif (array_key_exists('value', $return)) {
            // Normal asserting result.
            $this->assertSame($return['value'], MoodleUtil::meetsMinimumMoodleVersion($file, $minVersion));
        }

        // Do we want to reset any information cached (by default we do).
        $this->cleanMoodleUtilCaches();

        // We need to unset all config options when passed.
        Config::setConfigData('moodleBranch', null, true);
    }

    public static function findClassMethodPointerProvider(): array
    {
        return [
            [
                'instance_method',
                true,
            ],
            [
                'protected_method',
                true,
            ],
            [
                'private_method',
                true,
            ],
            [
                'static_method',
                true,
            ],
            [
                'protected_static_method',
                true,
            ],
            [
                'private_static_method',
                true,
            ],
            [
                'not_found_method',
                false,
            ],
        ];
    }

    /**
     * @dataProvider findClassMethodPointerProvider
     */
    public function testFindClassMethodPointer(
        string $methodName,
        bool $found
    ): void {
        $phpcsConfig = new Config();
        $phpcsRuleset = new Ruleset($phpcsConfig);
        $phpcsFile = new \PHP_CodeSniffer\Files\LocalFile(
            __DIR__ . '/fixtures/moodleutil/test_with_methods_to_find.php',
            $phpcsRuleset,
            $phpcsConfig
        );

        $phpcsFile->process();
        $classPointer = $phpcsFile->findNext(T_CLASS, 0);

        $pointer = MoodleUtil::findClassMethodPointer(
            $phpcsFile,
            $classPointer,
            $methodName
        );

        if ($found) {
            $this->assertGreaterThan(0, $pointer);
        } else {
            $this->assertNull($pointer);
        }
    }

    public function testGetTokensOnLine(): void {
        $phpcsConfig = new Config();
        $phpcsRuleset = new Ruleset($phpcsConfig);
        $phpcsFile = new \PHP_CodeSniffer\Files\LocalFile(
            __DIR__ . '/fixtures/moodleutil/test_with_methods_to_find.php',
            $phpcsRuleset,
            $phpcsConfig
        );

        $phpcsFile->process();
        $allTokens = $phpcsFile->getTokens();

        $expectedTokens = [];
        foreach ($allTokens as $tokenPtr => $token) {
            if ($token['line'] === 8) {
                $expectedTokens[$tokenPtr] = $token;
            }
        }

        // This line is:
        // protected function protected_method(): array {
        $tokens = MoodleUtil::getTokensOnLine($phpcsFile, 8);
        $this->assertCount(count($expectedTokens), $tokens);
        $this->assertEquals($expectedTokens, $tokens);
    }

    public function testGetMoodleApis(): void {
        $this->cleanMoodleUtilCaches();
        // Let's calculate moodleRoot.
        $vfs = vfsStream::setup('root', null, []);

        $apis = [
            'test' => [
                'component' => 'core',
                'allowlevel2' => false,
                'allowspread' => false,
            ],
            'time' => [
                'component' => 'core',
                'allowlevel2' => false,
                'allowspread' => false,
            ],
        ];

        vfsStream::copyFromFileSystem(__DIR__ . '/fixtures/moodleutil/complete', $vfs);
        vfsStream::create(
            [
                'lib' => [
                    'apis.json' => json_encode(
                        $apis,
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                    ),
                    'example.php' => '',
                ],
            ],
            $vfs
        );

        // We are passing a real File, prepare it.
        $phpcsConfig = new Config();
        $phpcsRuleset = new Ruleset($phpcsConfig);
        $file = new File($vfs->url() . '/lib/lib.php', $phpcsRuleset, $phpcsConfig);

        $this->assertEquals(
            array_keys($apis),
            MoodleUtil::getMoodleApis($file)
        );
    }

    public function testGetMoodleApisNoApis(): void {
        $this->cleanMoodleUtilCaches();

        // Let's calculate moodleRoot.
        $vfs = vfsStream::setup('root', null, []);

        vfsStream::copyFromFileSystem(__DIR__ . '/fixtures/moodleutil/complete', $vfs);

        // We are passing a real File, prepare it.
        $phpcsConfig = new Config();
        $phpcsRuleset = new Ruleset($phpcsConfig);
        $file = new File($vfs->url() . '/lib/lib.php', $phpcsRuleset, $phpcsConfig);

        $apis = json_decode(file_get_contents(__DIR__ . '/../../Util/apis.json'));
        $this->assertEquals(
            array_keys((array) $apis),
            MoodleUtil::getMoodleApis($file)
        );
    }

    public function testGetMoodleApisInvalidJson(): void {
        $this->cleanMoodleUtilCaches();
        // Let's calculate moodleRoot.
        $vfs = vfsStream::setup('root', null, []);

        vfsStream::copyFromFileSystem(__DIR__ . '/fixtures/moodleutil/complete', $vfs);
        vfsStream::create(
            [
                'lib' => [
                    'apis.json' => 'invalid:"json"',
                    'example.php' => '',
                ],
            ],
            $vfs
        );

        // We are passing a real File, prepare it.
        $phpcsConfig = new Config();
        $phpcsRuleset = new Ruleset($phpcsConfig);
        $file = new File($vfs->url() . '/lib/lib.php', $phpcsRuleset, $phpcsConfig);

        // Revert to the stored version if the file is not readable.
        $apis = json_decode(file_get_contents(__DIR__ . '/../../Util/apis.json'));
        $this->assertEquals(
            array_keys((array) $apis),
            MoodleUtil::getMoodleApis($file)
        );
    }


    public function testGetMoodleApisNotAMoodle(): void {
        $this->cleanMoodleUtilCaches();
        // Let's calculate moodleRoot.
        $vfs = vfsStream::setup('root', null, []);

        $apis = [
            'test' => [
                'component' => 'core',
                'allowlevel2' => false,
                'allowspread' => false,
            ],
            'time' => [
                'component' => 'core',
                'allowlevel2' => false,
                'allowspread' => false,
            ],
        ];

        vfsStream::create(
            [
                'lib' => [
                    'apis.json' => json_encode(
                        $apis,
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                    ),
                    'example.php' => '',
                ],
            ],
            $vfs
        );

        // We are passing a real File, prepare it.
        $phpcsConfig = new Config();
        $phpcsRuleset = new Ruleset($phpcsConfig);
        $file = new File($vfs->url() . '/lib/lib.php', $phpcsRuleset, $phpcsConfig);

        $apis = json_decode(file_get_contents(__DIR__ . '/../../Util/apis.json'));
        $this->assertEquals(
            array_keys((array) $apis),
            MoodleUtil::getMoodleApis($file)
        );
    }

    public function testGetMoodleApisMocked(): void {
        $this->cleanMoodleUtilCaches();
        // Let's calculate moodleRoot.
        $apis = [
            'test' => [
                'component' => 'core',
                'allowlevel2' => false,
                'allowspread' => false,
            ],
            'time' => [
                'component' => 'core',
                'allowlevel2' => false,
                'allowspread' => false,
            ],
        ];

        $vfs = vfsStream::setup('root', null, []);
        vfsStream::create(
            [
                'lib' => [
                    'apis.json' => json_encode([]),
                    'example.php' => '',
                ],
            ],
            $vfs
        );

        $this->setApiMappings($apis);

        // We are passing a real File, prepare it.
        $phpcsConfig = new Config();
        $phpcsRuleset = new Ruleset($phpcsConfig);
        $file = new File($vfs->url() . '/lib/lib.php', $phpcsRuleset, $phpcsConfig);

        $this->assertEquals(
            array_keys($apis),
            MoodleUtil::getMoodleApis($file)
        );
    }
}
