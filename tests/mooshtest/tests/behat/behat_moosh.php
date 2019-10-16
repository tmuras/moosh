<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Behat steps definitions for moosh.
 *
 * @package   moosh
 * @category  test
 * @copyright 2019 Tomasz Muras
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;
use Behat\Mink\Exception\ExpectationException;
use core_analytics\course;

/**
 * moosh tests.
 *
 * @copyright 2019 Tomasz Muras
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_moosh extends behat_base
{



    /**
     *
     * @Then /^a record in table "(?P<table>.+)" with "(?P<cell1>.+)" = "(?P<val1>.+)" and "(?P<cel2>.+)" = "(?P<val2>.+)" exist$/
     */
    public function moosh_course_with_parameter_exist($table, $cell1, $val1, $cell2, $val2){

        global $DB;
        if(!($DB->record_exists($table, array($cell1 => $val1, $cell2=> $val2)))){
            throw new ExpectationException("Failure! record with parameter $cell1 = $val1 and $cell2 = $val2 does not exist", $this->getSession());
        }
    }

    /**
     *
     * @Then /^moosh command "(?P<command>.+)" print out id "(?P<match>.+)"$/
     */
    public function moosh_command_return_id($command, $match)
    {
        $id = $this->modified_command($match);
        $output = null;
        $ret = null;
        $output = exec("php /var/www/html/moosh/moosh.php $command", $output, $ret);
        if($output==$id){
            echo "***moosh command output***\nId - ". $id . "\n***\n";;
        }else{
            throw new ExpectationException("Failure! Id $id does not match $output", $this->getSession());
        }
    }
    /**
     *
     * @Then /^there are "(\d+)" "(?P<shortname>.+)" record added to database$/
     */
    public function moosh_command_cout_how_many_are_added($value, $shortname)
    {
        global $DB;
        $shortname.='%';
        $sql= "SELECT COUNT(id)
                FROM {course}
                WHERE shortname LIKE ?";
        $course_count = $DB->count_records_sql($sql, array($shortname));
        if($course_count==$value) {
            echo "$shortname moosh command output\nNumber of added courses ". $value . "\n***\n";
        }else{
            throw new ExpectationException("Failure! $course_count the number of rows created does not match $value.a the number added to the database", $this->getSession());
        }
    }

    /**
     *
     * @Then /^there are "(\d+)" "(?P<shortname>.+)" category added to database$/
     */
    public function moosh_command_cout_how_many_are_added_category($value, $shortname)
    {
        global $DB;
        $shortname.='%';
        $sql= "SELECT COUNT(id)
                FROM {course_categories}
                WHERE name LIKE ?";
        $course_count = $DB->count_records_sql($sql, array($shortname));
        if($course_count==$value) {
            echo "$shortname moosh command output\nNumber of added courses ". $value . "\n***\n";
        }else{
            throw new ExpectationException("Failure! $course_count the number of rows created does not match $value.a the number added to the database", $this->getSession());
        }
    }

    /**
     *
     * @When /^I run moosh "(?P<command>.+)"$/
     */
    public function moosh_command_run($command)
    {
        $command = $this->modified_command($command);
        $output = null;
        $ret = null;
        exec("php /var/www/html/moosh/moosh.php $command", $output, $ret);
        //$this->log_moosh_output($output);
    }
    /**
     *
     * @Then /^moosh command "(?P<command>.+)" contains "(?P<match>.+)"$/
     */
    public function moosh_command_returns($command, $match)
    {
        $command = $this->modified_command($command);
        $match = $this->modified_command($match);
        $output = null;
        $ret = null;
        exec("php /var/www/html/moosh/moosh.php $command", $output, $ret);
        $matched = false;
        foreach ($output as $line) {
            if (stristr($line, $match) !== false) {
                $matched = true;
                break;
            }
        }
        $this->log_moosh_output($output);
        if (!$matched) {
            throw new ExpectationException("Failure! Not found '$match' in the output for the command: '$command'", $this->getSession());
        }
    }
    /**
     *
     * @Then /^moosh command "(?P<command>.+)" does not contain "(?P<match>.+)"$/
     */
    public function moosh_command_does_not_contain($command, $match)
    {
        $command = $this->modified_command($command);
        $match = $this->modified_command($match);
        $output = null;
        $ret = null;
        exec("php /var/www/html/moosh/moosh.php $command", $output, $ret);
        $matched = false;
        foreach ($output as $line) {
            if (stristr($line, $match) !== false) {
                $matched = true;
                break;
            }
        }
        $this->log_moosh_output($output);
        if ($matched) {
            throw new ExpectationException("Failure! Found '$match' in the output for the command: '$command'", $this->getSession());
        }
    }
    /**
     * For the debugging purposes. Displayed text shows when there is a failure.
     * @param $output
     */
    private function log_moosh_output($output)
    {
        file_put_contents("/tmp/test.txt", implode("\n", $output));
        echo "***moosh command output***\n". implode("\n", $output) . "\n***\n";
    }

    /**
     * @param string $input
     * @return string $command
     */
    private function modified_command($input)
    {
        global $DB;
        if(strchr($input, "%")!==false) {

            $subcommand = explode('%', $input);
            $subcommand_length = count($subcommand);

            for ($i = 0; $i < $subcommand_length; $i++) {
                if (strpos($subcommand[$i], ":") !== false) {
                    $table_name = explode('.', $subcommand[$i]);
                    $table_cel = explode(':', $table_name[1]);
                    $table=$table_name[0];

                    $id = $DB->get_field($table, 'id', [$table_cel[0] => $table_cel[1]], MUST_EXIST);

                    $patern = '/%' . $subcommand[$i] . '%/';
                    $output = preg_replace($patern, $id, $input);
                    $input = $output;
                }
            }
            return $output;
        }else{
            $output=$input;
            return $output;
        }
    }


}