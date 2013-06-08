<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Sql;
use Moosh\MooshCommand;

class SqlDump extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('dump', 'sql');
    }

    public function execute()
    {
        global $CFG, $DB;

        if($CFG->dbtype != 'mysqli') {
          cli_error('Only MySQL is currently supported');
        }

        passthru("mysqldump -h {$CFG->dbhost} -u {$CFG->dbuser} -p{$CFG->dbpass} {$CFG->dbname}");
    }
}
