<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Service;

/**
 * Provides the current date/time, abstracting the system clock for testability.
 */
interface ClockInterface
{
    public function now(): \DateTimeImmutable;
}
