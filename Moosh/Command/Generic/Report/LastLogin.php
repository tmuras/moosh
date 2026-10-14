<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2026 Enovation Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Generic\Report;

use Moosh\MooshCommand;


/**
 * Class LastLogin
 *
 * Datetime of the last user login
 *
 * @package Moosh\Command\Generic\Report
 */
class LastLogin extends MooshCommand
{

    public function __construct() {
        parent::__construct('login', 'lastuser');
    }

    public function bootstrapLevel() {
        return self::$BOOTSTRAP_DB_ONLY;
    }

    public function execute() {
        global $DB, $CFG;
        $lastlogin = $DB->get_record_sql('SELECT MAX(lastaccess) AS lastaccess FROM {user}');
        if ($lastlogin && !empty($lastlogin->lastaccess)) {
            printf("%d", $lastlogin->lastaccess);
        } else {
            printf("%d", -1);
        }
    }
}
