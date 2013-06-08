<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Sql;
use Moosh\MooshCommand;

class SqlRun extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('run', 'sql');

        $this->addArgument('sql');

    }

    public function execute()
    {
        global $CFG, $DB;

        $sql = trim($this->arguments[0]);
        if(stripos($sql,'select') === 0) {
            //SELECT type query
            $results =  $DB->get_records_sql($sql);
            $n=0;
            foreach($results as $result) {
                $n++;
                echo "\nRecord $n\n";
                print_r($result);
            }
        } elseif(stripos($sql,'update') === 0 || stripos($sql,'insert') === 0) {
            //UPDATE or INSERT type query
            echo $DB->execute($sql) . "\n";
        } else {
            cli_error("I don't know how to handle this query");
        }
    }
}
