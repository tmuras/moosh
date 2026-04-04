<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Adds --number numeric comparison filter to a command.
 *
 * Usage:
 *   moosh course:list --number users-enrolled>10
 *   moosh course:list --number "users-enrolled=5"
 *   moosh course:list --number users-enrolled<3
 *
 * Multiple --number options can be combined.
 *
 * Classes using this trait must implement supportedNumericMetrics().
 */
trait NumericFilterTrait
{
    /**
     * Return supported numeric metric names with descriptions.
     *
     * Each metric maps to a callable that receives a course ID and returns an int.
     *
     * @return array<string, string> e.g. ['users-enrolled' => 'Number of enrolled users']
     */
    abstract protected function supportedNumericMetrics(): array;

    /**
     * Resolve the actual count for a given metric and course.
     *
     * @return int The count value for the metric on the given course.
     */
    abstract protected function resolveNumericMetric(string $metric, int $courseId): int;

    /**
     * Check whether a metric name is valid.
     *
     * Override to support dynamic/pattern-based metrics (e.g. mod-forum).
     * The default implementation checks against supportedNumericMetrics().
     */
    protected function isMetricSupported(string $metric): bool
    {
        return array_key_exists($metric, $this->supportedNumericMetrics());
    }

    /**
     * Register --number option on the command.
     */
    protected function configureNumericFilters(Command $command): void
    {
        $metrics = implode(', ', array_keys($this->supportedNumericMetrics()));

        $command->addOption(
            'number',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            "Numeric filter: metric{>|<|=}N ($metrics). E.g. --number users-enrolled>10",
        );
    }

    /**
     * Parse --number options into structured filter definitions.
     *
     * @return array<int, array{metric: string, operator: string, value: int}>
     *
     * @throws \InvalidArgumentException on invalid syntax or unknown metrics
     */
    protected function parseNumericFilters(InputInterface $input): array
    {
        $supported = $this->supportedNumericMetrics();
        $raw = $input->getOption('number');
        $filters = [];

        foreach ($raw as $expr) {
            if (!preg_match('/^([a-z][a-z0-9-]*)(>|<|=)(\d+)$/', $expr, $m)) {
                throw new \InvalidArgumentException(
                    "Invalid --number expression '$expr'. Expected format: metric{>|<|=}N (e.g. users-enrolled>10)"
                );
            }

            $metric = $m[1];
            $operator = $m[2];
            $value = (int) $m[3];

            if (!$this->isMetricSupported($metric)) {
                throw new \InvalidArgumentException(
                    "Unknown metric '$metric' for --number. Supported: " . implode(', ', array_keys($supported))
                );
            }

            $filters[] = [
                'metric' => $metric,
                'operator' => $operator,
                'value' => $value,
            ];
        }

        return $filters;
    }

    /**
     * Apply numeric filters to a list of courses, removing those that don't match.
     *
     * @param object[] $courses Keyed by course ID.
     * @param array<int, array{metric: string, operator: string, value: int}> $numericFilters
     * @return object[]
     */
    protected function applyNumericFilters(array $courses, array $numericFilters): array
    {
        if (!$numericFilters) {
            return $courses;
        }

        foreach ($courses as $key => $course) {
            foreach ($numericFilters as $filter) {
                $actual = $this->resolveNumericMetric($filter['metric'], (int) $course->id);
                $matches = match ($filter['operator']) {
                    '>' => $actual > $filter['value'],
                    '<' => $actual < $filter['value'],
                    '=' => $actual === $filter['value'],
                };
                if (!$matches) {
                    unset($courses[$key]);
                    break;
                }
            }
        }

        return $courses;
    }
}
