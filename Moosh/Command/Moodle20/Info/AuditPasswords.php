<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle20\Info;

use Moosh\MooshCommand;

class AuditPasswords extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('audit-passwords');
        $this->addOption('r|reveal', 'Reveal cracked passwords.');
        $this->addOption('u|userid:', 'Only check this user id.');
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once($this->mooshDir . '/includes/passwords.php');
        require_once($CFG->libdir . '/authlib.php');
        require_once($CFG->libdir . '/moodlelib.php');
        $options = $this->expandedOptions;

        if ($options['userid']) {
            $users = $DB->get_records('user', array('id' => $options['userid']));
        } else {
            $users = $DB->get_records('user', array('deleted' => 0));
        }
        foreach ($users as $user) {
            if ($user->username == 'guest') {
                continue;
            }
            foreach ($passwords as $password) {
                if (validate_internal_user_password($user, $password)) {
                    echo "User with known (easily crackable) password: " . $user->username;
                    if ($options['reveal']) {
                        echo " / $password";
                    }
                    echo "\n";
                    continue 2;
                }
            }
        }
    }

}