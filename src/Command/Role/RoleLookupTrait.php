<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Role;

/**
 * Shared helpers for resolving a role by shortname or ID.
 */
trait RoleLookupTrait
{
    /**
     * Look up a role by shortname or numeric ID.
     *
     * Numeric strings are tried as IDs first, then as shortnames.
     *
     * @return object|null The role record, or null if not found.
     */
    private function findRole(string $identifier): ?object
    {
        global $DB;

        // Try as numeric ID first.
        if (ctype_digit($identifier)) {
            $role = $DB->get_record('role', ['id' => (int) $identifier]);
            if ($role) {
                return $role;
            }
        }

        // Try as shortname.
        $role = $DB->get_record('role', ['shortname' => $identifier]);
        return $role ?: null;
    }
}
