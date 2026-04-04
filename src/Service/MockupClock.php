<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Service;

/**
 * Fixed clock for testing — always returns the configured date/time.
 */
final class MockupClock implements ClockInterface
{
    private \DateTimeImmutable $fixedTime;

    public function __construct(string $dateTime)
    {
        $this->fixedTime = new \DateTimeImmutable($dateTime);
    }

    public function now(): \DateTimeImmutable
    {
        return $this->fixedTime;
    }
}
