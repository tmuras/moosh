<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Bootstrap;

/**
 * Defines how deeply Moodle should be bootstrapped before running a command.
 *
 * Maps to the original moosh bootstrap constants:
 *   BOOTSTRAP_NONE            = 0
 *   BOOTSTRAP_CONFIG          = 1
 *   BOOTSTRAP_FULL            = 2
 *   BOOTSTRAP_FULL_NOCLI      = 3
 *   BOOTSTRAP_DB_ONLY         = 4
 *   BOOTSTRAP_FULL_NO_ADMIN_CHECK = 5
 */
enum BootstrapLevel: int
{
    /** Do not include config.php at all. */
    case None = 0;

    /** Set CLI_SCRIPT, ABORT_AFTER_CONFIG and include config.php. */
    case Config = 1;

    /** Set CLI_SCRIPT and include config.php (standard full bootstrap). */
    case Full = 2;

    /** No CLI_SCRIPT — include config.php as if running in a browser context. */
    case FullNoCli = 3;

    /** Load only the minimum needed to connect to the database. */
    case DbOnly = 4;

    /** Full bootstrap but skip the admin-user login step. */
    case FullNoAdminCheck = 5;
}
