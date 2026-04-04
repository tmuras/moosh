<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Attribute;

use Attribute;

/**
 * Mark a command class or method as requiring a minimum Moodle version.
 *
 * When applied to a class, the entire command is skipped if the running
 * Moodle version is below the specified minimum.
 *
 * When applied to a method, that method's logic is skipped (the caller
 * is responsible for checking via MoodleVersion::isAtLeast()).
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class SinceVersion
{
    public function __construct(
        public readonly string $version,
    ) {
    }
}
