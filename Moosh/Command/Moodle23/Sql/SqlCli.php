<?php
/**
 * moosh - Moodle Shell
 *
 * @author     2014 Joby Harding
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Sql;
use Moosh\MooshCommand;

class SqlCli extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('cli', 'sql');

    }

    public function execute()
    {

        global $CFG;

        $connstr = '';

        if ($CFG->dbtype === 'mysqli') {
            $conn = "mysql -u {$CFG->dbuser} -h '{$CFG->dbhost}' -p'{$CFG->dbpass}' {$CFG->dbname}";
        }

        switch ($CFG->dbtype) {
            case 'mysqli':
                // TODO hide password rather than passing on command-line (see drush equivalent).
                $connstr = "mysql -h {$CFG->dbhost} -u {$CFG->dbuser} -p{$CFG->dbpass} {$CFG->dbname}";
                break;
            case 'pgsql':
                $portoption = '';
                if (!empty($CFG->dboptions['dbport'])) {
                    $portoption = '-p ' . $CFG->dboptions['dbport'];
                }
                putenv("PGPASSWORD={$CFG->dbpass}");
                $connstr = "psql -h {$CFG->dbhost} -U {$CFG->dbuser} {$portoption} {$CFG->dbname}";
                break;
            default:
                cli_error("Sorry, database type '$CFG->dbtype' is not supported yet.  Feel free to contribute!");
                break;
        }

        // TODO add?
        // if verbose mode was requested, show some more information/debug messages
        // if ($this->verbose) {
        //     echo "Say what you're doing now";
        // }

        $process = proc_open($conn, array(0 => STDIN, 1 => STDOUT, 2 => STDERR), $pipes);
        $proc_status = proc_get_status($process);
        $exit_code = proc_close($process);

        return ($proc_status["running"] ? $exit_code : $proc_status["exitcode"] );
    }

}
