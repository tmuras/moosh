<?php
/**
 * moosh - Moodle Shell
 *
 * List users.
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\User;

use Moosh\MooshCommand;
use context_system;

class UserList extends MooshCommand {
    public function __construct() {
        parent::__construct('list', 'user');

        $this->addOption('n|limit:', 'display max n users');
        $this->addOption('i|id', 'display id only column');
        $this->addOption('s|sort:', 'sort by (username, email or idnumber)');
        $this->addOption('d|descending', 'sort in descending order');
        $this->addOption('course-inactive', 'limit to users who never accessed course provided with --course.');
        $this->addOption('course-role:', 'limit to users with given role in a --course.');
        $this->addOption('course:', 'select all enrolled in given course id');

        $this->addArgument('sql expression');
        $this->minArguments = 0;
    }

    public function execute() {
        global $DB, $CFG;

        require_once $CFG->dirroot . '/lib/accesslib.php';
        require_once($CFG->dirroot . '/enrol/locallib.php');
        require_once($CFG->dirroot . '/group/lib.php');

        @error_reporting(E_ALL | E_STRICT);
        @ini_set('display_errors', '1');

        $options = $this->expandedOptions;
        $limit_from = 0;

        if (($options['course-inactive'] || $options['course-role']) && !$options['course']) {
            cli_error("You must provide --course if --course-inactive or --course-role is used.");
        }

        if (count($this->arguments) == 0) {
            $users = ("SELECT * FROM {user}");
            $limit_to = 100;
        } else {
            $users = ("SELECT * FROM {user} WHERE " . $this->arguments[0]);
            $limit_to = 0;
        }

        $sort = "id";
        if ($options['sort']) {
            if ($options['sort'] == 'username' || $options['sort'] == 'email' || $options['sort'] == 'idnumber') {
                $sort = $options['sort'];
            } else {
                echo "Invalid sorting option. Use 'username', 'email' or 'idnumber'.\n";
            }
        }

        $dir = 'ASC';
        if ($options['descending']) {
            $dir = 'DESC';
        }

        $users .= " ORDER BY $sort $dir";

        if ($options['limit'] && preg_match('/^\d+$/', $options['limit'])) {
            $limit_to = $options['limit'];
        }

        if($options['course']) {
            $users = $DB->get_records_sql($users, array('deleted'=>0, 'suspended'=>0));
        } else {
            $users = $DB->get_records_sql($users, $params = null, $limit_from, $limit_to);
        }

        // Possibly extra filtering.
        $extralimit = false;
        if ($options['course']) {
            $context = \context_course::instance($options['course']);
            $enrolledusers = get_enrolled_users($context);
            $extralimit = array_combine(array_keys($enrolledusers), array_keys($enrolledusers));
        }

        if ($options['course-inactive']) {
            foreach ($extralimit as $userid => $v) {
                $lastaccessexists = $DB->record_exists('user_lastaccess', array('courseid' => $options['course'], 'userid' => $userid));
                if ($lastaccessexists) {
                    unset($extralimit[$userid]);
                }
            }
        }

        if ($options['course-role']) {
            $role = $DB->get_record('role', array('shortname' => $options['course-role']), '*', MUST_EXIST);
            $roleusers = array_keys(get_role_users($role->id, $context));
            $roleusers = array_combine($roleusers,$roleusers);
            $extralimit = array_intersect_key($roleusers,$extralimit);
        }

        if($extralimit !== false) {
            $users = array_intersect_key($users,$extralimit);
        }

        // Apply original $options['limit'] at the end.
        if($options['limit']) {
            $users = array_splice($users,0,$options['limit']);
        }
        
        foreach ($users as $user) {
            if ($options['id']) {
                echo "$user->id \n";
                continue;
            }
            $to_print = $user->username . " ({$user->id}), " . $user->email . ", ";
            echo $to_print . "\n";
        }
    }
}
