<?php declare(strict_types=1);
/*
 * This file is part of phpunit-coverage-check.
 *
 * (c) Thor Juhasz <thor@juhasz.pro>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnitCoverageCheck\Tests;

use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;
use OutOfRangeException;
use PHPUnit\Framework\TestCase;
use PHPUnitCoverageCheck\CoverageChecker;
use Symfony\Component\Console\Tester\CommandTester;
use function array_merge;
use function basename;
use function bccomp;
use function explode;
use function file_exists;
use function in_array;
use function is_numeric;
use function is_string;
use function sprintf;
use function str_contains;
use function trim;

/**
 * Class CoverageCheckerTest
 *
 * @covers \PHPUnitCoverageCheck\CoverageChecker
 */
class CoverageCheckerTest extends TestCase
{
    /**
     * @dataProvider provideMatrix
     *
     * @param mixed $file
     * @param mixed $threshold
     * @param mixed $metric
     * @param mixed $suppressErrors
     */
    public function testCoverageChecker($file = null, $threshold = null, $metric = null, $suppressErrors = null): void
    {
        if (is_string($file) === false) {
            $this->expectException(InvalidArgumentException::class);
            $this->executeCommand($file, $threshold, $metric, $suppressErrors);
        }

        if (file_exists($file) === false) {
            $this->expectException(InvalidArgumentException::class);
            $this->executeCommand($file, $threshold, $metric, $suppressErrors);
        }

        if ($threshold !== null && is_numeric($threshold) === false) {
            $this->expectException(InvalidArgumentException::class);
            $this->executeCommand($file, $threshold, $metric, $suppressErrors);
        }

        if (
            $threshold !== null &&
            (
                bccomp((string) $threshold, "0", 2) === -1 ||
                bccomp((string) $threshold, "100", 2) === 1
            )
        ) {
            $this->expectException(OutOfRangeException::class);
            $this->executeCommand($file, $threshold, $metric, $suppressErrors);
        }

        if ($metric !== null && in_array($metric, CoverageChecker::$allowedMetrics) === false) {
            $this->expectException(InvalidArgumentException::class);
            $this->executeCommand($file, $threshold, $metric, $suppressErrors);
        }

        /** @psalm-var numeric-string $coverage */
        $coverage = "80";
        if (is_string($file) && file_exists($file)) {
            $filename = basename($file, ".xml");
            $coverage = explode("_", $filename)[1];
        }

        $expectSuccess = false;
        if (
            (
                is_numeric($threshold) ||
                $threshold === null
            ) &&
            $coverage >= ($threshold ?? '80')) {
            $expectSuccess = true;
        }

        $exitCode = $suppressErrors || $expectSuccess ? 0 : 1;
        $expectedResult = sprintf("requires >= %d%% coverage", $threshold ?? '80');
        if (bccomp((string) $threshold, "100", 2) === 0) {
            $expectedResult = "requires full coverage";
        }

        $res = $this->executeCommand($file, $threshold, $metric, $suppressErrors);

        if (
            !str_contains($res['result'], $expectedResult) ||
            $res['exitCode'] !== $exitCode
        ) {
            var_dump(
                [
                    'result' => $res,
                    'expect' => [$expectedResult, $exitCode],
                ],
                [
                    'file' => $file,
                    'threshold' => $threshold,
                    'metric' => $metric,
                    'suppress' => $suppressErrors,
                ]
            );
            die;
        }

        $this->assertSame($exitCode, $res['exitCode']);
        $this->assertStringContainsString($expectedResult, $res['result']);
    }

    /**
     * @param mixed $file
     * @param mixed $threshold
     * @param mixed $metric
     * @param bool  $suppressErrors
     *
     * @return array
     * @psalm-return array{
     *     result: string,
     *     exitCode: int
     * }
     */
    #[ArrayShape(['result' => "string", 'exitCode' => "int"])]
    private function executeCommand($file, $threshold, $metric, bool $suppressErrors): array
    {
        $command       = new CoverageChecker();
        $commandTester = new CommandTester($command);

        $arguments = ['file' => $file,];

        if ($threshold !== null) {
            $arguments['--threshold'] = $threshold;
        }

        if ($metric !== null) {
            $arguments['--metric'] = $metric;
        }

        if ($suppressErrors) {
            $arguments['--suppress-errors'] = true;
        }

        $commandTester->execute(
           $arguments,
            [
                'capture_stderr_separately' => true,
            ]
        );

        return [
            'result'   => trim($commandTester->getDisplay()),
            'exitCode' => $commandTester->getStatusCode(),
        ];
    }

    /**
     * @return array[]
     */
    public function provideMatrix(): array
    {
        $files = [
            __DIR__ . '/Fixtures/coverage_1.xml',
            __DIR__ . '/Fixtures/coverage_50.xml',
            __DIR__ . '/Fixtures/coverage_100.xml',
            'unknown_file',
            123,
            null,
        ];

        $thresholds = [
            '0',
            '40',
            '100',
            '-1',
            '120',
            'asd',
            null,
        ];

        $metrics = array_merge(
            CoverageChecker::$allowedMetrics,
            [
                'unknownMetric',
                123,
                null,
            ]
        );

        $suppressErrors = [
            true,
            false,
        ];

        $matrices = [];

        foreach ($files as $file) {
            foreach ($thresholds as $threshold) {
                foreach ($metrics as $metric) {
                    foreach ($suppressErrors as $suppress) {
                        $matrix = [
                            'file'           => $file,
                            'threshold'      => $threshold,
                            'metric'         => $metric,
                            'suppressErrors' => $suppress,
                        ];

                        $matrices[] = $matrix;
                    }
                }
            }
        }

        return $matrices;
    }
}
