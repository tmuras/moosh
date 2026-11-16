<?php
/**
 * Delete user by username.
 * moosh user-delete      [<username1> <username2> ...]
 *
 * @copyright  2013 onwards Matteo Mosangini
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\User;
use Moosh\MooshCommand;

class UserDelete extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('delete', 'user');

        $this->addOption('i|id', 'use numerical id instead of username');
        $this->addArgument('user');
        $this->maxArguments = 255;

    }

    public function execute()
    {
        global $CFG, $DB;
        require_once $CFG->dirroot . '/user/lib.php';

        $options = $this->expandedOptions;
      
        if (!$options['id']) {
            // Check all usernames in a single query before deleting anything.
            list($insql, $params) = $DB->get_in_or_equal($this->arguments, SQL_PARAMS_NAMED);
            $ambiguous = $DB->get_records_sql(
                "SELECT username, COUNT(*) AS cnt FROM {user} WHERE username $insql GROUP BY username HAVING COUNT(*) > 1",
                $params
            );
            if ($ambiguous) {
                $list = array();
                foreach ($ambiguous as $row) {
                    $list[] = "'{$row->username}' ({$row->cnt} records)";
                }
                cli_error("Ambiguous username(s): " . implode(', ', $list)
                    . ". The same username exists for multiple users (e.g. different authentication/mnet hosts). "
                    . "Nothing was deleted. Use the --id option with numerical user ids instead.");
            }
        }

        foreach ($this->arguments as $argument) {
            
            if ($options['id']) {
                $user = $DB->get_record('user', array('id' => $argument));
            } else {
                $user = $DB->get_record('user', array('username' => $argument));
            }

            if ($user instanceof \stdClass) {
                user_delete_user($user);
            } else {
                print "User not found\n";
            }
        }
    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_FULL_NO_ADMIN_CHECK;
    }
}
