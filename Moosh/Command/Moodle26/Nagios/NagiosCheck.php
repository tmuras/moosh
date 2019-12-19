<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle26\Nagios;
use Moosh\MooshCommand;

class NagiosCheck extends MooshCommand {
    public function __construct() {
        parent::__construct('check', 'nagios');
    }

    public function bootstrapLevel() {
        return self::$BOOTSTRAP_FULL_NOCLI;
    }

    public function execute() {
        global $CFG;

        if (!function_exists('curl_version')) {
            die("PHP cURL not installed\n");
        }

        /** Ugly hack to get this working. Forking doesn't work here
         * because moodle can't log entries from forked process into db.
         * We need this run in external process because apache doesn't can't
         * give usvalid cookie before moosh process ends.
         */
        $loginasadmin = run_external_command("moosh admin-login", "admin-login failed");

        $target = $CFG->wwwroot . '/admin/index.php';
        $credentials = explode(":", $loginasadmin[0]);
        $cookiename = $credentials[0];
        $cookie = $credentials[1];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_COOKIE, sprintf('%s=%s', $cookiename, $cookie));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $target);
        curl_setopt($ch, CURLOPT_HEADER, true);

        curl_getinfo($ch);
        $result = curl_exec($ch);
        curl_close($ch);

        if (strpos($result, "You are logged in as")) {
            echo "Moodle instance is running.";
            return 0;
        } else {
            echo "Moodle instance unreachable.";
            return 2;
        }

    }
}
