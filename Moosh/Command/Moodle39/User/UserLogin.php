<?php
/**
 * `moosh user-login`
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle29\User;

use Moosh\MooshCommand;

class UserLogin extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('login', 'user');

        $this->addArgument('username');
        $this->addOption('i|id', 'pass user id instead of username');

    }

    public function bootstrapLevel()
    {
        # set to no client. when CLI_SCRIPT is defined, moodle creates an empty session
        return self::$BOOTSTRAP_FULL_NOCLI;
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once("$CFG->libdir/datalib.php");

        $username = $this->arguments[0];
        $options = $this->expandedOptions;

        if ($options['id']) {
            $user = $DB->get_record('user', array('id' => $username), '*', MUST_EXIST);
        } else {
            $user = $DB->get_record('user', array('username' => $username), '*', MUST_EXIST);
        }

        $auth = empty($user->auth) ? 'manual' : $user->auth;
        if ($auth == 'nologin' or !is_enabled_auth($auth)) {
            cli_error(sprintf("User authentication is either 'nologin' or disabled. Check Moodle authentication method for '%s'", $user->username));
        }
        $authplugin = get_auth_plugin($auth);
        $authplugin->sync_roles($user);
        login_attempt_valid($user);
        complete_user_login($user);
        printf("%s:%s\n", session_name(), session_id());
    }
}
