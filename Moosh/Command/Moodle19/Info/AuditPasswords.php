<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle19\Info;

use Moosh\MooshCommand;

class AuditPasswords extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('audit-passwords');
        $this->addOption('r|reveal', 'Reveal cracked passwords.');
    }

    public function execute()
    {
        global $CFG;

        require_once($this->mooshDir . '/includes/passwords.php');
        require_once($CFG->libdir . '/authlib.php');
        require_once($CFG->libdir . '/moodlelib.php');
        $options = $this->expandedOptions;

        $users = get_records('user', 'deleted', 0);
        foreach ($users as $user) {
            if($user->username == 'guest') {
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
