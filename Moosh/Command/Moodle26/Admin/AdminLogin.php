<?php
/**
 * `moosh admin-login`
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle26\Admin;
use Moosh\MooshCommand;

class AdminLogin extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('login', 'admin');
        $this->maxArguments = 2;
    }

    public function bootstrapLevel() {
      # set to no client. when CLI_SCRIPT is defined, moodle creates an empty session
      return self::$BOOTSTRAP_FULL_NOCLI;
    }

    public function execute() {
        global $CFG;

        require_once("$CFG->libdir/datalib.php");
        $user = get_admin();
        if (!$user) {
          cli_error("Unable to find admin user in DB.");
        }
        $auth = empty($user->auth) ? 'manual' : $user->auth;
        if ($auth=='nologin' or !is_enabled_auth($auth)) {
          cli_error(sprintf("User authentication is either 'nologin' or disabled. Check Moodle authentication method for '%s'", $user->username));
        }
        $authplugin = get_auth_plugin($auth);
        $authplugin->sync_roles($user);
        login_attempt_valid($user);
        complete_user_login($user);
        printf("%s:%s\n", session_name(), session_id());
    }
}
