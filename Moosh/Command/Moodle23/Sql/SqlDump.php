<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Sql;
use Moosh\MooshCommand;

//TODO: add compression option (mysql: (g)zip, postgres: -C option)
class SqlDump extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('dump', 'sql');
    }

    public function execute()
    {
        global $CFG, $DB;

        switch ($CFG->dbtype) {
            case 'mysqli':
                passthru("mysqldump -h {$CFG->dbhost} -u {$CFG->dbuser} -p{$CFG->dbpass} {$CFG->dbname}");
                break;
            case 'pgsql':
                $portoption = '';
                if (!empty($CFG->dboptions['dbport'])) {
                    $portoption = '-p ' . $CFG->dboptions['dbport'];
                }
                putenv('PGPASSWORD='.$CFG->dbpass);
                passthru("pg_dump -h $CFG->dbhost -U $CFG->dbuser $portoption $CFG->dbname");
                break;
            default:
                cli_error("Sorry, database type '$CFG->dbtype' is not supported yet.  Feel free to contribute!");
                break;
        }
    }
}
