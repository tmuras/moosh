<?php
/**
 * moosh - Moodle Shell
 *
 * @auhtor  2021 Céline Pervès <cperves@unistra.fr>
 * @copyright Université de Strasbourg unistra.fr
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Generic\Request;

use Moosh\MooshCommand;

class RequestSelect extends MooshCommand {
    public function __construct() {
        parent::__construct('select', 'request');
        $this->addArgument('select_query');
        $this->minArguments = 1;
        $this->maxArguments = 1;
    }

    protected function getArgumentsHelp() {
        $help = parent::getArgumentsHelp();
        $help .= "\n\n";
        $help .= "This command enable to make a select query and return resultset into a csv format";
        return $help;
    }


    public function execute() {
        global $CFG, $DB;
        $selectquery = trim($this->arguments[0]);
        cli_writeln("$selectquery");
        $results = $DB->get_records_sql($selectquery);
        $output = '';
        foreach($results as $index=>$result) {
            $output.=implode(';',(array) $result)."\n";
        }
        echo $output;
    }
}


