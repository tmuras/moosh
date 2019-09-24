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
     * @Then /^a record in table "(?P<wtable>.+)" with "(?P<rec1>.+)" = "(?P<val1>.+)" and "(?P<para>.+)" = "(?P<para_val>.+)" exist$/
     */
    public function moosh_course_with_parameter_exist($wtable, $rec1, $val1, $para, $para_val){

        global $DB;
        if(!($DB->record_exists($wtable, array($rec1 => $val1, $para => $para_val)))){
            throw new ExpectationException("Failure! $rec1, $val1, $para, $para_val", $this->getSession());
        }
    }

    /**
     *
     * @Then /^moosh command "(?P<command>.+)" print out id "(?P<match>.+)"$/
     */
    public function moosh_command_return_id($command, $match)
    {
        $id = $this->modified_match($match);
        $output = null;
        $ret = null;
        $output = exec("php /var/www/html/moosh/moosh.php $command", $output, $ret);
        if($output==$id){
            echo "***moosh command output***\nId from created course ". $id . "\n***\n";;
        }else{
            throw new ExpectationException("Failure! Id $id does not match $output", $this->getSession());
        }
    }
    /**
     *
     * @Then /^there are "(\d+)" "(?P<shortname>.+)" courses added to database$/
     */
    public function moosh_command_cout_how_many_are_added($val, $shortname)
    {
        global $DB;
        $shortname.='%';
        $sql= "SELECT COUNT(id)
                FROM {course}
                WHERE shortname LIKE ?";
        $coursecount = $DB->count_records_sql($sql, array($shortname));
        if($coursecount==$val) {
            echo "$shortname moosh command output\nNumber of added courses ". $val . "\n***\n";
        }else{
            throw new ExpectationException("Failure! $coursecount the number of rows created does not match $val.a the number added to the database", $this->getSession());
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
        $match = $this->modified_match($match);
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
        $match = $this->modified_match($match);
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

            if ((strpos($subcommand[0], "--course ")) !== false) {
                $table[] = 'course';
            } else{
                $table = explode('-', $subcommand[0]);
            }

            for ($i = 1; $i < $subcommand_length; $i++) {
                if(strchr($subcommand[$i], ":")!==false) {
                    $table_cel = explode(':', $subcommand[$i]);

                    $id = $DB->get_field($table[0], 'id', [$table_cel[0] => $table_cel[1]], MUST_EXIST);

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

    /**
     *
     * @param string $input
     * @return string $command
     */
    private function modified_match($input)
    {
        global $DB;
        if(strchr($input, "%")!==false) {
            $submatch = explode('%', $input);
            $tablematch = explode(':', $submatch[1]);
            $courseid = $DB->get_field('course', 'id', array($tablematch[0] => $tablematch[1]), MUST_EXIST);
            $pattern = '/(%)(\w+)(:)(\w+)(%)/';
            $command = preg_replace($pattern, $courseid, $input);
            return $command;
        }else{
            $command = $input;
            return $command;
        }
    }
}