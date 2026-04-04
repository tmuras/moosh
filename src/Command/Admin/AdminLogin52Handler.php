<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Admin;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * admin:login implementation for Moodle 5.1.
 */
class AdminLogin52Handler extends BaseHandler
{
    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/datalib.php';

        // Get admin user.
        $verbose->step('Looking up admin user');
        $user = get_admin();
        if (!$user) {
            $output->writeln('<error>Unable to find admin user in database.</error>');
            return Command::FAILURE;
        }

        $verbose->detail('Admin user', $user->username . ' (ID: ' . $user->id . ')');

        // Validate authentication method.
        $auth = empty($user->auth) ? 'manual' : $user->auth;
        if ($auth === 'nologin' || !is_enabled_auth($auth)) {
            $output->writeln(sprintf(
                '<error>User authentication is either "nologin" or disabled. Check Moodle authentication method for "%s".</error>',
                $user->username,
            ));
            return Command::FAILURE;
        }

        $verbose->detail('Auth method', $auth);

        // Perform login.
        $verbose->step('Authenticating admin user');
        $authPlugin = get_auth_plugin($auth);
        $authPlugin->sync_roles($user);
        login_attempt_valid($user);
        complete_user_login($user);

        $verbose->done('Login successful');

        $sessionName = session_name();
        $sessionId = session_id();

        // Default output: simple cookie format for scripting.
        if ($format === 'table') {
            $output->writeln("$sessionName:$sessionId");
        } else {
            $formatter = new ResultFormatter($output, $format);
            $formatter->display(
                ['cookie_name', 'cookie_value'],
                [[$sessionName, $sessionId]],
            );
        }

        return Command::SUCCESS;
    }
}
