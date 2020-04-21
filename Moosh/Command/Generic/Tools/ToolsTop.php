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

class ToolsTop extends MooshCommand {
    public function __construct() {
        parent::__construct('top');

    }

    public function bootstrapLevel() {
        return MooshCommand::$BOOTSTRAP_DB_ONLY;
    }

    public function execute() {
        global $DB;
        exec("tput lines", $lines);
        exec("tput cols", $cols);
        $rows_to_render = (int) $lines[0] - 3;
        $actions = $DB->get_records('logstore_standard_log', null, 'id DESC', '*', 0, $rows_to_render);

        $user_id_header = "User ID";
        $user_name_header = "User name";
        $last_action_header = "Last action";
        $event_header = "Event";

        $user_id_header_len = strlen($user_id_header);
        $user_name_header_len = strlen($user_name_header);
        $last_action_header_len = strlen($last_action_header);
        $event_header_len = strlen($event_header);

        $output = array();

        $user_id_len = $user_id_header_len;
        $user_name_len = $user_name_header_len;
        $last_action_len = $last_action_header_len;
        $event_len = $event_header_len;

        $users = [];

        foreach ($actions as $action) {

            if (!isset($action->userid)) {
                continue;
            }

            $user_id = $action->userid;
            $user = null;

            if (!isset($users[$user_id])) {
                $user = $DB->get_record('user', array("id" => $user_id));
                $users[$user_id] = $user;
            } else {
                $user = $users[$user_id];
            }

            if ($user) {
                $user_name = $user->username;
            } else {
                continue;
            }
            $last_action = date('h:m:s A', $action->timecreated);
            $event = $action->eventname;

            if (strlen($user_id) > $user_id_len) {
                $user_id_len = strlen($user_id);
            }
            if (strlen($user_name) > $user_name_len) {
                $user_name_len = strlen($user_name);
            }
            if (strlen($last_action) > $last_action_len) {
                $last_action_len = strlen($last_action);
            }
            if (strlen($event) > $event_len) {
                $event_len = strlen($event);
            }
            $output[] = array($user_id, $user_name, $last_action, $event);
        }

        $longest_line = $user_id_len + $user_name_len + $last_action_len + $event_len + 2;

        $header = str_pad($user_id_header, $user_id_len + 1) .
                str_pad($user_name_header, $user_name_len + 1) .
                str_pad($last_action_header, $last_action_len + 1) .
                $event_header . PHP_EOL;

        echo $header;
        echo str_repeat("=", $longest_line) . "\n";

        foreach ($output as $line_raw) {
            $line = str_pad($line_raw[0], $user_id_len + 1) .
                    str_pad($line_raw[1], $user_name_len + 1) .
                    str_pad($line_raw[2], $last_action_len + 1) .
                    $line_raw[3] . PHP_EOL;
            echo $line;
        }
    }
}
