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
     * @Then /^course with "(?P<shortname>.+)" = "(?P<course>.+)" and "(?P<para>.+)" = "(?P<para_val>.+)" exist$/
     */
    public function moosh_course_with_parameter_exist($shortname, $course, $para, $para_val){

        global $DB;

        if(!($DB->record_exists('course', array($shortname => $course, $para => $para_val)))){
            throw new ExpectationException("Failure! $shortname, $course, $para, $para_val", $this->getSession());
        }
    }

    /**
     *
     * @Then /^moosh command "(?P<command>.+)" print out id "(?P<match>.+)"$/
     */
    public function moosh_command_return_id($command, $match)
    {
        $id = $this->explode_id_command($match);
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
        $output = null;
        $ret = null;
        exec("php /var/www/html/moosh/moosh.php $command", $output, $ret);
    }
    /**
     *
     * @Then /^moosh command "(?P<command>.+)" contains "(?P<match>.+)"$/
     */
    public function moosh_command_returns($command, $match)
    {
        $command = $this->explode_id_command($command);
        $match = $this->explode_id_command($match);
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
        $command = $this->explode_id_command($command);
        $match = $this->explode_id_command($match);
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
    private function explode_id_command($output)
    {
        global $DB;
        if(strchr($output, "%")!==False) {
            $subcommand = explode('%', $output);
            $tab_var = explode(':', $subcommand[1]);
            $command_id = $DB->get_field('course', 'id', [$tab_var[0] => $tab_var[1]], MUST_EXIST);
            $pattern = '/(%)(\w+)(:)(\w+)(%)/';
            $returned_command = preg_replace($pattern, $command_id, $output);
            return $returned_command;
        }else{
            return $output;
        }
    }
}
