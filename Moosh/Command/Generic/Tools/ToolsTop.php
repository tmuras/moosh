<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Kacper Golewski <k.golewski@gmail.com>
 */


namespace Moosh\Command\Generic\Tools;
use Moosh\MooshCommand;

define('NEW_LINE', "\n");

class ToolsTop extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('top');

    }

    public function execute()
    {   
        global $DB;
        exec("tput lines", $lines);
        exec("tput cols", $cols);   
        
        $all_actions = $DB->get_records('log');

        if ($lines[0] <= count($all_actions)) {
            $actions = array_slice($all_actions, 0, $lines[0]-3);
        }
        else {
            $actions = $all_actions;
        }

        $user_id_header     = "User ID";
        $user_name_header   = "User name";
        $last_action_header = "Last action";

        $user_id_header_len     = strlen($user_id_header);
        $user_name_header_len   = strlen($user_name_header);
        $last_action_header_len = strlen($last_action_header); 

        $output             = array();

        $user_id_len        = $user_id_header_len;
        $user_name_len      = $user_name_header_len;
        $last_action_len    = $last_action_header_len;

        foreach ($actions as $action) {

            if (!isset($action->userid)) {
                continue;
            }

            $user_id        = $action->userid;
            $user           = $DB->get_record('user', array("id" => $user_id));
            if ($user) {
                $user_name  = $user->username;
            } else {
                continue;
            }
            $last_action    = date('h:m:s A, D - d M Y', $action->time);

            if (strlen($user_id) > $user_id_len) {
                $user_id_len = strlen($user_id);
            }
            if (strlen($user_name) > $user_name_len) {
                $user_name_len = strlen($user_name);
            }
            if (strlen($last_action) > $last_action_len) {
                $last_action_len = strlen($last_action);
            }
            $output[] = array($user_id, $user_name, $last_action);
        }
        
        $longest_line = $user_id_len + $user_name_len + $last_action_len + 2;

        $header = str_pad($user_id_header, $user_id_len+1) . 
                  str_pad($user_name_header, $user_name_len+1) .
                  $last_action_header . NEW_LINE; 
        
        echo $header;
        echo str_repeat("=", $longest_line) . "\n";

        foreach ($output as $line_raw) {
            $line = str_pad($line_raw[0], $user_id_len+1) .
                    str_pad($line_raw[1], $user_name_len+1) .
                    $line_raw[2] . NEW_LINE;
            echo $line;
        }
    }
}
