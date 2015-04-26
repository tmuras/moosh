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
        global $CFG, $DB;

        require_once("$CFG->libdir/authlib.php");
        require_once("$CFG->dirroot/login/lib.php");
        $user = get_complete_user_data('username', $CFG->admin, $CFG->mnet_localhost_id);
        if (!$user) {
          cli_error(sprintf("Unable to find admin user in DB. Check config.php for correct \$CFG->admin. Current value is: '%s'", $CFG->admin));
        }
        $auth = empty($user->auth) ? 'manual' : $user->auth;
        if ($auth=='nologin' or !is_enabled_auth($auth)) {
          cli_error(sprintf("User authentication is either 'nologin' or disabled. Check config.php for correct \$CFG->admin or change in Moodle authentication method for '%s'", $user->username));
        }
        $authplugin = get_auth_plugin($auth);
        $authplugin->sync_roles($user);
        login_attempt_valid($user);
        complete_user_login($user);
        printf("%s:%s\n", session_name(), session_id());
    }
}
